<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Machine Controller - Quản lý máy/dây chuyền sản xuất
 * 
 * Controller chuyên dụng cho Trưởng dây chuyền
 * Module: Leader/Machine
 * Xử lý các chức năng:
 * - Quản lý thông tin máy/dây chuyền (CRUD)
 * - Lập lịch bảo trì máy
 * - Theo dõi trạng thái và lịch sử thay đổi
 * - Tích hợp với hệ thống phân quyền RBAC
 * 
 * @author Copilot AI
 * @date 2025-11-27
 */
class Machine extends CI_Controller
{
    /**
     * Constructor - Kiểm tra phân quyền
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leader/MachineModel');
        $this->load->library('session');
        
        // Kiểm tra đăng nhập
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            exit();
        }

        // Lấy thông tin RBAC từ session
        $role_name = $this->session->userdata('role_name');
        $level = $this->session->userdata('level');
        
        // Kiểm tra quyền truy cập theo RBAC
        // Cho phép: BOD (level 100), System Admin (level 80), Line Manager (level 50+)
        $allowed_roles = ['bod', 'system_admin', 'line_manager'];
        $has_access = in_array($role_name, $allowed_roles) || ($level >= 50);
        
        if (!$has_access) {
            $this->session->set_flashdata('error', 'Không có quyền truy cập module quản lý máy. Yêu cầu level 50 trở lên.');
            redirect('leader/');
            exit();
        }

        // Xác định quyền chỉnh sửa (level >= 50 có full quyền CRUD)
        $this->can_edit = ($level >= 50);
        $this->can_manage_maintenance = ($level >= 50);
    }

    /**
     * Dashboard - Danh sách máy với filter và search
     */
    public function index()
    {
        // Clear any old error messages since user has access now
        $this->session->unset_userdata('error');
        
        // Lấy filter parameters
        $filters = [
            'search' => $this->input->get('search'),
            'status' => $this->input->get('status'),
            'stage_type' => $this->input->get('stage_type'),
            'capacity_min' => $this->input->get('capacity_min'),
            'capacity_max' => $this->input->get('capacity_max'),
        ];

        // Pagination
        $page = max(1, (int)$this->input->get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Lấy dữ liệu
        $result = $this->MachineModel->getMachines($filters, $limit, $offset);
        $statistics = $this->MachineModel->getStatistics();

        // Nhóm máy theo stage_type (Line) -> location (Khu) -> machines
        $grouped_machines = $this->groupMachinesByLineAndArea($result['machines']);

        $data = [
            'title' => 'Quản lý Máy/Dây chuyền',
            'machines' => $result['machines'],
            'grouped_machines' => $grouped_machines,
            'total' => $result['total'],
            'current_page' => $page,
            'total_pages' => ceil($result['total'] / $limit),
            'limit' => $limit,
            'filters' => $filters,
            'statistics' => $statistics,
            'can_edit' => $this->can_edit,
            'content' => 'leader/machine/index',
            'navlink' => 'machine',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * Form tạo máy mới
     */
    public function create()
    {
        if (!$this->can_edit) {
            $this->session->set_flashdata('error', 'Không có quyền tạo máy mới');
            redirect('machine/');
            return;
        }

        $data = [
            'title' => 'Thêm Máy/Dây chuyền Mới',
            'content' => 'leader/machine/create',
            'navlink' => 'machine',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * Xử lý tạo máy mới
     */
    public function store()
    {
        if (!$this->can_edit) {
            $this->jsonResponse(['error' => 'Không có quyền tạo máy'], 403);
            return;
        }

        try {
            $machine_data = [
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'capacity' => $this->input->post('capacity'),
                'stage_type' => $this->input->post('stage_type'),
                'status' => $this->input->post('status', 'active'),
                'description' => $this->input->post('description'),
                'location' => $this->input->post('location'),
                'purchase_date' => $this->input->post('purchase_date'),
                'warranty_until' => $this->input->post('warranty_until'),
                'created_by' => $this->session->userdata('username')
            ];

            $result = $this->MachineModel->createMachine($machine_data);

            if ($result['success']) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => $result['message'],
                    'machine_code' => $result['machine_code']
                ]));
                redirect('machine/');
            } else {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ]));
                redirect('machine/create');
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]));
            redirect('machine/create');
        }
    }

    /**
     * Xem chi tiết máy
     */
    public function detail($id)
    {
        $machine = $this->MachineModel->getMachineById($id);
        if (!$machine) {
            show_404();
            return;
        }

        // Lấy lịch sử trạng thái
        $status_logs = $this->MachineModel->getStatusLogs($id, 10);
        
        // Lấy lịch bảo trì
        $maintenance_schedules = $this->MachineModel->getMaintenanceSchedules($id, ['status' => ['planned', 'in_progress', 'completed']]);

        $data = [
            'title' => 'Chi tiết Máy: ' . $machine->code,
            'machine' => $machine,
            'status_logs' => $status_logs,
            'maintenance_schedules' => $maintenance_schedules,
            'can_edit' => $this->can_edit,
            'can_manage_maintenance' => $this->can_manage_maintenance,
            'content' => 'leader/machine/detail',
            'navlink' => 'machine',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * Form chỉnh sửa máy
     */
    public function edit($id)
    {
        if (!$this->can_edit) {
            $this->session->set_flashdata('error', 'Không có quyền chỉnh sửa máy');
            redirect('machine/');
            return;
        }

        $machine = $this->MachineModel->getMachineById($id);
        if (!$machine) {
            show_404();
            return;
        }

        $data = [
            'title' => 'Chỉnh sửa Máy: ' . $machine->code,
            'machine' => $machine,
            'content' => 'leader/machine/edit',
            'navlink' => 'machine',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * Xử lý cập nhật máy
     */
    public function update($id)
    {
        if (!$this->can_edit) {
            $this->jsonResponse(['error' => 'Không có quyền cập nhật máy'], 403);
            return;
        }

        try {
            $update_data = [
                'name' => $this->input->post('name'),
                'capacity' => $this->input->post('capacity'),
                'stage_type' => $this->input->post('stage_type'),
                'status' => $this->input->post('status'),
                'description' => $this->input->post('description'),
                'location' => $this->input->post('location'),
                'purchase_date' => $this->input->post('purchase_date'),
                'warranty_until' => $this->input->post('warranty_until'),
                'status_reason' => $this->input->post('status_reason'),
                'updated_by' => $this->session->userdata('username')
            ];

            $result = $this->MachineModel->updateMachine($id, $update_data);

            if ($result['success']) {
                $message = $result['message'];
                if ($result['status_changed']) {
                    $message .= ' (Trạng thái đã được thay đổi)';
                }

                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Cập nhật thành công!',
                    'message' => $message
                ]));
                redirect('machine/detail/' . $id);
            } else {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? [],
                    'suggestion' => $result['suggestion'] ?? null
                ]));
                redirect('machine/edit/' . $id);
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]));
            redirect('machine/edit/' . $id);
        }
    }

    /**
     * Quản lý lịch bảo trì cho máy
     */
    public function maintenance($machine_id)
    {
        if (!$this->can_manage_maintenance) {
            $this->session->set_flashdata('error', 'Không có quyền quản lý bảo trì');
            redirect('machine/');
            return;
        }

        $machine = $this->MachineModel->getMachineById($machine_id);
        if (!$machine) {
            show_404();
            return;
        }

        // Lấy filter
        $filters = [
            'status' => $this->input->get('status'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date'),
        ];

        $maintenance_schedules = $this->MachineModel->getMaintenanceSchedules($machine_id, $filters);

        $data = [
            'title' => 'Lịch Bảo trì - Máy: ' . $machine->code,
            'machine' => $machine,
            'maintenance_schedules' => $maintenance_schedules,
            'filters' => $filters,
            'content' => 'leader/machine/maintenance',
            'navlink' => 'machine',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * Tạo lịch bảo trì mới
     */
    public function createMaintenance($machine_id)
    {
        if (!$this->can_manage_maintenance) {
            $this->jsonResponse(['error' => 'Không có quyền tạo lịch bảo trì'], 403);
            return;
        }

        try {
            $maintenance_data = [
                'machine_id' => $machine_id,
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'start_time' => $this->input->post('start_time'),
                'end_time' => $this->input->post('end_time'),
                'maintenance_type' => $this->input->post('maintenance_type', 'preventive'),
                'status' => $this->input->post('status', 'planned'),
                'estimated_cost' => $this->input->post('estimated_cost', 0),
                'technician_name' => $this->input->post('technician_name'),
                'notes' => $this->input->post('notes'),
                'created_by' => $this->session->userdata('username')
            ];

            $result = $this->MachineModel->createMaintenance($maintenance_data);

            if ($result['success']) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => $result['message']
                ]));
            } else {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? [],
                    'conflicts' => $result['conflicts'] ?? []
                ]));
            }

            redirect('machine/maintenance/' . $machine_id);

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]));
            redirect('machine/maintenance/' . $machine_id);
        }
    }

    /**
     * API: Lấy máy khả dụng cho ca sản xuất
     */
    public function getAvailableMachines()
    {
        $stage_type = $this->input->get('stage_type');
        $start_time = $this->input->get('start_time');
        $end_time = $this->input->get('end_time');

        if (!$stage_type || !$start_time || !$end_time) {
            $this->jsonResponse([
                'error' => 'Missing required parameters: stage_type, start_time, end_time'
            ], 400);
            return;
        }

        $machines = $this->MachineModel->getAvailableMachines($stage_type, $start_time, $end_time);

        $this->jsonResponse([
            'success' => true,
            'machines' => $machines,
            'count' => count($machines)
        ]);
    }

    /**
     * API: Thống kê máy
     */
    public function getStatistics()
    {
        $statistics = $this->MachineModel->getStatistics();
        $this->jsonResponse([
            'success' => true,
            'statistics' => $statistics
        ]);
    }

    /**
     * Helper: Nhóm máy theo Line (stage_type) và Khu (location)
     */
    private function groupMachinesByLineAndArea($machines)
    {
        $grouped = [];
        
        $stage_labels = [
            'molding' => 'Dây chuyền Ép khuôn',
            'assembly' => 'Dây chuyền Lắp ráp', 
            'packaging' => 'Dây chuyền Đóng gói',
            'quality_check' => 'Dây chuyền Kiểm tra CL',
            'other' => 'Dây chuyền Khác'
        ];

        foreach ($machines as $machine) {
            $line = $machine->stage_type;
            $area = $machine->location ?: 'Chưa phân khu';
            
            if (!isset($grouped[$line])) {
                $grouped[$line] = [
                    'label' => $stage_labels[$line] ?? $line,
                    'areas' => []
                ];
            }
            
            if (!isset($grouped[$line]['areas'][$area])) {
                $grouped[$line]['areas'][$area] = [
                    'machines' => [],
                    'count' => 0,
                    'active_count' => 0
                ];
            }
            
            $grouped[$line]['areas'][$area]['machines'][] = $machine;
            $grouped[$line]['areas'][$area]['count']++;
            
            if ($machine->status === 'active') {
                $grouped[$line]['areas'][$area]['active_count']++;
            }
        }
        
        return $grouped;
    }

    /**
     * Helper: JSON Response
     */
    private function jsonResponse($data, $status_code = 200)
    {
        $this->output
             ->set_status_header($status_code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }
}