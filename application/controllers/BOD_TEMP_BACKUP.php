<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Bod Controller - Ban Giám Đốc (Board of Directors)
 * 
 * Controller chuyên dụng cho Ban Giám Đốc
 * Xử lý các use case:
 * - Tiếp nhận & Tạo đơn hàng bút bi
 * - Quản lý khách hàng
 * - Quản lý sản phẩm
 * - Phê duyệt kế hoạch sản xuất
 * - Xem báo cáo tổng hợp
 * 
 * @author Do Cong Danh
 * @date 2025-11-02
 */
class BOD extends CI_Controller
{
    /**
     * Constructor - Kiểm tra phân quyền Ban Giám Đốc
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->model('OrderModel');
        $this->load->library('session');
        
        // Kiểm tra phân quyền: Chỉ cho phép role = 'bod' hoặc 'admin'
        $user_role = $this->session->userdata('role');
        if (!in_array($user_role, ['bod', 'admin'])) {
            redirect('login/');
        }
    }

    /**
     * Dashboard - Trang chủ Ban Giám Đốc
     */
    public function index()
    {
        $data = [
            // Đơn hàng hoàn thành - JOIN đúng với cột total_finished
            'finished' => $this->db->query('
                SELECT fr.id_finished, fr.total_finished, fr.fdate,
                       p.project_name, p.qty_request,
                       c.cust_name 
                FROM finished_report fr
                JOIN project p ON fr.id_project = p.id_project
                JOIN customer c ON p.id_cust = c.id_cust
                ORDER BY fr.id_finished DESC
                LIMIT 10
            ')->result(),
            
            // Báo cáo sản xuất - Sử dụng cột đúng: finished + waste
            'sorting' => $this->db->query('
                SELECT sr.id_sorting, sr.finished, sr.waste,
                       (sr.finished + sr.waste) as qty_output,
                       ps.id_plan,
                       s.staff_name
                FROM sorting_report sr
                JOIN plan_shift ps ON sr.id_planshift = ps.id_planshift
                JOIN staff s ON ps.id_staff = s.id_staff
                JOIN planning pl ON ps.id_plan = pl.id_plan
                ORDER BY sr.id_sorting DESC
                LIMIT 10
            ')->result(),

            // Số liệu thống kê
            'project' => $this->crudModel->getData('project')->num_rows(),
            'planning' => $this->crudModel->getData('planning')->num_rows(),
            'plan_shift' => $this->crudModel->getData('plan_shift')->num_rows(),
            'finished_report' => $this->crudModel->getData('finished_report')->num_rows(),

            'content' => 'bod/beranda',
            'navlink' => 'beranda',
        ];

        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Quản lý Dự án / Đơn hàng
     * Use Case: Tiếp nhận & Tạo đơn hàng bút bi
     */
    public function project()
    {
        if ($this->uri->segment(3) === 'addproject') 
        {
            $data = [
                'customer' => $this->OrderModel->getCustomers(),
                'product' => $this->OrderModel->getProducts(),
                'content' => 'bod/project/AddProject',
                'navlink' => 'project',
            ];
        }
        elseif ($this->uri->segment(3) === 'updateproject') 
        {
            $id = $this->uri->segment(4);
            $data = [
                'detail' => $this->OrderModel->getOrderById($id),
                'customer' => $this->OrderModel->getCustomers(),
                'product' => $this->OrderModel->getProducts(),
                'content' => 'bod/project/UpdateProject',
                'navlink' => 'project',
            ];
        }
        elseif ($this->uri->segment(3) === 'deleteproject') 
        {
            $id = $this->uri->segment(4);
            $data = [
                'detail' => $this->OrderModel->getOrderById($id),
                'content' => 'bod/project/DeleteProject',
                'navlink' => 'project',
            ];
        }
        else 
        {
            $data = [
                'data' => $this->OrderModel->getAllOrders(),
                'content' => 'bod/project/Project',
                'navlink' => 'project',
            ];
        }
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Tiếp nhận & Tạo đơn hàng bút bi
     * 
     * Use Case: Tiếp nhận và tạo đơn hàng
     * Basic Flow: Bước 3-8
     * Alternative Flow: 4.1 (Thiếu dữ liệu), 6.1 (Vượt công suất)
     * Exception: 5.1 (Hủy đơn), 5.2 (Lỗi DB)
     * 
     * @return void
     */
    public function addProject()
    {
        try {
            // Basic Flow - Bước 3: Lấy dữ liệu từ form
            $id_cust         = trim($this->input->post('id_cust'));
            $id_product      = trim($this->input->post('id_product'));
            $diameter        = trim($this->input->post('diameter'));
            $qty_request     = trim($this->input->post('qty_request'));
            $entry_date      = trim($this->input->post('entry_date'));
            $customer_request = trim($this->input->post('customer_request'));

            // Basic Flow - Bước 4: VALIDATION
            // Alternative Flow 4.1: Thiếu dữ liệu bắt buộc
            $validation_result = $this->OrderModel->validateOrderData([
                'id_cust'      => $id_cust,
                'id_product'   => $id_product,
                'diameter'     => $diameter,
                'qty_request'  => $qty_request,
                'entry_date'   => $entry_date,
            ]);

            if (!$validation_result['valid']) {
                // AF 4.1.1: Thông báo lỗi
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => $validation_result['message']
                ]));
                // AF 4.1.2: Quay lại bước 3
                redirect(site_url('BOD/project/addproject?msg=error'));
                return;
            }

            // Basic Flow - Bước 6: Kiểm tra năng lực sản xuất
            // Alternative Flow 6.1: Vượt công suất
            $capacity_check = $this->OrderModel->checkCapacity(
                $id_product, 
                $qty_request, 
                $entry_date
            );

            $risk_flag = 0;
            $warning_message = null;
            if (!$capacity_check['feasible']) {
                // AF 6.1.1: Cảnh báo
                $risk_flag = 1;
                $warning_message = $capacity_check['message'];
            }

            // Tạo tên project tự động
            $project_name_input = trim($this->input->post('project_name'));
            if (empty($project_name_input)) {
                $project_name = $this->OrderModel->generateProjectName($id_cust);
            } else {
                $project_name = $project_name_input;
            }

            // Basic Flow - Bước 7: Tạo đơn hàng
            $order_data = [
                'id_project'       => $this->crudModel->generateCode(1, 'id_project', 'project'),
                'project_name'     => $project_name,
                'id_cust'          => $id_cust,
                'id_product'       => $id_product,
                'diameter'         => $diameter,
                'qty_request'      => $qty_request,
                'entry_date'       => $entry_date,
                'pr_status'        => 1,  // Đã duyệt
                'risk_flag'        => $risk_flag,
                'customer_request' => $customer_request,
            ];

            // Bước 7.a.3: Lưu với transaction
            $result = $this->OrderModel->createOrder($order_data);

            if ($result['success']) {
                // Basic Flow - Bước 8: Thông báo thành công
                // Nếu có cảnh báo vượt công suất, hiển thị warning thay vì success
                if ($risk_flag == 1 && $warning_message) {
                    $this->session->set_flashdata('warning_js', json_encode([
                        'title' => 'Đã lưu nhưng có cảnh báo!',
                        'message' => $warning_message,
                        'project_name' => $project_name,
                        'risk_flag' => $risk_flag
                    ]));
                    redirect(site_url('BOD/project?msg=warning'));
                } else {
                    $this->session->set_flashdata('success_js', json_encode([
                        'title' => 'Thành công!',
                        'message' => $result['message'],
                        'project_name' => $project_name,
                        'risk_flag' => $risk_flag
                    ]));
                    redirect(site_url('BOD/project?msg=success'));
                }
            } else {
                throw new Exception($result['message']);
            }

        } catch (Exception $e) {
            // Exception 5.2: Lỗi DB
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Không thể kết nối đến cơ sở dữ liệu',
                'details' => ['Lỗi: ' . $e->getMessage()]
            ]));
            redirect(site_url('BOD/project/addproject?msg=error'));
        }
    }

    /**
     * Cập nhật đơn hàng
     * 
     * Quy tắc nghiệp vụ:
     * - Re-validate toàn bộ dữ liệu
     * - Re-check capacity để cập nhật risk_flag
     * - Giữ nguyên pr_status (không tự động thay đổi trạng thái duyệt)
     */
    public function updateProject()
    {
        try {
            $id_project = $this->input->post('id_project');

            // Validation
            $validation_result = $this->OrderModel->validateOrderData([
                'id_cust'      => $this->input->post('id_cust'),
                'id_product'   => $this->input->post('id_product'),
                'diameter'     => $this->input->post('diameter'),
                'qty_request'  => $this->input->post('qty_request'),
                'entry_date'   => $this->input->post('entry_date'),
            ]);

            if (!$validation_result['valid']) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => $validation_result['message']
                ]));
                redirect(site_url('BOD/project/updateproject/' . $id_project . '?msg=error'));
                return;
            }

            // Re-check capacity
            $capacity_check = $this->OrderModel->checkCapacity(
                $this->input->post('id_product'),
                $this->input->post('qty_request'),
                $this->input->post('entry_date')
            );

            $update = [
                'project_name'     => trim($this->input->post('project_name')),
                'entry_date'       => trim($this->input->post('entry_date')),
                'id_cust'          => trim($this->input->post('id_cust')),
                'id_product'       => trim($this->input->post('id_product')),
                'diameter'         => trim($this->input->post('diameter')),
                'qty_request'      => trim($this->input->post('qty_request')),
                'risk_flag'        => !$capacity_check['feasible'] ? 1 : 0,
                'customer_request' => trim($this->input->post('customer_request')),
            ];

            $this->crudModel->updateData('project', 'id_project', $id_project, $update);
            
            $this->session->set_flashdata('success_js', json_encode([
                'title' => 'Cập nhật thành công!',
                'message' => 'Đơn hàng đã được cập nhật',
                'project_name' => $this->input->post('project_name'),
                'risk_flag' => $update['risk_flag']
            ]));

            redirect(site_url('BOD/project?msg=updated'));

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Không thể cập nhật đơn hàng',
                'details' => ['Lỗi: ' . $e->getMessage()]
            ]));
            redirect(site_url('BOD/project/updateproject/' . $id_project . '?msg=error'));
        }
    }

    /**
     * Xóa đơn hàng
     * 
     * Quy tắc nghiệp vụ:
     * - Chỉ xóa được nếu chưa có planning liên quan (FK constraint)
     * - Hiển thị thông báo lỗi nếu có ràng buộc
     */
    public function deleteProject()
    {
        $id_project = $this->uri->segment(3);
        
        try {
            $this->crudModel->deleteData('project', 'id_project', $id_project);
            
            $this->session->set_flashdata('success_js', json_encode([
                'title' => 'Xóa thành công!',
                'message' => 'Đơn hàng đã được xóa khỏi hệ thống',
                'project_name' => 'ID: ' . $id_project,
                'risk_flag' => 0
            ]));
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Không thể xóa đơn hàng',
                'details' => [
                    '⚠️ Có thể đã có kế hoạch sản xuất liên quan',
                    'Lỗi: ' . $e->getMessage()
                ]
            ]));
        }

        redirect(site_url('BOD/project?msg=action'));
    }

    /**
     * Quản lý Khách hàng
     * TODO: Implement full CRUD for Customer management
     */
    public function customer()
    {
        $data = [
            'data' => $this->crudModel->getData('customer')->result(),
            'content' => 'bod/customer/Customer',
            'navlink' => 'customer',
        ];
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Quản lý Sản phẩm
     * TODO: Implement full CRUD for Product management
     */
    public function product()
    {
        $data = [
            'data' => $this->crudModel->getData('product')->result(),
            'content' => 'bod/product/Product',
            'navlink' => 'product',
        ];
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Kế hoạch sản xuất (View Only - Read Only)
     * BOD chỉ xem, không chỉnh sửa
     */
    public function planning()
    {
        $data = [
            'data' => $this->crudModel->getData('planning')->result(),
            'content' => 'bod/planning/Planning',
            'navlink' => 'planning',
        ];
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Báo cáo tổng hợp
     * TODO: Implement comprehensive reporting dashboard
     */
    public function report()
    {
        $data = [
            'content' => 'bod/report/Report',
            'navlink' => 'report',
        ];
        
        $this->load->view('bod/vbackend', $data);
    }
}
