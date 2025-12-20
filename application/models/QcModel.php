<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QcModel - Quality Control Module Data Access Layer
 * 
 * Handles all QC-related database operations:
 * - Shift closures management
 * - QC sessions and inspection items
 * - Decisions (APPROVE/REJECT)
 * - Attachments and adjustment requests
 * 
 * @author Production Management System v2 Team
 * @date 2025-11-02
 */
class QcModel extends CI_Model
{
    /**
     * Detect shift_closures table schema at runtime (supports multiple migration versions)
     *
     * @return array
     */
    private function _getShiftClosureSchema()
    {
        static $schema = null;

        if ($schema !== null) {
            return $schema;
        }

        $table = 'shift_closures';
        $schema = [
            'exists' => $this->db->table_exists($table),
            'has_code' => false,
            'has_id' => false,
            'has_closure_id' => false,
            'has_line_code' => false,
            'has_shift_code' => false,
            'has_project_code' => false,
            'has_product_code' => false,
            'has_status' => false,
            'has_can_receive_fg' => false,
            'has_lot_code' => false,
            'has_variant' => false,
            'has_qty_finished' => false,
            'has_qty_waste' => false,
        ];

        if (!$schema['exists']) {
            return $schema;
        }

        $schema['has_code'] = $this->db->field_exists('code', $table);
        $schema['has_id'] = $this->db->field_exists('id', $table);
        $schema['has_closure_id'] = $this->db->field_exists('closure_id', $table);
        $schema['has_line_code'] = $this->db->field_exists('line_code', $table);
        $schema['has_shift_code'] = $this->db->field_exists('shift_code', $table);
        $schema['has_project_code'] = $this->db->field_exists('project_code', $table);
        $schema['has_product_code'] = $this->db->field_exists('product_code', $table);
        $schema['has_status'] = $this->db->field_exists('status', $table);
        $schema['has_can_receive_fg'] = $this->db->field_exists('can_receive_fg', $table);
        $schema['has_lot_code'] = $this->db->field_exists('lot_code', $table);
        $schema['has_variant'] = $this->db->field_exists('variant', $table);
        $schema['has_qty_finished'] = $this->db->field_exists('qty_finished', $table);
        $schema['has_qty_waste'] = $this->db->field_exists('qty_waste', $table);

        return $schema;
    }

    // ========================================
    // SHIFT CLOSURES
    // ========================================
    
