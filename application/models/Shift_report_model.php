<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift_report_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // List running shifts (basic stub - adapt to your shifts table)
    public function get_running_shifts($production_line_id = null)
    {
        // Use existing `plan_shift` table from project dump. ps_status=1 is treated as running.
        // Also include qty_target and a comma-separated list of machines assigned to the planshift
    $this->db->select("p.id_planshift AS id, p.id_plan, p.id_shift, p.id_staff, p.start_date, p.ps_status, pl.qty_target");
    $this->db->from('plan_shift p');
    $this->db->where('p.ps_status', 1);

    // join planning table to get qty_target, and p_machine->machine to get machine names (grouped)
    $this->db->join('planning pl', 'pl.id_plan = p.id_plan', 'left');
    $this->db->join('p_machine pm', 'pm.id_planshift = p.id_planshift', 'left');
    $this->db->join('machine m', 'm.id_machine = pm.id_machine', 'left');
        if ($production_line_id) {
            $this->db->where('pm.id_machine', intval($production_line_id));
        }
        $this->db->group_by('p.id_planshift');
    $this->db->select('GROUP_CONCAT(DISTINCT m.machine_name SEPARATOR ", ") as machines', FALSE);
    // also return the machine ids as a comma-separated list so views can link to a specific dây chuyền
    $this->db->select('GROUP_CONCAT(DISTINCT pm.id_machine SEPARATOR ",") as machine_ids', FALSE);

        $this->db->order_by('p.start_date', 'DESC');
        $q = $this->db->get();
        return $q->result_array();
    }

    // Get all machine reports for a shift
    public function get_shift_report($shift_id)
    {
        // First try: read from dedicated reporting table if exists
        $this->db->where('shift_id', $shift_id);
        $q = $this->db->get('shift_machine_reports');
        $rows = $q->result_array();
        if (!empty($rows)) {
            return $rows;
        }

        // Fallback: build per-machine report from existing schema (p_machine + machine + plan_shift + sorting_report)
        // Get machines assigned to this planshift
        $this->db->select('pm.id_machine, m.machine_name');
        $this->db->from('p_machine pm');
        $this->db->join('machine m', 'm.id_machine = pm.id_machine', 'left');
        $this->db->where('pm.id_planshift', $shift_id);
        $machines_q = $this->db->get();
        $machines = $machines_q->result_array();

        // Get overall target from plan_shift
    // get qty_target from planning via plan_shift.id_plan
    $plan = $this->db->select('p.id_plan, pl.qty_target')->from('plan_shift p')->join('planning pl','pl.id_plan = p.id_plan','left')->where('p.id_planshift', $shift_id)->get()->row_array();
    $total_target = isset($plan['qty_target']) ? (int)$plan['qty_target'] : null;

        $out = [];
        $count = count($machines) ?: 1;
        foreach ($machines as $m) {
            // attempt to get produced count from sorting_report.finished (fallback)
            $this->db->select('SUM(finished) as finished_sum');
            $this->db->from('sorting_report');
            $this->db->where('id_planshift', $shift_id);
            $sum_q = $this->db->get()->row_array();
            $produced = isset($sum_q['finished_sum']) ? (int)$sum_q['finished_sum'] : 0;

            // naive per-machine allocation when only aggregate exists
            $per_machine_target = $total_target ? intval(round($total_target / $count)) : null;

            $out[] = [
                'machine_id' => $m['id_machine'],
                'machine_name' => $m['machine_name'],
                'produced_qty' => $produced,
                'target_qty' => $per_machine_target,
                'downtime_seconds' => 0,
                'status' => ($produced>0) ? 'running' : 'idle',
                'last_updated_at' => null,
            ];
        }

        // If there are no p_machine rows, return empty array
        return $out;
    }

    // Get single machine report
    public function get_machine_report($shift_id, $machine_id)
    {
        // First try dedicated report table
        $this->db->where('shift_id', $shift_id);
        $this->db->where('machine_id', $machine_id);
        $q = $this->db->get('shift_machine_reports');
        $row = $q->row_array();
        if (!empty($row)) return $row;

        // Fallback: try to build a single machine row from aggregated data (same logic as get_shift_report)
        // Get machine name
        $m = $this->db->select('m.machine_name')->from('p_machine pm')->join('machine m','m.id_machine=pm.id_machine','left')->where('pm.id_planshift',$shift_id)->where('pm.id_machine',$machine_id)->get()->row_array();

        // produced from sorting_report (aggregate across planshift)
        $this->db->select('SUM(finished) as finished_sum');
        $this->db->from('sorting_report');
        $this->db->where('id_planshift', $shift_id);
        $sum_q = $this->db->get()->row_array();
        $produced = isset($sum_q['finished_sum']) ? (int)$sum_q['finished_sum'] : 0;

        // target: divide equally among machines assigned (naive fallback)
    // get qty_target from planning via plan_shift.id_plan
    $plan = $this->db->select('p.id_plan, pl.qty_target')->from('plan_shift p')->join('planning pl','pl.id_plan = p.id_plan','left')->where('p.id_planshift', $shift_id)->get()->row_array();
    $total_target = isset($plan['qty_target']) ? (int)$plan['qty_target'] : null;
        $machines_count = $this->db->from('p_machine')->where('id_planshift',$shift_id)->count_all_results() ?: 1;
        $per_machine_target = $total_target ? intval(round($total_target / $machines_count)) : null;

        return [
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'machine_name' => $m['machine_name'] ?? null,
            'produced_qty' => $produced,
            'target_qty' => $per_machine_target,
            'downtime_seconds' => 0,
            'status' => ($produced>0) ? 'running' : 'idle',
            'last_updated_at' => null,
        ];
    }

    // Get recent events for a machine in a shift
    public function get_machine_events($shift_id, $machine_id, $limit = 10)
    {
        $this->db->where('shift_id', $shift_id);
        $this->db->where('machine_id', $machine_id);
        $this->db->order_by('ts', 'DESC');
        $this->db->limit(intval($limit));
        $q = $this->db->get('shift_machine_events');
        $dbErr = $this->db->error();
        if (!empty($dbErr) && isset($dbErr['code']) && $dbErr['code'] != 0) {
            throw new Exception('Database error: ' . ($dbErr['message'] ?? 'unknown'));
        }
        return $q->result_array();
    }

    // Check access: whether a user can view this shift or machine
    public function user_has_access($shift_id, $user_id, $machine_id = null)
    {
        // Check if user is the assigned staff for plan_shift
        $plan = $this->db->select('id_staff')->where('id_planshift', $shift_id)->get('plan_shift')->row_array();
        if ($plan && isset($plan['id_staff']) && intval($plan['id_staff']) === intval($user_id)) {
            return true;
        }

        // If machine specified, check if user is assigned to that machine via a staff-machine mapping (optional table 'staff_machine')
        // Fallback: allow if user has role 'leader' or 'admin' (role check normally in controller)
        // Here we just return false; controller will allow leader/admin by role.
        return false;
    }

    // Record production increment (atomic)
    public function record_production($shift_id, $machine_id, $delta_qty, $user_id = null)
    {
        $this->db->trans_start();
        $this->db->set('produced_qty', 'produced_qty + ' . intval($delta_qty), FALSE);
        $this->db->where('shift_id', $shift_id)->where('machine_id', $machine_id);
        $this->db->update('shift_machine_reports');

        // if no row exists, insert one
        if ($this->db->affected_rows() == 0) {
            $insert = [
                'shift_id' => $shift_id,
                'machine_id' => $machine_id,
                'produced_qty' => intval($delta_qty),
                'created_at' => date('Y-m-d H:i:s'),
                'recorded_by' => $user_id
            ];
            $this->db->insert('shift_machine_reports', $insert);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // Add detailed event
    public function add_event($shift_id, $machine_id, $event_type, $detail = null, $user_id = null)
    {
        $data = [
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'event_type' => $event_type,
            'detail' => $detail,
            'created_by' => $user_id,
            'ts' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('shift_machine_events', $data);
    }

    // Record downtime interval (start_ts and end_ts are datetimes or strings parseable by strtotime)
    public function record_downtime($shift_id, $machine_id, $start_ts, $end_ts, $user_id = null)
    {
        $start = strtotime($start_ts);
        $end = strtotime($end_ts);
        if ($start === false || $end === false || $end < $start) {
            return false;
        }
        $duration = intval($end - $start);

        $this->db->trans_start();

        // Update or insert into shift_machine_reports
        $this->db->set('downtime_seconds', 'downtime_seconds + ' . $duration, FALSE);
        $this->db->where('shift_id', $shift_id)->where('machine_id', $machine_id);
        $this->db->update('shift_machine_reports');

        if ($this->db->affected_rows() == 0) {
            // insert a minimal row if missing
            $insert = [
                'shift_id' => $shift_id,
                'machine_id' => $machine_id,
                'produced_qty' => 0,
                'target_qty' => null,
                'downtime_seconds' => $duration,
                'status' => 'paused',
                'created_at' => date('Y-m-d H:i:s'),
                'recorded_by' => $user_id
            ];
            $this->db->insert('shift_machine_reports', $insert);
        }

        // Insert an event record
        $detail = "Downtime from " . date('Y-m-d H:i:s', $start) . " to " . date('Y-m-d H:i:s', $end) . ", duration {$duration}s";
        $ev = [
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'event_type' => 'downtime',
            'detail' => $detail,
            'ts' => date('Y-m-d H:i:s', $start),
            'created_by' => $user_id
        ];
        $this->db->insert('shift_machine_events', $ev);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // Aggregate KPI example (simple)
    public function aggregate_kpi($shift_id)
    {
        $this->db->select('SUM(produced_qty) as total_produced, SUM(target_qty) as total_target, SUM(downtime_seconds) as total_downtime');
        $this->db->where('shift_id', $shift_id);
        $q = $this->db->get('shift_machine_reports');
        return $q->row_array();
    }
}
