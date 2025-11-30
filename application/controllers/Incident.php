<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Incident extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
        $this->load->model('IncidentModel', 'incidentModel');
        $this->load->model('CrudModel', 'crudModel');
    }

    // List incidents (basic)
    public function index()
    {
        // Require login
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }

        $data = [];
        $data['incidents'] = $this->incidentModel->get_all(200);
        $data['content'] = 'incident/list';
        $data['navlink'] = 'incident';

        $this->load->view('leader/vbackend', $data);
    }

    // Create incident (handles POST)
    public function create()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }

        if ($this->input->method() === 'post') {
            $user_id = $this->session->userdata('user_id');
            $id_machine = (int) $this->input->post('id_machine');
            $id_planshift = (int) $this->input->post('id_planshift');
            $incident_description = trim($this->input->post('incident_description'));

            if (empty($incident_description)) {
                $this->session->set_flashdata('error', 'Mô tả sự cố không được để trống');
                redirect(site_url('incident/create'));
                return;
            }

            $insert = [
                'user_id' => $user_id,
                'id_machine' => $id_machine,
                'id_planshift' => $id_planshift,
                'incident_description' => $incident_description,
                'status' => 0,
            ];

            $this->incidentModel->add($insert);
            $this->session->set_flashdata('flash', 'Sự cố đã được ghi nhận');
            redirect(site_url('incident'));
        }

        $data = [];
        $data['machines'] = $this->crudModel->getData('machine')->result();
        $data['planshifts'] = $this->crudModel->getData('plan_shift')->result();
        $data['content'] = 'incident/create';
        $data['navlink'] = 'incident';

        $this->load->view('leader/vbackend', $data);
    }

    // View single incident
    public function view($id = null)
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }

        $incident = $this->incidentModel->get_by_id($id);
        if (empty($incident)) {
            show_404();
            return;
        }

        $data = [
            'incident' => $incident,
            'content' => 'incident/view',
            'navlink' => 'incident',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    // Update status or assign (simple)
    public function update($id = null)
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }

        if ($this->input->method() !== 'post') {
            redirect(site_url('incident/view/' . $id));
            return;
        }

        $status = $this->input->post('status');
        $update = [];
        if ($status !== null) {
            $update['status'] = (int) $status;
        }

        $this->incidentModel->update($id, $update);
        $this->session->set_flashdata('flash', 'Cập nhật sự cố thành công');
        redirect(site_url('incident/view/' . $id));
    }
}