    /**
     * Get pending shift closures awaiting QC inspection
     * 
     * @param array $filters Optional filters: line_code, shift_code, project_code, date_from, date_to
     * @return array
     */
    public function getPendingClosures($filters = [])
    {
        $schema = $this->_getShiftClosureSchema();
        
        // Lấy phiếu chốt ca chờ QC từ warehouse_import_requests
        // JOIN với shift_closures, production_shifts, production_lines, planning, project, product
        $select = [
            'wir.*',
            'sc.closure_id',
            'sc.closure_code as code',
            'COALESCE(NULLIF(ps.shift_code, ""), ps.shift_name) as shift_code',
            'pl.line_code',
            'pln.plan_name as project_name',
            'p.id_project as project_code',
            'pr.product_name as product_name',
            'pr.id_product as product_code',
            'pr.application as variant',
            'p.diameter as lot_code',
            'sc.total_good as qty_finished',
            'sc.total_defect as qty_waste',
            'sc.closure_date as closed_at',
            'u.full_name as closed_by'
        ];

        // Add schema-based fallbacks from shift_closures
        if ($schema['has_line_code']) $select[] = 'sc.line_code as sc_line_code';
        if ($schema['has_shift_code']) $select[] = 'sc.shift_code as sc_shift_code';
        if ($schema['has_project_code']) $select[] = 'sc.project_code as sc_project_code';
        if ($schema['has_product_code']) $select[] = 'sc.product_code as sc_product_code';
        if ($schema['has_variant']) $select[] = 'sc.variant as sc_variant';

        $this->db->select(implode(",\n            ", $select));
        
        $this->db->from('warehouse_import_requests wir');
        $this->db->join('shift_closures sc', 'wir.closure_id = sc.closure_id', 'left');
        $this->db->join('production_shifts ps', 'sc.shift_id = ps.shift_id', 'left');
        $this->db->join('production_lines pl', 'ps.line_id = pl.id', 'left');
        $this->db->join('planning pln', 'ps.id_plan = pln.id_plan', 'left');
        $this->db->join('project p', 'pln.id_project = p.id_project', 'left');
        $this->db->join('product pr', 'p.id_product = pr.id_product', 'left');
        $this->db->join('user u', 'sc.closed_by = u.user_id', 'left');
        
        // Join with qc_sessions to check if it's already decided
        $this->db->join('qc_sessions qs', 'wir.closure_id = qs.closure_id', 'left');
        
        $this->db->where('wir.status', 'pending_qc');
        $this->db->where('(qs.status IS NULL OR qs.status != "DECIDED")');

        if (!empty($filters['shift_id'])) {
            $this->db->where('ps.shift_id', $filters['shift_id']);
        }
        if (!empty($filters['product_id'])) {
            $this->db->where('p.id_product', $filters['product_id']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('wir.created_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('wir.created_at <=', $filters['date_to']);
        }
        $this->db->order_by('wir.created_at', 'DESC');
        $results = $this->db->get()->result();

        // Apply fallbacks for each result
        foreach ($results as &$row) {
            if (empty($row->line_code) && !empty($row->sc_line_code)) $row->line_code = $row->sc_line_code;
            if (empty($row->shift_code) && !empty($row->sc_shift_code)) $row->shift_code = $row->sc_shift_code;
            
            if (empty($row->product_name)) {
                $row->product_name = !empty($row->product_name) ? $row->product_name : (!empty($row->product_code) ? $row->product_code : $row->code);
            }
            
            if (empty($row->project_name)) {
                if (!empty($row->sc_project_code)) $row->project_name = $row->sc_project_code;
                elseif (!empty($row->project_code)) $row->project_name = $row->project_code;
                else $row->project_name = $row->product_name;
            }

            if (empty($row->variant) && !empty($row->sc_variant)) $row->variant = $row->sc_variant;
        }

        return $results;
    }
    
    /**
     * Get today's QC statistics
     * 
     * @return array
     */
    public function getTodayStatistics()
    {
        $today = date('Y-m-d');
        
        // Count pending QC
        // Đếm số phiếu chốt ca chờ QC trong warehouse_import_requests
        $pending = 0;
        if ($this->db->table_exists('warehouse_import_requests')) {
            $this->db->from('warehouse_import_requests wir');
            $this->db->join('qc_sessions qs', 'wir.closure_id = qs.closure_id', 'left');
            $this->db->where('wir.status', 'pending_qc');
            $this->db->where('(qs.status IS NULL OR qs.status != "DECIDED")');
            $this->db->where('DATE(wir.created_at)', $today);
            $pending = $this->db->count_all_results();
        }
        
        // Count verified today
        $this->db->select('COUNT(*) as count');
        $this->db->from('qc_decisions qd');
        $this->db->join('qc_sessions qs', 'qd.session_id = qs.id', 'inner');
        $this->db->where('qd.result', 'APPROVE');
        $this->db->where('DATE(qd.decided_at)', $today);
        $verified_query = $this->db->get();
        $verified = $verified_query->row()->count ?? 0;
        
        // Count rejected today
        $this->db->select('COUNT(*) as count');
        $this->db->from('qc_decisions qd');
        $this->db->join('qc_sessions qs', 'qd.session_id = qs.id', 'inner');
        $this->db->where('qd.result', 'REJECT');
        $this->db->where('DATE(qd.decided_at)', $today);
        $rejected_query = $this->db->get();
        $rejected = $rejected_query->row()->count ?? 0;
        
        return [
            'pending' => $pending,
            'verified' => $verified,
            'rejected' => $rejected
        ];
    }
    
    /**
     * Get warehouse import request by closure_id
     * 
     * @param int $closure_id
     * @return object|null
     */
    public function getWarehouseImportRequest($closure_id)
    {
        $this->db->select('*');
        $this->db->from('warehouse_import_requests');
        $this->db->where('closure_id', $closure_id);
        return $this->db->get()->row();
    }
    
    /**
     * Get closure by ID
     * 
     * @param int $id
     * @return object|null
     */
    public function getClosureById($id)
    {
        $schema = $this->_getShiftClosureSchema();

        if (!$schema['exists']) {
            return null;
        }

        // Determine primary key column
        $pk = $schema['has_id'] ? 'id' : ($schema['has_closure_id'] ? 'closure_id' : 'id');

        if ($schema['has_project_code'] && $schema['has_product_code']) {
            $this->db->select('sc.*, p.project_name, pr.product_name');
            $this->db->from('shift_closures sc');
            $this->db->join('project p', 'sc.project_code = p.id_project', 'left');
            $this->db->join('product pr', 'sc.product_code = pr.id_product', 'left');
        } else {
            $this->db->select('sc.*');
            $this->db->from('shift_closures sc');
        }

        $this->db->where('sc.' . $pk, $id);

        return $this->db->get()->row();
    }
    
    /**
     * Update closure status
     * 
     * @param int $closure_id
     * @param string $status VERIFIED|REJECTED
     * @param bool $can_receive_fg
     * @return bool
     */
    public function updateClosureStatus($closure_id, $status, $can_receive_fg = false)
    {
        $schema = $this->_getShiftClosureSchema();

        // Determine primary key column (id vs closure_id) based on actual schema
        $pk = 'id';
        if ($schema['exists']) {
            if ($schema['has_id']) {
                $pk = 'id';
            } elseif ($schema['has_closure_id']) {
                $pk = 'closure_id';
            }
        }

        $data = [
            'status' => $status,
        ];

        // Only set can_receive_fg flag if the column exists in this schema
        if ($schema['exists'] && !empty($schema['has_can_receive_fg']) && $schema['has_can_receive_fg']) {
            $data['can_receive_fg'] = $can_receive_fg ? 1 : 0;
        }

        // Note: we update the base table without alias here
        return $this->db->update('shift_closures', $data, [$pk => $closure_id]);
    }

    /**
     * Update warehouse import request status
     * 
     * @param int $closure_id
     * @param string $status
     * @param array $extra_data
     * @return bool
     */
    public function updateWarehouseImportRequestStatus($closure_id, $status, $extra_data = [])
    {
        if (!$this->db->table_exists('warehouse_import_requests')) {
            return false;
        }

        $data = array_merge(['status' => $status], $extra_data);
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('closure_id', $closure_id);
        return $this->db->update('warehouse_import_requests', $data);
    }
    
    // ========================================
    // QC SESSIONS
    // ========================================
    
    /**
     * Create new QC session
     * 
     * @param array $data
     * @return int|bool Session ID or false
     */
    public function createSession($data)
    {
        // Generate session code
        if (empty($data['code'])) {
            $data['code'] = $this->generateSessionCode();
        }
        
        if (empty($data['started_at'])) {
            $data['started_at'] = date('Y-m-d H:i:s');
        }
        
        if ($this->db->insert('qc_sessions', $data)) {
            $session_id = $this->db->insert_id();
            
            return $session_id;
        }
        
        return false;
    }
    
    /**
     * Get session by ID with closure details
     * 
     * @param int $id
     * @return object|null
     */
    public function getSessionById($id)
    {
        $schema = $this->_getShiftClosureSchema();
        
        // Select session data + closure + project/product/line/shift info via joins
        $select = [
            'qs.*',
            'sc.closure_id',
            'sc.closure_code as code',
            'sc.closure_code',
            'COALESCE(NULLIF(ps.shift_code, ""), ps.shift_name) as shift_code',
            'pl.line_code',
            'pln.plan_name as project_name',
            'p.id_project as project_code',
            'pr.product_name as product_name',
            'pr.id_product as product_code',
            'pr.application as variant',
            'p.diameter as lot_code',
            'sc.total_good as qty_finished',
            'sc.total_defect as qty_waste',
            'sc.closure_date as closed_at',
            'u.full_name as inspector_name',
            'u2.full_name as closed_by',
            'qd.result',
            'qd.reason as decision_reason',
            'qd.defect_rate',
            'qd.aql as decision_aql',
            'qd.decided_by',
            'qd.decided_at',
            'wir.status as closure_status',
            'wir.product_name as wir_product_name',
            'wir.product_code as wir_product_code'
        ];

        // Add schema-based fallbacks from shift_closures
        if ($schema['has_line_code']) $select[] = 'sc.line_code as sc_line_code';
        if ($schema['has_shift_code']) $select[] = 'sc.shift_code as sc_shift_code';
        if ($schema['has_project_code']) $select[] = 'sc.project_code as sc_project_code';
        if ($schema['has_product_code']) $select[] = 'sc.product_code as sc_product_code';
        if ($schema['has_variant']) $select[] = 'sc.variant as sc_variant';

        $this->db->select(implode(",\n            ", $select));
        
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.closure_id', 'left');
        $this->db->join('warehouse_import_requests wir', 'qs.closure_id = wir.closure_id', 'left');
        $this->db->join('production_shifts ps', 'sc.shift_id = ps.shift_id', 'left');
        $this->db->join('production_lines pl', 'ps.line_id = pl.id', 'left');
        $this->db->join('planning pln', 'ps.id_plan = pln.id_plan', 'left');
        $this->db->join('project p', 'pln.id_project = p.id_project', 'left');
        $this->db->join('product pr', 'p.id_product = pr.id_product', 'left');
        $this->db->join('user u', 'qs.inspector_code = u.username', 'left');
        $this->db->join('user u2', 'sc.closed_by = u2.user_id', 'left');
        $this->db->join('qc_decisions qd', 'qs.id = qd.session_id', 'left');
        
        $this->db->where('qs.id', $id);
        $result = $this->db->get()->row();

        // Đảm bảo luôn có đầy đủ thông tin hiển thị (Fallback logic)
        if ($result) {
            // 1. Fallback cho Line/Shift
            if (empty($result->line_code) && !empty($result->sc_line_code)) {
                $result->line_code = $result->sc_line_code;
            }
            if (empty($result->shift_code) && !empty($result->sc_shift_code)) {
                $result->shift_code = $result->sc_shift_code;
            }

            // 2. Fallback cho Product Code
            if (empty($result->product_code)) {
                if (!empty($result->sc_product_code)) $result->product_code = $result->sc_product_code;
                elseif (!empty($result->wir_product_code)) $result->product_code = $result->wir_product_code;
                else $result->product_code = $result->product_name;
            }
            
            // 3. Fallback cho Product Name
            if (empty($result->product_name)) {
                if (!empty($result->wir_product_name)) $result->product_name = $result->wir_product_name;
                else $result->product_name = $result->product_code;
            }
            
            // 4. Fallback cho Project Name
            if (empty($result->project_name)) {
                if (!empty($result->sc_project_code)) $result->project_name = $result->sc_project_code;
                elseif (!empty($result->project_code)) $result->project_name = $result->project_code;
                else $result->project_name = $result->product_name;
            }
            
            // 5. Fallback cho Project Code
            if (empty($result->project_code)) {
                $result->project_code = !empty($result->sc_project_code) ? $result->sc_project_code : $result->project_name;
            }

            // 6. Fallback cho Variant
            if (empty($result->variant)) {
                if (!empty($result->sc_variant)) $result->variant = $result->sc_variant;
                elseif (isset($result->wir_variant)) $result->variant = $result->wir_variant;
            }
        }
        return $result;
    }
    
    /**
     * Get all sessions by inspector username
     * 
     * @param string $inspector_code
     * @return array
     */
    public function getSessionsByInspector($inspector_code)
    {
        $schema = $this->_getShiftClosureSchema();

        // Determine closure PK for join
        $closurePk = 'id';
        if ($schema['exists'] && !$schema['has_id'] && $schema['has_closure_id']) {
            $closurePk = 'closure_id';
        }

        $select = ['qs.*'];

        if ($schema['exists']) {
            $closureCodeSelect = 'NULL';
            if ($schema['has_code']) {
                $closureCodeSelect = 'sc.code';
            } elseif ($schema['has_id']) {
                $closureCodeSelect = 'sc.id';
            } elseif ($schema['has_closure_id']) {
                $closureCodeSelect = 'sc.closure_id';
            }

            $select[] = $closureCodeSelect . ' as closure_code';
        }

        // Always join with production tables to get full info
        $select[] = 'pl.line_code';
        $select[] = 'COALESCE(NULLIF(ps.shift_code, ""), ps.shift_name) as shift_code';
        $select[] = 'pln.plan_name as project_name';
        $select[] = 'pr.product_name';
        $select[] = 'qd.result';
        $select[] = 'qd.decided_at';
        
        // Add schema-based fallbacks from shift_closures
        if ($schema['has_line_code']) $select[] = 'sc.line_code as sc_line_code';
        if ($schema['has_shift_code']) $select[] = 'sc.shift_code as sc_shift_code';
        if ($schema['has_project_code']) $select[] = 'sc.project_code as sc_project_code';
        if ($schema['has_product_code']) $select[] = 'sc.product_code as sc_product_code';

        $this->db->select(implode(",\n            ", $select));
        $this->db->from('qc_sessions qs');

        if ($schema['exists']) {
            $this->db->join('shift_closures sc', 'qs.closure_id = sc.' . $closurePk, 'left');
            $this->db->join('production_shifts ps', 'sc.shift_id = ps.shift_id', 'left');
            $this->db->join('production_lines pl', 'ps.line_id = pl.id', 'left');
            $this->db->join('planning pln', 'ps.id_plan = pln.id_plan', 'left');
            $this->db->join('project p', 'pln.id_project = p.id_project', 'left');
            $this->db->join('product pr', 'p.id_product = pr.id_product', 'left');
        } else {
            // Fallback if shift_closures doesn't exist (unlikely but for safety)
            $this->db->join('shift_closures sc', '1=0', 'left');
            $this->db->join('production_shifts ps', '1=0', 'left');
            $this->db->join('production_lines pl', '1=0', 'left');
            $this->db->join('planning pln', '1=0', 'left');
            $this->db->join('project p', '1=0', 'left');
            $this->db->join('product pr', '1=0', 'left');
        }

        $this->db->join('qc_decisions qd', 'qs.id = qd.session_id', 'left');
        $this->db->where('qs.inspector_code', $inspector_code);
        $this->db->order_by('qs.created_at', 'DESC');
        
        $results = $this->db->get()->result();

        // Apply fallbacks for each result
        foreach ($results as &$row) {
            if (empty($row->line_code) && !empty($row->sc_line_code)) $row->line_code = $row->sc_line_code;
            if (empty($row->shift_code) && !empty($row->sc_shift_code)) $row->shift_code = $row->sc_shift_code;
            
            if (empty($row->product_name)) {
                $row->product_name = !empty($row->sc_product_code) ? $row->sc_product_code : $row->closure_code;
            }
            
            if (empty($row->project_name)) {
                $row->project_name = !empty($row->sc_project_code) ? $row->sc_project_code : $row->product_name;
            }
        }

        return $results;
    }
    
    /**
     * Get decided sessions with filters (for reports)
     * 
     * @param array $filters
     * @return array
     */
    public function getDecidedSessions($filters = [])
    {
        $schema = $this->_getShiftClosureSchema();

        // Determine closure PK for join
        $closurePk = 'id';
        if ($schema['exists'] && !$schema['has_id'] && $schema['has_closure_id']) {
            $closurePk = 'closure_id';
        }

        $select = ['qs.*'];

        if ($schema['exists']) {
            $closureCodeSelect = 'NULL';
            if ($schema['has_code']) {
                $closureCodeSelect = 'sc.code';
            } elseif ($schema['has_id']) {
                $closureCodeSelect = 'sc.id';
            } elseif ($schema['has_closure_id']) {
                $closureCodeSelect = 'sc.closure_id';
            }

            $select[] = $closureCodeSelect . ' as closure_code';
            
            // These are joined from production_shifts and production_lines
            $select[] = 'pl.line_code';
            $select[] = 'COALESCE(NULLIF(ps.shift_code, ""), ps.shift_name) as shift_code';

            if ($schema['has_project_code']) {
                $select[] = 'sc.project_code';
            }
            if ($schema['has_product_code']) {
                $select[] = 'sc.product_code';
            }
            if ($schema['has_lot_code']) {
                $select[] = 'sc.lot_code';
            }

            // Add schema-based fallbacks from shift_closures
            if ($schema['has_line_code']) $select[] = 'sc.line_code as sc_line_code';
            if ($schema['has_shift_code']) $select[] = 'sc.shift_code as sc_shift_code';
        }

        $select[] = 'pln.plan_name as project_name';
        $select[] = 'pr.product_name';
        $select[] = 'qd.result';
        $select[] = 'qd.defect_rate';
        $select[] = 'qd.aql';
        $select[] = 'qd.reason as decision_reason';
        $select[] = 'qd.decided_by';
        $select[] = 'qd.decided_at';
        $select[] = 'u.full_name as inspector_name';
        $select[] = 'u2.full_name as decided_by_name';

        $this->db->select(implode(",\n            ", $select));
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.closure_id', 'left');
        $this->db->join('production_shifts ps', 'sc.shift_id = ps.shift_id', 'left');
        $this->db->join('production_lines pl', 'ps.line_id = pl.id', 'left');
        $this->db->join('planning pln', 'ps.id_plan = pln.id_plan', 'left');
        $this->db->join('project p', 'pln.id_project = p.id_project', 'left');
        $this->db->join('product pr', 'p.id_product = pr.id_product', 'left');
        $this->db->join('user u', 'qs.inspector_code = u.username', 'left');
        $this->db->join('qc_decisions qd', 'qs.id = qd.session_id', 'inner');
        $this->db->join('user u2', 'qd.decided_by = u2.username', 'left');

        $this->db->where('qs.status', 'DECIDED');
        
        // Apply filters
        if (!empty($filters['line_code'])) {
            $this->db->where('pl.line_code', $filters['line_code']);
        }
        if (!empty($filters['shift_code'])) {
            $this->db->where('ps.shift_code', $filters['shift_code']);
        }
        if (!empty($filters['project_code'])) {
            // Try to filter by project_code in sc or project_name in p
            $this->db->group_start();
            if ($schema['has_project_code']) {
                $this->db->where('sc.project_code', $filters['project_code']);
            }
            $this->db->or_where('p.id_project', $filters['project_code']);
            $this->db->group_end();
        }
        if (!empty($filters['result'])) {
            $this->db->where('qd.result', $filters['result']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(qd.decided_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(qd.decided_at) <=', $filters['date_to']);
        }
        
        $this->db->order_by('qd.decided_at', 'DESC');
        
        $results = $this->db->get()->result();

        // Apply fallbacks for each result
        foreach ($results as &$row) {
            if (empty($row->line_code) && !empty($row->sc_line_code)) $row->line_code = $row->sc_line_code;
            if (empty($row->shift_code) && !empty($row->sc_shift_code)) $row->shift_code = $row->sc_shift_code;
            
            if (empty($row->product_name)) {
                $row->product_name = !empty($row->product_code) ? $row->product_code : (!empty($row->sc_product_code) ? $row->sc_product_code : $row->closure_code);
            }
            
            if (empty($row->project_name)) {
                $row->project_name = !empty($row->project_code) ? $row->project_code : (!empty($row->sc_project_code) ? $row->sc_project_code : $row->product_name);
            }
        }

        return $results;
    }
    
    /**
     * Get sessions by closure ID
     * 
     * @param int $closure_id
     * @return array
     */
    public function getSessionsByClosureId($closure_id)
    {
        $this->db->select('qs.*, u.full_name as inspector_name');
        $this->db->from('qc_sessions qs');
        $this->db->join('user u', 'qs.inspector_code = u.username', 'left');
        $this->db->where('qs.closure_id', $closure_id);
        $this->db->order_by('qs.started_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get existing session by closure ID (OPEN status or latest)
     * 
     * @param int $closure_id
     * @return object|null
     */
    public function getSessionByClosureId($closure_id)
    {
        // First try to get OPEN session
        $this->db->select('qs.*');
        $this->db->from('qc_sessions qs');
        $this->db->where('qs.closure_id', $closure_id);
        $this->db->where('qs.status', 'OPEN');
        $this->db->order_by('qs.started_at', 'DESC');
        $this->db->limit(1);
        
        $open_session = $this->db->get()->row();
        
        if ($open_session) {
            return $open_session;
        }
        
        // If no OPEN session, get latest session (any status)
        $this->db->select('qs.*');
        $this->db->from('qc_sessions qs');
        $this->db->where('qs.closure_id', $closure_id);
        $this->db->order_by('qs.started_at', 'DESC');
        $this->db->limit(1);
        
        return $this->db->get()->row();
    }
    
    /**
     * Update session status
     * 
     * @param int $session_id
     * @param string $status OPEN|DECIDED
     * @return bool
     */
    public function updateSessionStatus($session_id, $status)
    {
        return $this->db->update('qc_sessions', ['status' => $status], ['id' => $session_id]);
    }
    
    /**
     * Check if user can access session (permission check)
     * 
     * @param int $session_id
     * @param string $user_code
     * @param string|null $line_code User's assigned line (null = can access all)
     * @return bool
     */
    public function canUserAccessSession($session_id, $user_code, $line_code = null)
    {
        $this->db->select('qs.inspector_code, sc.line_code');
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.id');
        $this->db->where('qs.id', $session_id);
        
        $session = $this->db->get()->row();
        
        if (!$session) {
            return false;
        }
        
        // Inspector owns the session
        if ($session->inspector_code === $user_code) {
            return true;
        }
        
        // Check line assignment if provided
        if ($line_code !== null && $session->line_code !== $line_code) {
            return false;
        }
        
        return true;
    }
    
    // ========================================
    // QC ITEMS
    // ========================================
    
    /**
     * Save multiple QC items (bulk insert/update)
     * 
     * @param int $session_id
     * @param array $items
     * @return bool
     */
    public function saveQcItems($session_id, $items)
    {
        if (empty($items)) {
            return false;
        }
        
        // Start transaction
        $this->db->trans_start();
        
        foreach ($items as $item) {
            $item['session_id'] = $session_id;
            $checklist_item_code = $item['checklist_item_code'];
            
            // Extract metadata fields (used for auto-create, not for qc_items)
            $item_name = $item['item_name'] ?? null;
            $description = $item['description'] ?? null;
            $test_method = $item['test_method'] ?? null;
            
            // Remove these fields from item array before saving to qc_items
            unset($item['item_name']);
            unset($item['description']);
            unset($item['test_method']);
            
            // Check if checklist_item_code exists in qc_checklist_master (using 'code' column)
            $current_product_code = $this->getProductCodeFromSession($session_id);
            $checklist_exists = $this->db->get_where('qc_checklist_master', [
                'code' => $checklist_item_code,
                'product_code' => $current_product_code
            ])->row();
            
            // If checklist item doesn't exist, create it
            if (!$checklist_exists) {
                $this->db->insert('qc_checklist_master', [
                    'code' => $checklist_item_code,
                    'item_name' => $item_name ?? 'Item: ' . $checklist_item_code,
                    'criteria' => $description ?? '',  // Map description to criteria column
                    'product_code' => $current_product_code,
                    'sequence' => 999,  // Default sequence, can be reordered
                    'variant' => null,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Check if item exists in qc_items (by session_id + checklist_item_code)
            $existing = $this->db->get_where('qc_items', [
                'session_id' => $session_id,
                'checklist_item_code' => $checklist_item_code
            ])->row();
            
            // Ensure session_id is in the data
            $item['session_id'] = $session_id;
            
            if ($existing) {
                // Update
                $this->db->update('qc_items', $item, ['id' => $existing->id]);
            } else {
                // Insert (only with qc_items valid columns)
                $this->db->insert('qc_items', $item);
            }
        }
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    /**
     * Get product code from session (via join with closure/shift/plan)
     * 
     * @param int $session_id
     * @return string
     */
    private function getProductCodeFromSession($session_id)
    {
        // Get session with all joined details
        $session = $this->getSessionById($session_id);
        
        if ($session && !empty($session->product_code)) {
            return $session->product_code;
        }
        
        return 'DEFAULT_PRODUCT';
    }
    
    /**
     * Get QC items by session ID
     * 
     * @param int $session_id
     * @return array
     */
    public function getQcItemsBySessionId($session_id)
    {
        $this->db->select('*');
        $this->db->from('qc_items');
        $this->db->where('session_id', $session_id);
        $this->db->order_by('id', 'ASC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Calculate defect rate from QC items
     * 
     * @param int $session_id
     * @return array [total_items, failed_items, defect_rate, critical_count, major_count, minor_count]
     */
    public function calculateDefectRate($session_id)
    {
        $items = $this->getQcItemsBySessionId($session_id);
        
        $total = count($items);
        $failed = 0;
        $critical = 0;
        $major = 0;
        $minor = 0;
        
        foreach ($items as $item) {
            if ($item->result === 'FAIL') {
                $failed++;
                
                switch ($item->severity) {
                    case 'CRITICAL':
                        $critical++;
                        break;
                    case 'MAJOR':
                        $major++;
                        break;
                    case 'MINOR':
                        $minor++;
                        break;
                }
            }
        }
        
        $defect_rate = $total > 0 ? ($failed / $total) * 100 : 0;
        
        return [
            'total_items' => $total,
            'failed_items' => $failed,
            'passed_items' => $total - $failed,
            'defect_rate' => round($defect_rate, 2),
            'critical_count' => $critical,
            'major_count' => $major,
            'minor_count' => $minor
        ];
    }
    
    // ========================================
    // QC DECISIONS
    // ========================================
    
    /**
     * Create QC decision
     * 
     * @param array $data
     * @return int|bool Decision ID or false
     */
    public function createDecision($data)
    {
        if (empty($data['decided_at'])) {
            $data['decided_at'] = date('Y-m-d H:i:s');
        }
        
        if ($this->db->insert('qc_decisions', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }
    
    /**
     * Get decision by session ID
     * 
     * @param int $session_id
     * @return object|null
     */
    public function getDecisionBySessionId($session_id)
    {
        return $this->db->get_where('qc_decisions', ['session_id' => $session_id])->row();
    }
    
    /**
     * Process APPROVE decision (transactional)
     * 
     * @param int $session_id
     * @param array $decision_data [aql, defect_rate, decided_by]
     * @return bool
     */
    public function processApproveDecision($session_id, $decision_data)
    {
        $this->db->trans_start();
        
        // 1. Get session to find closure_id
        $session = $this->getSessionById($session_id);
        if (!$session) {
            $this->db->trans_rollback();
            return false;
        }
        
        // 2. Create decision record
        $decision = [
            'session_id' => $session_id,
            'result' => 'APPROVE',
            'aql' => $decision_data['aql'] ?? null,
            'defect_rate' => $decision_data['defect_rate'] ?? null,
            'reason' => $decision_data['reason'] ?? null,
            'decided_by' => $decision_data['decided_by'] ?? null,
            'decided_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('qc_decisions', $decision);
        
        // 3. Update session status to DECIDED
        $this->db->update('qc_sessions', ['status' => 'DECIDED'], ['id' => $session_id]);
        
        // 4. Update closure status to VERIFIED with can_receive_fg flag
        $this->updateClosureStatus($session->closure_id, 'VERIFIED', true);
        
        // 5. Update warehouse import request status to qc_approved
        $this->updateWarehouseImportRequestStatus($session->closure_id, 'qc_approved', [
            'qc_by' => $decision_data['decided_by'],
            'qc_date' => date('Y-m-d H:i:s'),
            'qc_approved_quantity' => $session->qty_finished ?? 0
        ]);
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    /**
     * Process REJECT decision (transactional)
     * 
     * @param int $session_id
     * @param array $decision_data [aql, defect_rate, reason, decided_by]
     * @return bool
     */
    public function processRejectDecision($session_id, $decision_data)
    {
        $this->db->trans_start();
        
        // 1. Get session to find closure_id
        $session = $this->getSessionById($session_id);
        if (!$session) {
            $this->db->trans_rollback();
            return false;
        }
        
        // 2. Create decision record
        $decision = [
            'session_id' => $session_id,
            'result' => 'REJECT',
            'aql' => $decision_data['aql'] ?? null,
            'defect_rate' => $decision_data['defect_rate'] ?? null,
            'reason' => $decision_data['reason'],
            'decided_by' => $decision_data['decided_by'] ?? null,
            'decided_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('qc_decisions', $decision);
        
        // 3. Update session status to DECIDED
        $this->db->update('qc_sessions', ['status' => 'DECIDED'], ['id' => $session_id]);
        
        // 4. Update closure status to REJECTED
        $this->updateClosureStatus($session->closure_id, 'REJECTED', false);
        
        // 5. Update warehouse import request status to qc_rejected
        $this->updateWarehouseImportRequestStatus($session->closure_id, 'qc_rejected', [
            'qc_by' => $decision_data['decided_by'],
            'qc_date' => date('Y-m-d H:i:s'),
            'qc_rejected_quantity' => $session->qty_finished ?? 0,
            'qc_notes' => $decision_data['reason']
        ]);
        
        // 6. Create adjustment request
        $adj_request = [
            'code' => $this->generateAdjustmentRequestCode(),
            'closure_id' => $session->closure_id,
            'created_by' => $decision_data['decided_by'] ?? $session->inspector_code,
            'reason' => $decision_data['reason'],
            'status' => 'OPEN'
        ];
        $this->db->insert('adjustment_requests', $adj_request);
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    // ========================================
    // QC ATTACHMENTS
    // ========================================
    
    /**
     * Save attachment record
     * 
     * @param array $data
     * @return int|bool Attachment ID or false
     */
    public function saveAttachment($data)
    {
        if ($this->db->insert('qc_attachments', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }
    
    /**
     * Get attachments by session ID
     * 
     * @param int $session_id
     * @return array
     */
    public function getAttachmentsBySessionId($session_id)
    {
        $this->db->select('*');
        $this->db->from('qc_attachments');
        $this->db->where('session_id', $session_id);
        $this->db->order_by('created_at', 'ASC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Count attachments for a session
     * 
     * @param int $session_id
     * @return int
     */
    public function countAttachments($session_id)
    {
        return $this->db->where('session_id', $session_id)->count_all_results('qc_attachments');
    }
    
    // ========================================
    // ADJUSTMENT REQUESTS
    // ========================================
    
    /**
     * Get adjustment requests by status
     * 
     * @param string|null $status OPEN|ACKED|DONE
     * @return array
     */
    public function getAdjustmentRequests($status = null)
    {
        $schema = $this->_getShiftClosureSchema();

        $closurePk = 'id';
        if ($schema['exists'] && !$schema['has_id'] && $schema['has_closure_id']) {
            $closurePk = 'closure_id';
        }

        $closureCodeSelect = 'NULL';
        if ($schema['has_code']) {
            $closureCodeSelect = 'sc.code';
        } elseif ($schema['has_id']) {
            $closureCodeSelect = 'sc.id';
        } elseif ($schema['has_closure_id']) {
            $closureCodeSelect = 'sc.closure_id';
        }

        $select = ['ar.*'];
        $select[] = $closureCodeSelect . ' as closure_code';

        if ($schema['has_line_code']) {
            $select[] = 'sc.line_code';
        }
        if ($schema['has_shift_code']) {
            $select[] = 'sc.shift_code';
        }

        $this->db->select(implode(', ', $select));
        $this->db->select('u.full_name as creator_name');
        $this->db->from('adjustment_requests ar');

        if ($schema['exists']) {
            $this->db->join('shift_closures sc', 'ar.closure_id = sc.' . $closurePk, 'left');
        }

        $this->db->join('user u', 'ar.created_by = u.username', 'left');
        
        if ($status !== null) {
            $this->db->where('ar.status', $status);
        }
        
        $this->db->order_by('ar.created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    // ========================================
    // CHECKLIST MASTER
    // ========================================
    
    /**
     * Get checklist items for product/variant
     * 
     * @param string $product_code
     * @param string|null $variant
     * @return array
     */
    public function getChecklistItems($product_code, $variant = null)
    {
        $this->db->select('*');
        $this->db->from('qc_checklist_master');
        $this->db->where('product_code', $product_code);
        $this->db->where('is_active', 1);
        
        // Match specific variant or NULL (applies to all variants)
        if ($variant !== null) {
            $this->db->group_start();
            $this->db->where('variant', $variant);
            $this->db->or_where('variant IS NULL');
            $this->db->group_end();
        } else {
            $this->db->where('variant IS NULL');
        }
        
        $this->db->order_by('sequence', 'ASC');
        
        $result = $this->db->get()->result();
        
        return $result;
    }
    
    // ========================================
    // QC CONFIG
    // ========================================
    
    /**
     * Get QC config value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        $config = $this->db->get_where('qc_config', ['config_key' => $key])->row();
        
        return $config ? $config->config_value : $default;
    }
    
    /**
     * Get default AQL
     * 
     * @return float
     */
    public function getDefaultAql()
    {
        return (float) $this->getConfig('QC_AQL_DEFAULT', 2.5);
    }
    
    /**
     * Get near threshold margin
     * 
     * @return float
     */
    public function getNearThresholdMargin()
    {
        return (float) $this->getConfig('QC_NEAR_THRESHOLD_MARGIN', 5);
    }
    
    // ========================================
    // HELPER METHODS
    // ========================================
    
    /**
     * Generate unique session code
     * Format: QCS-YYYYMMDD-NNNN
     * 
     * @return string
     */
    private function generateSessionCode()
    {
        $date = date('Ymd');
        $prefix = "QCS-{$date}-";
        
        // Find max sequence for today
        $this->db->select('code');
        $this->db->from('qc_sessions');
        $this->db->like('code', $prefix, 'after');
        $this->db->order_by('code', 'DESC');
        $this->db->limit(1);
        
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_seq = (int) substr($last->code, -4);
            $seq = $last_seq + 1;
        } else {
            $seq = 1;
        }
        
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate unique adjustment request code
     * Format: AR-YYYYMMDD-NNNN
     * 
     * @return string
     */
    private function generateAdjustmentRequestCode()
    {
        $date = date('Ymd');
        $prefix = "AR-{$date}-";
        
        // Find max sequence for today
        $this->db->select('code');
        $this->db->from('adjustment_requests');
        $this->db->like('code', $prefix, 'after');
        $this->db->order_by('code', 'DESC');
        $this->db->limit(1);
        
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_seq = (int) substr($last->code, -4);
            $seq = $last_seq + 1;
        } else {
            $seq = 1;
        }
        
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
    
    // ========================================
    // VALIDATION METHODS (Use Case 19)
    // ========================================
    
    /**
     * Check if checklist is complete
     * Validates that all required checklist items have been filled
     * 
     * @param int $session_id
     * @return array ['complete' => bool, 'total' => int, 'filled' => int, 'missing' => array]
     */
    public function isChecklistComplete($session_id)
    {
        $session = $this->getSessionById($session_id);
        
        if (!$session) {
            return [
                'complete' => false,
                'total' => 0,
                'filled' => 0,
                'error' => 'Session not found'
            ];
        }
        
        // Get filled items first (always available if saved)
        $qc_items = $this->getQcItemsBySessionId($session_id);
        $filled_items = count($qc_items);
        
        // Get product_code and variant
        $product_code = !empty($session->product_code) ? $session->product_code : 'DEFAULT_PRODUCT';
        $variant = isset($session->variant) ? $session->variant : null;
        
        // Get required checklist items from master
        $checklist_items = $this->getChecklistItems($product_code, $variant);
        $total_items = count($checklist_items);
        
        // If master is empty but we have results, use results count as total (for auto-generated items)
        if ($total_items === 0 && $filled_items > 0) {
            $total_items = $filled_items;
            $missing_items = [];
            $complete = true;
        } else {
            // Find missing items
            $filled_codes = array_column($qc_items, 'checklist_item_code');
            $required_codes = array_column($checklist_items, 'code');
            $missing_codes = array_diff($required_codes, $filled_codes);
            
            $missing_items = [];
            foreach ($checklist_items as $item) {
                if (in_array($item->code, $missing_codes)) {
                    $missing_items[] = [
                        'code' => $item->code,
                        'name' => $item->item_name,
                        'category' => $item->category
                    ];
                }
            }
            $complete = ($total_items > 0 && $filled_items >= $total_items);
        }
        
        return [
            'complete' => $complete,
            'total' => $total_items,
            'filled' => $filled_items,
            'missing' => $missing_items,
            'completion_rate' => $total_items > 0 ? round(($filled_items / $total_items) * 100, 2) : 0
        ];
    }
    
    /**
     * Validate requirements for REJECT decision
     * Use Case 19 - Alternative Flow 8.1: Reject requires reason + attachment
     * 
     * @param int $session_id
     * @param string $reason
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateRejectRequirements($session_id, $reason)
    {
        $errors = [];
        
        // Check reason is provided
        if (empty($reason) || trim($reason) === '') {
            $errors[] = 'Lý do từ chối là bắt buộc';
        }
        
        // Check reason minimum length
        if (strlen(trim($reason)) < 20) {
            $errors[] = 'Lý do từ chối phải có ít nhất 20 ký tự';
        }
        
        // Check attachments exist
        $attachment_count = $this->countAttachments($session_id);
        if ($attachment_count === 0) {
            $errors[] = 'Phải đính kèm ít nhất 1 ảnh/video làm bằng chứng khi từ chối';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'attachment_count' => $attachment_count
        ];
    }
    
    /**
     * Check if defect rate is near AQL threshold
     * Use Case 19 - Alternative Flow 6.1: Near threshold detection
     * 
     * @param float $defect_rate Current defect rate
     * @param float $aql Acceptance Quality Limit
     * @param float $margin Margin percentage (default from config)
     * @return array ['near_threshold' => bool, 'distance' => float, 'recommendation' => string]
     */
    public function isNearThreshold($defect_rate, $aql, $margin = null)
    {
        if ($margin === null) {
            $margin = $this->getNearThresholdMargin();
        }
        
        // Calculate threshold range
        $lower_bound = $aql - ($aql * $margin / 100);
        $upper_bound = $aql + ($aql * $margin / 100);
        
        $near_threshold = ($defect_rate >= $lower_bound && $defect_rate <= $upper_bound);
        $distance = abs($defect_rate - $aql);
        $distance_percent = $aql > 0 ? ($distance / $aql) * 100 : 0;
        
        $recommendation = '';
        if ($near_threshold) {
            if ($defect_rate > $aql) {
                $recommendation = 'INCREASE_SAMPLE_SIZE';
            } else {
                $recommendation = 'REVIEW_CAREFULLY';
            }
        } elseif ($defect_rate > $aql) {
            $recommendation = 'REJECT';
        } else {
            $recommendation = 'APPROVE';
        }
        
        return [
            'near_threshold' => $near_threshold,
            'distance' => round($distance, 2),
            'distance_percent' => round($distance_percent, 2),
            'lower_bound' => round($lower_bound, 2),
            'upper_bound' => round($upper_bound, 2),
            'recommendation' => $recommendation,
            'message' => $near_threshold 
                ? "Tỷ lệ lỗi ({$defect_rate}%) gần ngưỡng AQL ({$aql}%). {$recommendation}."
                : "Tỷ lệ lỗi rõ ràng. {$recommendation}."
        ];
    }
    
    /**
     * Validate APPROVE decision requirements
     * 
     * @param int $session_id
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateApproveRequirements($session_id)
    {
        $errors = [];
        
        // Check checklist completeness
        $checklist_status = $this->isChecklistComplete($session_id);
        if (!$checklist_status['complete']) {
            $errors[] = sprintf(
                'Checklist chưa đầy đủ (%d/%d items). Còn thiếu: %s',
                $checklist_status['filled'],
                $checklist_status['total'],
                implode(', ', array_column($checklist_status['missing'], 'name'))
            );
        }
        
        // Check for critical defects
        $stats = $this->calculateDefectRate($session_id);
        if ($stats['critical_count'] > 0) {
            $errors[] = sprintf(
                'Không thể duyệt khi có %d lỗi CRITICAL',
                $stats['critical_count']
            );
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'stats' => $stats
        ];
    }
}
