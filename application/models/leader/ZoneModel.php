<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Zone Model
 * Model quản lý khu vực sản xuất
 * 
 * @package    Production Management
 * @subpackage Models/Leader
 * @category   Zone Management
 * @author     Copilot AI
 * @version    1.0
 */
class ZoneModel extends CI_Model
{
    private $table = 'zones';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Lấy tất cả zones với thống kê
     * 
     * @param array $filters Filter options
     * @return array List of zones with statistics
     */
    public function getZones($filters = [])
    {
        $this->db->select('
            z.*,
            COUNT(DISTINCT pl.id) as line_count,
            COUNT(DISTINCT m.id) as machine_count
        ');
        $this->db->from($this->table . ' z');
        $this->db->join('production_lines pl', 'z.zone_id = pl.zone_id', 'left');
        $this->db->join('machines m', 'pl.id = m.line_id', 'left');
        
        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('z.status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('z.zone_code', $filters['search']);
            $this->db->or_like('z.zone_name', $filters['search']);
            $this->db->or_like('z.description', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->group_by('z.zone_id');
        $this->db->order_by('z.zone_code', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Lấy zone theo ID
     * 
     * @param int $zone_id Zone ID
     * @return object|null Zone data
     */
    public function getZoneById($zone_id)
    {
        $this->db->select('
            z.*,
            COUNT(DISTINCT pl.id) as line_count,
            COUNT(DISTINCT m.id) as machine_count
        ');
        $this->db->from($this->table . ' z');
        $this->db->join('production_lines pl', 'z.zone_id = pl.zone_id', 'left');
        $this->db->join('machines m', 'pl.id = m.line_id', 'left');
        $this->db->where('z.zone_id', $zone_id);
        $this->db->group_by('z.zone_id');
        
        return $this->db->get()->row();
    }

    /**
     * Tạo zone mới
     * 
     * @param array $data Zone data
     * @return array Result with success status
     */
    public function createZone($data)
    {
        // Validate zone code uniqueness
        $this->db->where('zone_code', $data['zone_code']);
        if ($this->db->count_all_results($this->table) > 0) {
            return [
                'success' => false,
                'message' => 'Mã khu đã tồn tại'
            ];
        }

        $zone_data = [
            'zone_code' => $data['zone_code'],
            'zone_name' => $data['zone_name'],
            'description' => $data['description'] ?? null,
            'floor' => $data['floor'] ?? null,
            'building' => $data['building'] ?? null,
            'status' => $data['status'] ?? 1,
        ];

        if ($this->db->insert($this->table, $zone_data)) {
            return [
                'success' => true,
                'message' => 'Tạo khu vực thành công',
                'zone_id' => $this->db->insert_id()
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể tạo khu vực'
        ];
    }

    /**
     * Cập nhật zone
     * 
     * @param int $zone_id Zone ID
     * @param array $data Zone data
     * @return array Result with success status
     */
    public function updateZone($zone_id, $data)
    {
        // Check if zone exists
        if (!$this->getZoneById($zone_id)) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ];
        }

        // Validate zone code uniqueness (except current zone)
        $this->db->where('zone_code', $data['zone_code']);
        $this->db->where('zone_id !=', $zone_id);
        if ($this->db->count_all_results($this->table) > 0) {
            return [
                'success' => false,
                'message' => 'Mã khu đã tồn tại'
            ];
        }

        $zone_data = [
            'zone_code' => $data['zone_code'],
            'zone_name' => $data['zone_name'],
            'description' => $data['description'] ?? null,
            'floor' => $data['floor'] ?? null,
            'building' => $data['building'] ?? null,
            'status' => $data['status'] ?? 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('zone_id', $zone_id);
        if ($this->db->update($this->table, $zone_data)) {
            return [
                'success' => true,
                'message' => 'Cập nhật khu vực thành công'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể cập nhật khu vực'
        ];
    }

    /**
     * Xóa zone
     * 
     * @param int $zone_id Zone ID
     * @return array Result with success status
     */
    public function deleteZone($zone_id)
    {
        $zone = $this->getZoneById($zone_id);
        
        if (!$zone) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ];
        }

        // Check if zone has production lines
        if ($zone->line_count > 0) {
            return [
                'success' => false,
                'message' => 'Không thể xóa khu vực đang có dây chuyền. Vui lòng di chuyển hoặc xóa các dây chuyền trước.'
            ];
        }

        $this->db->where('zone_id', $zone_id);
        if ($this->db->delete($this->table)) {
            return [
                'success' => true,
                'message' => 'Xóa khu vực thành công'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể xóa khu vực'
        ];
    }

    /**
     * Lấy production lines của zone
     * 
     * @param int $zone_id Zone ID
     * @return array List of production lines
     */
    public function getZoneLines($zone_id)
    {
        $this->db->select('
            pl.*,
            COUNT(m.id) as machine_count
        ');
        $this->db->from('production_lines pl');
        $this->db->join('machines m', 'pl.id = m.line_id', 'left');
        $this->db->where('pl.zone_id', $zone_id);
        $this->db->group_by('pl.id');
        $this->db->order_by('pl.line_code', 'ASC');
        
        return $this->db->get()->result();
    }
}
