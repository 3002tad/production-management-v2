<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Zone Controller
 * Controller quản lý khu vực sản xuất
 * 
 * @package    Production Management
 * @subpackage Controllers/Leader
 * @category   Zone Management
 * @author     Copilot AI
 * @version    1.0
 */
class Zone extends CI_Controller
{
    private $can_edit = false;

    public function __construct()
    {
        parent::__construct();
        
        // Check authentication
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        // Load models
        $this->load->model('leader/ZoneModel');
        
        // Check permissions
        $role = $this->session->userdata('role_name');
        $this->can_edit = in_array($role, ['line_manager', 'admin', 'system_admin']);
    }

    /**
     * Danh sách zones
     */
    public function index()
    {
        $filters = [
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
        ];

        $zones = $this->ZoneModel->getZones($filters);

        $data = [
            'title' => 'Quản lý Khu vực',
            'zones' => $zones,
            'filters' => $filters,
            'can_edit' => $this->can_edit,
            'content' => 'leader/zone/index',
            'navlink' => 'zone',
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * Form tạo zone mới
     */
    public function create()
    {
        if (!$this->can_edit) {
            $this->session->set_flashdata('error', 'Không có quyền tạo khu vực');
            redirect('leader/zone/');
            return;
        }

        $data = [
            'title' => 'Thêm Khu vực Mới',
            'content' => 'leader/zone/create',
            'navlink' => 'zone',
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * Xử lý tạo zone mới
     */
    public function store()
    {
        if (!$this->can_edit) {
            $this->session->set_flashdata('error', 'Không có quyền tạo khu vực');
            redirect('leader/zone/');
            return;
        }

        $zone_data = [
            'zone_code' => $this->input->post('zone_code'),
            'zone_name' => $this->input->post('zone_name'),
            'description' => $this->input->post('description'),
            'floor' => $this->input->post('floor'),
            'building' => $this->input->post('building'),
            'status' => $this->input->post('status', 1),
        ];

        $result = $this->ZoneModel->createZone($zone_data);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('leader/zone/');
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('leader/zone/create');
        }
    }

    /**
     * Xem chi tiết zone
     */
    public function detail($zone_id)
    {
        $zone = $this->ZoneModel->getZoneById($zone_id);
        
        if (!$zone) {
            show_404();
            return;
        }

        $lines = $this->ZoneModel->getZoneLines($zone_id);

        $data = [
            'title' => 'Chi tiết Khu vực: ' . $zone->zone_name,
            'zone' => $zone,
            'lines' => $lines,
            'can_edit' => $this->can_edit,
            'content' => 'leader/zone/detail',
            'navlink' => 'zone',
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * Form chỉnh sửa zone
     */
    public function edit($zone_id)
    {
        if (!$this->can_edit) {
            $this->session->set_flashdata('error', 'Không có quyền chỉnh sửa khu vực');
            redirect('leader/zone/');
            return;
        }

        $zone = $this->ZoneModel->getZoneById($zone_id);
        
        if (!$zone) {
            show_404();
            return;
        }

        $data = [
            'title' => 'Chỉnh sửa Khu vực',
            'zone' => $zone,
            'content' => 'leader/zone/edit',
            'navlink' => 'zone',
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * Xử lý cập nhật zone
     */
    public function update($zone_id)
    {
        if (!$this->can_edit) {
            $this->session->set_flashdata('error', 'Không có quyền chỉnh sửa khu vực');
            redirect('leader/zone/');
            return;
        }

        $zone_data = [
            'zone_code' => $this->input->post('zone_code'),
            'zone_name' => $this->input->post('zone_name'),
            'description' => $this->input->post('description'),
            'floor' => $this->input->post('floor'),
            'building' => $this->input->post('building'),
            'status' => $this->input->post('status', 1),
        ];

        $result = $this->ZoneModel->updateZone($zone_id, $zone_data);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('leader/zone/detail/' . $zone_id);
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('leader/zone/edit/' . $zone_id);
        }
    }

    /**
     * Xóa zone
     */
    public function delete($zone_id)
    {
        if (!$this->can_edit) {
            echo json_encode(['success' => false, 'message' => 'Không có quyền xóa khu vực']);
            return;
        }

        $result = $this->ZoneModel->deleteZone($zone_id);
        echo json_encode($result);
    }
}
