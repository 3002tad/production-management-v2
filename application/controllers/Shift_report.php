<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift_report extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Shift_report_model');
        $this->load->helper('url');
        // Basic access control: allow leader, admin and worker roles
        $role = $this->session->userdata('role');
        if (empty($role) || !in_array($role, ['leader','admin','worker'])) {
            // Not logged in or not authorized for this controller
            // If AJAX request, return JSON 401 instead of redirect so frontend can handle it
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json', true, 401);
                echo json_encode(['error' => 'unauthorized']);
                exit;
            }
            redirect('login');
            return;
        }
    }

    // List running shifts
    public function index()
    {
    $data['shifts'] = $this->Shift_report_model->get_running_shifts();
    $data['content'] = 'leader/shift_report/shift_list';
    $data['navlink'] = 'shift_report';
    $this->load->view('leader/vbackend', $data);
    }

    // View report for a shift (leader view)
    public function view($shift_id)
    {
        // Permission: if user is worker, only allow if assigned to this shift
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        if ($role === 'worker' && !$this->Shift_report_model->user_has_access($shift_id, $user_id)) {
            $this->session->set_flashdata('error', 'Bạn không có quyền truy cập ca này');
            redirect('leader/shift-report');
            return;
        }

        // Always load the detail view; let the view show "Chưa có dữ liệu cho ca này" when report empty
        $report = $this->Shift_report_model->get_shift_report($shift_id);
        $data['shift_id'] = $shift_id;
        $data['report'] = $report; // may be empty array
        // Prefetch recent events per machine to avoid AJAX/auth issues
        $events_map = [];
        if (!empty($report) && is_array($report)) {
            foreach ($report as $r) {
                $mid = isset($r['machine_id']) ? intval($r['machine_id']) : null;
                if ($mid !== null) {
                    $events_map[$mid] = $this->Shift_report_model->get_machine_events($shift_id, $mid, 20);
                }
            }
        }
        $data['events_map'] = $events_map;
        $data['content'] = 'leader/shift_report/shift_report';
        $data['navlink'] = 'shift_report';
        $this->load->view('leader/vbackend', $data);
    }

    // View specific machine report for a shift (worker view)
    public function view_machine($shift_id, $machine_id)
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        // If worker, verify access to this shift/machine
        if ($role === 'worker' && !$this->Shift_report_model->user_has_access($shift_id, $user_id, $machine_id)) {
            $this->session->set_flashdata('error', 'Bạn không có quyền truy cập ca này');
            redirect('leader/shift-report');
            return;
        }

        // Always load worker detail view; view will show message if no data
        $row = $this->Shift_report_model->get_machine_report($shift_id, $machine_id);
        $data['row'] = $row; // may be null/empty
        // Prefetch recent events for this machine so view can render without AJAX
        try {
            $data['events'] = $this->Shift_report_model->get_machine_events($shift_id, $machine_id, 50);
        } catch (Exception $ex) {
            // on error, log and provide empty array so view can show appropriate message
            log_message('error', 'Shift_report::view_machine get_machine_events error: ' . $ex->getMessage());
            $data['events'] = [];
        }
        $data['content'] = 'leader/shift_report/shift_report_worker';
        $data['navlink'] = 'shift_report';
        $this->load->view('leader/vbackend', $data);
    }

    // API endpoints (simple router)
    public function api_get_shift_report($shift_id)
    {
        $report = $this->Shift_report_model->get_shift_report($shift_id);
        header('Content-Type: application/json');
        echo json_encode($report);
    }

    public function api_get_machine_report($shift_id, $machine_id)
    {
        $row = $this->Shift_report_model->get_machine_report($shift_id, $machine_id);
        header('Content-Type: application/json');
        echo json_encode($row);
    }

    // API to fetch recent events for a machine in a shift
    public function api_get_machine_events($shift_id, $machine_id)
    {
        try {
            $events = $this->Shift_report_model->get_machine_events($shift_id, $machine_id, 20);
            header('Content-Type: application/json');
            echo json_encode($events);
        } catch (Exception $ex) {
            // Log server error for debugging and return JSON error
            log_message('error', 'Shift_report::api_get_machine_events error: ' . $ex->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Server error when fetching events', 'message' => $ex->getMessage()]);
        }
    }

    // Debug endpoint: run direct query and return DB error, SQL and sample rows
    public function api_debug_events($shift_id = null, $machine_id = null)
    {
        header('Content-Type: application/json');
        if ($shift_id === null || $machine_id === null) {
            echo json_encode(['error' => 'missing parameters', 'usage' => '/leader/shift-report/api/debug_events/{shift_id}/{machine_id}']);
            return;
        }
        try {
            $sql = "SELECT id, shift_id, machine_id, event_type, detail, ts, created_by FROM shift_machine_events WHERE shift_id = ? AND machine_id = ? ORDER BY ts DESC LIMIT 50";
            $q = $this->db->query($sql, [(int)$shift_id, (int)$machine_id]);
            $rows = [];
            if ($q !== false) {
                $rows = $q->result_array();
            }
            $dberr = $this->db->error();
            echo json_encode(['sql' => $sql, 'params' => [(int)$shift_id, (int)$machine_id], 'db_error' => $dberr, 'count' => count($rows), 'rows' => $rows]);
        } catch (Exception $ex) {
            log_message('error', 'Shift_report::api_debug_events exception: ' . $ex->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'exception', 'message' => $ex->getMessage()]);
        }
    }

    // API to record downtime interval (POST: shift_id, machine_id, start_ts, end_ts)
    public function api_add_downtime()
    {
        $shift_id = $this->input->post('shift_id');
        $machine_id = $this->input->post('machine_id');
        $start_ts = $this->input->post('start_ts');
        $end_ts = $this->input->post('end_ts');
        $user_id = $this->session->userdata('user_id');

        $ok = $this->Shift_report_model->record_downtime($shift_id, $machine_id, $start_ts, $end_ts, $user_id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$ok]);
    }

    // API to record production (POST)
    public function api_record_production()
    {
        $shift_id = $this->input->post('shift_id');
        $machine_id = $this->input->post('machine_id');
        $delta = $this->input->post('delta_qty');
        $user_id = $this->session->userdata('user_id');
        $ok = $this->Shift_report_model->record_production($shift_id, $machine_id, $delta, $user_id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$ok]);
    }
}
