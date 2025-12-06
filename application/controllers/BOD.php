<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * BOD Controller - Ban Giám Đốc (Board of Directors)
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
        $this->load->model('PlanModel');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            return;
        }
        
        // RBAC: Check if user has BOD access
        // Allow: BOD (role_name = 'bod', level 100) and System Admin (level >= 90)
        $role_name = $this->session->userdata('role_name');
        $level = $this->session->userdata('level');
        $old_role = $this->session->userdata('role'); // Backward compatibility
        
        $has_access = false;
        
        // New RBAC system
        if ($role_name) {
            $allowed_roles = ['bod', 'system_admin'];
            $has_access = in_array($role_name, $allowed_roles) || ($level >= 90);
        }
        // Old system fallback
        elseif ($old_role === 'admin' || $old_role === 'bod') {
            $has_access = true;
        }
        
        if (!$has_access) {
            show_error('Access Denied - BOD Only', 403, 'Forbidden');
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
     * AJAX endpoint: trả về BOM (materials array) cho một product id
     * GET param: id (id_product) hoặc URI segment 3
     * Response: JSON { success: bool, materials: [...] }
     */
    public function getProductBom()
    {
        $this->load->model('ProductModel');
        $id = $this->input->get('id') ?: $this->uri->segment(3);
        if (empty($id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing product id']);
            return;
        }

        $product = $this->ProductModel->getProductById($id);
        $materials = [];
        if ($product && isset($product->bom_data) && isset($product->bom_data['materials'])) {
            $materials = $product->bom_data['materials'];
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'materials' => $materials], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Quản lý Khách hàng - UC1
     * Pattern: Giống như project() - routing based on URI segments
     */
    public function customer()
    {
        $this->load->model('CustomerModel');
        $this->load->model('OrderModel');
        
        if ($this->uri->segment(3) === 'add') 
        {
            $data = [
                'content' => 'bod/customer/customer_add',
                'navlink' => 'customer',
            ];
        }
        elseif ($this->uri->segment(3) === 'edit') 
        {
            $id = $this->uri->segment(4);
            $customer = $this->CustomerModel->getCustomerById($id);
            if (!$customer) {
                show_404();
            }
            $data = [
                'customer' => $customer,
                'content' => 'bod/customer/customer_edit',
                'navlink' => 'customer',
            ];
        }
        elseif ($this->uri->segment(3) === 'view') 
        {
            $id = $this->uri->segment(4);
            $customer = $this->CustomerModel->getCustomerById($id);
            if (!$customer) {
                show_404();
            }
            $data = [
                'customer' => $customer,
                'orders' => $this->OrderModel->getOrdersByCustomer($id),
                'content' => 'bod/customer/customer_view',
                'navlink' => 'customer',
            ];
        }
        elseif ($this->uri->segment(3) === 'delete') 
        {
            $id = $this->uri->segment(4);
            $customer = $this->CustomerModel->getCustomerById($id);
            if (!$customer) {
                show_404();
            }
            $data = [
                'customer' => $customer,
                'content' => 'bod/customer/customer_delete_confirm',
                'navlink' => 'customer',
            ];
        }
        else 
        {
            $data = [
                'data' => $this->CustomerModel->getAllCustomers(),
                'content' => 'bod/customer/Customer',
                'navlink' => 'customer',
            ];
        }
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Quản lý Sản phẩm - UC2
     * Pattern: Giống như project() - routing based on URI segments
     */
    public function product()
    {
        $this->load->model('ProductModel');
        $this->load->model('OrderModel');
        
        if ($this->uri->segment(3) === 'add') 
        {
            $data = [
                'materials' => $this->ProductModel->getMaterialsList(),
                'diameters' => $this->ProductModel->getDiameters(),
                'content' => 'bod/product/product_add',
                'navlink' => 'product',
            ];
        }
        elseif ($this->uri->segment(3) === 'edit') 
        {
            $id = $this->uri->segment(4);
            $product = $this->ProductModel->getProductById($id);
            if (!$product) {
                show_404();
            }
            $data = [
                'product' => $product,
                'materials' => $this->ProductModel->getMaterialsList(),
                'diameters' => $this->ProductModel->getDiameters(),
                'content' => 'bod/product/product_edit',
                'navlink' => 'product',
            ];
        }
        elseif ($this->uri->segment(3) === 'view') 
        {
            $id = $this->uri->segment(4);
            $product = $this->ProductModel->getProductById($id);
            if (!$product) {
                show_404();
            }
            $data = [
                'product' => $product,
                'orders' => $this->OrderModel->getOrdersByProduct($id),
                'content' => 'bod/product/product_view',
                'navlink' => 'product',
            ];
        }
        elseif ($this->uri->segment(3) === 'delete') 
        {
            $id = $this->uri->segment(4);
            $product = $this->ProductModel->getProductById($id);
            if (!$product) {
                show_404();
            }
            $data = [
                'product' => $product,
                'content' => 'bod/product/product_delete_confirm',
                'navlink' => 'product',
            ];
        }
        else 
        {
            $data = [
                'data' => $this->ProductModel->getAllProducts(),
                'content' => 'bod/product/Product',
                'navlink' => 'product',
            ];
        }
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Store Customer - POST handler for add form
     */
    public function storeCustomer()
    {
        try {
            $this->load->model('CustomerModel');
            
            $cust_name = trim($this->input->post('cust_name'));
            $data = [
                'cust_name' => $cust_name,
                'address'   => trim($this->input->post('address')),
                'telp'      => trim($this->input->post('telp')),
                'email'     => trim($this->input->post('email')),
                'notes'     => trim($this->input->post('notes')),
                'is_active' => 1,
            ];
            
            $result = $this->CustomerModel->addCustomer($data);
            
            if (isset($result['success']) && $result['success'] === true) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Thêm khách hàng thành công',
                    'cust_name' => $cust_name
                ]));
                redirect(site_url('BOD/customer?msg=success'));
            } else {
                throw new Exception($result['message'] ?? 'Không thể thêm khách hàng vào database');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi thêm khách hàng',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/customer/add?msg=error'));
        }
    }

    /**
     * Update Customer - POST handler for edit form
     */
    public function updateCustomer()
    {
        try {
            $this->load->model('CustomerModel');
            
            $id = $this->input->post('id_cust');
            $cust_name = trim($this->input->post('cust_name'));
            $data = [
                'cust_name' => $cust_name,
                'address'   => trim($this->input->post('address')),
                'telp'      => trim($this->input->post('telp')),
                'email'     => trim($this->input->post('email')),
                'notes'     => trim($this->input->post('notes')),
                'is_active' => (int) $this->input->post('is_active'),
            ];
            
            $result = $this->CustomerModel->updateCustomer($id, $data);
            
            if (isset($result['success']) && $result['success'] === true) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Cập nhật khách hàng thành công',
                    'cust_name' => $cust_name
                ]));
                redirect(site_url('BOD/customer?msg=success'));
            } else {
                throw new Exception($result['message'] ?? 'Không thể cập nhật khách hàng');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi cập nhật khách hàng',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/customer/edit/' . $id . '?msg=error'));
        }
    }

    /**
     * Destroy Customer - POST handler for delete
     */
    public function destroyCustomer()
    {
        try {
            $this->load->model('CustomerModel');
            
            $id = $this->input->post('id_cust');
            $cust_name = $this->input->post('cust_name');
            
            $result = $this->CustomerModel->deleteCustomer($id);
            
            if (isset($result['success']) && $result['success'] === true) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Xóa khách hàng thành công',
                    'cust_name' => $cust_name
                ]));
                redirect(site_url('BOD/customer?msg=success'));
            } else {
                throw new Exception($result['message'] ?? 'Không thể xóa khách hàng (Có đơn hàng liên quan)');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi xóa khách hàng',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/customer?msg=error'));
        }
    }

    /**
     * Store Product - POST handler for add form
     */
    public function storeProduct()
    {
        try {
            $this->load->model('ProductModel');
            
            $product_name = trim($this->input->post('product_name'));
            $product_data = [
                'product_name' => $product_name,
                'summary'      => trim($this->input->post('summary')),
                'application'  => trim($this->input->post('application')),
                'diameter'     => trim($this->input->post('diameter')),
                'is_active'    => 1,
            ];
            
            // VALIDATION - Check data before processing BOM
            $validation_result = $this->ProductModel->validateProductData($product_data);
            
            if (!$validation_result['valid']) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => $validation_result['message']
                ]));
                redirect(site_url('BOD/product/add?msg=error'));
                return;
            }
            
            // BOM data - xử lý cả material names và material IDs
            $bom_material_names = $this->input->post('bom_material_names'); // Tên nguyên liệu (text input)
            $bom_material_ids = $this->input->post('bom_materials'); // ID nguyên liệu (hidden input)
            $bom_quantities = $this->input->post('bom_quantities');
            $bom_units = $this->input->post('bom_units');
            
            if ($bom_material_names && is_array($bom_material_names)) {
                $bom_data = [];
                
                foreach ($bom_material_names as $key => $material_name) {
                    $material_name = trim($material_name);
                    if (!empty($material_name)) {
                        $id_material = !empty($bom_material_ids[$key]) ? $bom_material_ids[$key] : null;
                        
                        $bom_data[] = [
                            'id_material' => $id_material,
                            'material_name' => $material_name,
                            'quantity' => floatval($bom_quantities[$key] ?? 0),
                            'unit' => $bom_units[$key] ?? 'g',
                        ];
                    }
                }
                
                // Pass array to model - model will validate and encode to JSON
                $product_data['bom'] = !empty($bom_data) ? $bom_data : null;
            }
            
            $result = $this->ProductModel->addProduct($product_data);
            
            if (isset($result['success']) && $result['success'] === true) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Thêm sản phẩm thành công',
                    'product_name' => $product_name
                ]));
                redirect(site_url('BOD/product?msg=success'));
            } else {
                // Nếu model return error, ném exception để catch block xử lý
                throw new Exception($result['message'] ?? 'Không thể thêm sản phẩm vào database');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi thêm sản phẩm',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/product/add?msg=error'));
        }
    }

    /**
     * Update Product - POST handler for edit form
     */
    public function updateProduct()
    {
        try {
            $this->load->model('ProductModel');
            
            $id = $this->input->post('id_product');
            $product_name = trim($this->input->post('product_name'));
            $product_data = [
                'product_name' => $product_name,
                'summary'      => trim($this->input->post('summary')),
                'application'  => trim($this->input->post('application')),
                'diameter'     => trim($this->input->post('diameter')),
                'is_active'    => (int) $this->input->post('is_active'),
            ];
            
            // VALIDATION - Check data before processing BOM
            $validation_result = $this->ProductModel->validateProductData($product_data, $id);
            
            if (!$validation_result['valid']) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => $validation_result['message']
                ]));
                redirect(site_url('BOD/product/edit/' . $id . '?msg=error'));
                return;
            }
            
            // BOM data - xử lý cả material names và material IDs
            $bom_material_names = $this->input->post('bom_material_names'); // Tên nguyên liệu (text input)
            $bom_material_ids = $this->input->post('bom_materials'); // ID nguyên liệu (hidden input)
            $bom_quantities = $this->input->post('bom_quantities');
            $bom_units = $this->input->post('bom_units');
            
            if ($bom_material_names && is_array($bom_material_names)) {
                $bom_data = [];
                
                foreach ($bom_material_names as $key => $material_name) {
                    $material_name = trim($material_name);
                    if (!empty($material_name)) {
                        $id_material = !empty($bom_material_ids[$key]) ? $bom_material_ids[$key] : null;
                        
                        $bom_data[] = [
                            'id_material' => $id_material,
                            'material_name' => $material_name,
                            'quantity' => floatval($bom_quantities[$key] ?? 0),
                            'unit' => $bom_units[$key] ?? 'g',
                        ];
                    }
                }
                
                // Pass array to model - model will validate and encode to JSON
                $product_data['bom'] = !empty($bom_data) ? $bom_data : null;
            }
            
            $result = $this->ProductModel->updateProduct($id, $product_data);
            
            if (isset($result['success']) && $result['success'] === true) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Cập nhật sản phẩm thành công',
                    'product_name' => $product_name
                ]));
                redirect(site_url('BOD/product?msg=success'));
            } else {
                throw new Exception($result['message'] ?? 'Không thể cập nhật sản phẩm');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi cập nhật sản phẩm',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/product/edit/' . $id . '?msg=error'));
        }
    }

    /**
     * Destroy Product - POST handler for delete
     */
    public function destroyProduct()
    {
        try {
            $this->load->model('ProductModel');
            
            $id = $this->input->post('id_product');
            $product_name = $this->input->post('product_name');
            
            $result = $this->ProductModel->deleteProduct($id);
            
            if (isset($result['success']) && $result['success'] === true) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Xóa sản phẩm thành công',
                    'product_name' => $product_name
                ]));
                redirect(site_url('BOD/product?msg=success'));
            } else {
                throw new Exception($result['message'] ?? 'Không thể xóa sản phẩm (Đang được sử dụng trong đơn hàng)');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi xóa sản phẩm',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/product?msg=error'));
        }
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
            $recommendations = [];
            
            if (!$capacity_check['feasible']) {
                // AF 6.1.1: Cảnh báo
                $risk_flag = 1;
                $warning_message = $capacity_check['message'];
                $recommendations = $capacity_check['recommendations'] ?? [];
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
                // AF 6.1: Nếu có cảnh báo vượt công suất
                if ($risk_flag == 1 && $warning_message) {
                    // Hiển thị CẢ 2 toast: Warning trước → Success sau
                    // 1. Cảnh báo vượt công suất (hiển thị trước)
                    
                    // Tạo đề xuất chi tiết dựa trên recommendations
                    $shortage = $recommendations['shortage'] ?? 0;
                    $shifts_needed = $recommendations['shifts_needed'] ?? 0;
                    
                    $recommendation_text = '<br><br><strong>💡 Đề xuất giải pháp:</strong><br>';
                    $recommendation_text .= sprintf(
                        '• <strong>Phương án 1:</strong> Tăng ca - Cần thêm <strong>%d ca</strong> để sản xuất đủ %s đơn vị<br>',
                        $shifts_needed,
                        number_format($shortage)
                    );
                    $recommendation_text .= sprintf(
                        '• <strong>Phương án 2:</strong> Nhập hàng - Cần nhập thành phẩm <strong>%s đơn vị</strong> để bù thiếu hụt<br>',
                        number_format($shortage)
                    );
                    $recommendation_text .= '• <strong>Phương án 3:</strong> Thương lượng gia hạn với khách hàng';
                    
                    $this->session->set_flashdata('warning_js', json_encode([
                        'title' => '⚠️ Cảnh báo vượt công suất!',
                        'message' => $warning_message . $recommendation_text,
                        'duration' => 3000 // 3 giây (test nhanh)
                    ]));
                    // 2. Thông báo đã lưu thành công (hiển thị sau)
                    $this->session->set_flashdata('success_js', json_encode([
                        'title' => 'Đã lưu đơn hàng!',
                        'message' => 'Đơn hàng "' . $project_name . '" đã được lưu và đánh dấu NGUY CƠ TRỄ HẠN.',
                        'project_name' => $project_name,
                        'risk_flag' => $risk_flag,
                        'delay' => 3500 // 3.5s delay (hiện sau warning 3s)
                    ]));
                    redirect(site_url('BOD/project?msg=warning_then_success'));
                } else {
                    // Bình thường: Chỉ hiển thị success
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

            $risk_flag = !$capacity_check['feasible'] ? 1 : 0;
            $warning_message = null;
            $recommendations = [];
            
            if (!$capacity_check['feasible']) {
                $warning_message = $capacity_check['message'];
                $recommendations = $capacity_check['recommendations'] ?? [];
            }

            $project_name = trim($this->input->post('project_name'));
            
            $update = [
                'project_name'     => $project_name,
                'entry_date'       => trim($this->input->post('entry_date')),
                'id_cust'          => trim($this->input->post('id_cust')),
                'id_product'       => trim($this->input->post('id_product')),
                'diameter'         => trim($this->input->post('diameter')),
                'qty_request'      => trim($this->input->post('qty_request')),
                'risk_flag'        => $risk_flag,
                'customer_request' => trim($this->input->post('customer_request')),
            ];

            $this->crudModel->updateData('project', 'id_project', $id_project, $update);
            
            // Nếu vượt công suất, hiển thị cả warning và success
            if ($risk_flag == 1 && $warning_message) {
                // Tạo đề xuất chi tiết
                $shortage = $recommendations['shortage'] ?? 0;
                $shifts_needed = $recommendations['shifts_needed'] ?? 0;
                
                $recommendation_text = '<br><br><strong>💡 Đề xuất giải pháp:</strong><br>';
                $recommendation_text .= sprintf(
                    '• <strong>Phương án 1:</strong> Tăng ca - Cần thêm <strong>%d ca</strong> để sản xuất đủ %s đơn vị<br>',
                    $shifts_needed,
                    number_format($shortage)
                );
                $recommendation_text .= sprintf(
                    '• <strong>Phương án 2:</strong> Nhập hàng - Cần nhập thành phẩm <strong>%s đơn vị</strong> để bù thiếu hụt<br>',
                    number_format($shortage)
                );
                $recommendation_text .= '• <strong>Phương án 3:</strong> Thương lượng gia hạn với khách hàng';
                
                $this->session->set_flashdata('warning_js', json_encode([
                    'title' => '⚠️ Cảnh báo vượt công suất!',
                    'message' => $warning_message . $recommendation_text,
                    'duration' => 3000 // 3 giây (test nhanh)
                ]));
                
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Đã cập nhật đơn hàng!',
                    'message' => 'Đơn hàng "' . $project_name . '" đã được cập nhật và đánh dấu NGUY CƠ TRỄ HẠN.',
                    'project_name' => $project_name,
                    'risk_flag' => $risk_flag,
                    'delay' => 3500 // 3.5s delay
                ]));
                
                redirect(site_url('BOD/project?msg=warning_then_success'));
            } else {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Cập nhật thành công!',
                    'message' => 'Đơn hàng đã được cập nhật',
                    'project_name' => $project_name,
                    'risk_flag' => $risk_flag
                ]));
                
                redirect(site_url('BOD/project?msg=success'));
            }

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
        $hasError = false;
        
        try {
            $this->crudModel->deleteData('project', 'id_project', $id_project);
            
            $this->session->set_flashdata('success_js', json_encode([
                'title' => 'Xóa thành công!',
                'message' => 'Đơn hàng đã được xóa khỏi hệ thống',
                'project_name' => 'ID: ' . $id_project,
                'risk_flag' => 0
            ]));
        } catch (Exception $e) {
            $hasError = true;
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Không thể xóa đơn hàng',
                'details' => [
                    '⚠️ Có thể đã có kế hoạch sản xuất liên quan',
                    'Lỗi: ' . $e->getMessage()
                ]
            ]));
        }

        redirect(site_url('BOD/project?msg=' . ($hasError ? 'error' : 'success')));
    }







    
    /**
     * Form tạo Kế hoạch sản xuất (BGĐ)
     * Hiển thị form để BGĐ chọn đơn hàng (đã duyệt), nhập target và phân rã line/ca.
     */
    public function createPlan()
    {
        // respect optional GET param `project_id` but normalize via CI input (used for selected flag)
        $selected_project_id = $this->input->get('project_id') ?: null;

        // Lấy danh sách các đơn hàng đã duyệt để lựa chọn
        $this->load->model('PlanModel');
        $orders = $this->PlanModel->getApprovedOrdersForPlanning();

        // Chuẩn hoá trường ngày giao cho mỗi đơn hàng
        // Mục đích: tránh để view phải dò nhiều tên cột khác nhau (entry_date, delivery_date, due_date)
        // Controller sẽ gắn một thuộc tính `delivery_date` chuẩn để view dùng trực tiếp.
        // (Nếu không có ngày giao, giá trị sẽ là null/empty)
        // Normalize delivery date for each order (so view doesn't inspect DB fields)
        foreach ($orders as &$o) {
            $delivery = null;
            if (!empty($o->entry_date)) {
                $delivery = $o->entry_date;
            }
            if (empty($delivery) && !empty($o->delivery_date)) {
                $delivery = $o->delivery_date;
            }
            if (empty($delivery) && !empty($o->due_date)) {
                $delivery = $o->due_date;
            }
            // attach a normalized property used by the view
            $o->delivery_date = $delivery;
            // Compute finished stock for product (if table exists) and suggested target
            $stock_qty = 0;
            if ($this->db->table_exists('finished_stock')) {
                $fs = $this->db->get_where('finished_stock', ['id_product' => $o->id_product])->row();
                if ($fs) {
                    // prefer explicit column `quantity_in_stock` and `id_product` per request
                    if (isset($fs->quantity_in_stock)) {
                        $stock_qty = (int)$fs->quantity_in_stock;
                    } else {
                        // fallback to other possible column names
                        if (isset($fs->quantity)) $stock_qty = (int)$fs->quantity;
                        elseif (isset($fs->qty)) $stock_qty = (int)$fs->qty;
                        elseif (isset($fs->qty_finished)) $stock_qty = (int)$fs->qty_finished;
                        elseif (isset($fs->total_finished)) $stock_qty = (int)$fs->total_finished;
                    }
                }
            }
            // fallback: if finished_report exists with project-level finished, do not count here
            $o->finished_stock_qty = $stock_qty;
            $req = isset($o->qty_request) ? intval($o->qty_request) : 0;
            $o->suggested_target = max(0, $req - $stock_qty);
            // Prepare view-friendly option fields so the view can be dumb/simple
            $opt_value = (string)($o->id_project ?? '');
            $opt_data_qty = $o->suggested_target;
            $opt_data_stock = $o->finished_stock_qty ?? 0;
            $opt_data_delivery = $o->delivery_date ?? '';
            $opt_label = ($o->project_name ?? '') . ' — ' . ($o->product_name ?? '') . ' — Yêu cầu: ' . number_format($o->qty_request ?? 0) . ', Tồn: ' . number_format($opt_data_stock) . ', Mục tiêu: ' . number_format($opt_data_qty);
            $opt_selected = ($selected_project_id !== null && $selected_project_id == ($o->id_project ?? '')) ? true : false;

            $o->opt_value = $opt_value;
            $o->opt_data_qty = $opt_data_qty;
            $o->opt_data_stock = $opt_data_stock;
            $o->opt_data_delivery = $opt_data_delivery;
            $o->opt_label = $opt_label;
            $o->opt_selected = $opt_selected;
        }
        // tránh rò tham chiếu của biến $o sau khi dùng foreach by reference
        // nếu không unset, các vòng foreach tiếp theo có thể có hành vi bất ngờ
        unset($o);

        // Lấy danh sách máy từ bảng `machines` và áp dụng lọc/đặt nhãn
        // Ghi chú: bảng trong database của bạn dùng tên `machines` theo yêu cầu.
        // Filter/label logic sẽ tách trường id/name/code/capacity linh hoạt để tránh lỗi khi schema khác nhau.
        // Fetch machines from the 'machines' table and apply filtering/label logic
        $machines_raw = $this->db->get('machines')->result();
        $machines = [];
        if (!empty($machines_raw)) {
            foreach ($machines_raw as $m) {
                // only include machines with code starting with 'QC'
                if (!isset($m->code) || !preg_match('/^QC/i', $m->code)) {
                    continue;
                }

                // determine status field
                $status = null;
                if (isset($m->mc_status)) {
                    $status = $m->mc_status;
                } elseif (isset($m->status)) {
                    $status = $m->status;
                } elseif (isset($m->mc_stats)) {
                    $status = $m->mc_stats;
                } elseif (isset($m->machine_status)) {
                    $status = $m->machine_status;
                }

                if ($status !== null) {
                    if (is_numeric($status)) {
                        if (intval($status) !== 1) {
                            continue;
                        }
                    } else {
                        $s = strtolower(trim($status));
                        $excluded = ['broken', 'maintenance', 'down', 'unavailable', 'repair'];
                        if (in_array($s, $excluded, true)) {
                            continue;
                        }
                    }
                }

                // ánh xạ tên cột id (linh hoạt với nhiều tên cột khác nhau)
                $mid = null;
                if (isset($m->id_machine)) { $mid = $m->id_machine; }
                elseif (isset($m->id)) { $mid = $m->id; }
                elseif (isset($m->machine_id)) { $mid = $m->machine_id; }
                elseif (isset($m->id_machines)) { $mid = $m->id_machines; }

                // ánh xạ tên cột name/code/capacity với fallback
                $mname = $m->machine_name ?? ($m->name ?? null);
                $mcode = $m->code ?? ($m->mc_code ?? null);
                $cap = isset($m->capacity) ? $m->capacity : (isset($m->cap) ? $m->cap : 0);

                // tạo nhãn hiển thị cho option (ví dụ: 'Dây chuyền 1' hoặc tên máy)
                $label = $mname ?? ('Máy ' . ($mid ?? ''));
                if (!empty($mcode) && preg_match('/^QC[^0-9]*(\d+)/i', $mcode, $matches)) {
                    $num = intval($matches[1]);
                    $label = 'Dây chuyền ' . ($num > 0 ? $num : $matches[1]);
                }

                $machines[] = (object)[
                    'id_machine' => $mid,
                    'machine_name' => $mname,
                    'code' => $mcode,
                    'capacity' => $cap,
                    'label' => $label
                ];
            }
        }

        // Ensure arrays are defined for the view (avoid undefined variable issues)
        $orders = $orders ?: [];
        $machines = $machines ?: [];
        // Load product model to fetch materials list for the view
        $this->load->model('ProductModel');
        $materials = $this->ProductModel->getMaterialsList();
        $materials = $materials ?: [];

        // respect optional GET param `project_id` but normalize via CI input
        $selected_project_id = $this->input->get('project_id') ?: null;

        $data = [
            'orders' => $orders,
            'machines' => $machines,
            'materials' => $materials,
            'selected_project_id' => $selected_project_id,
            'content' => 'bod/planning/PlanCreate', // view sẽ cần tạo (form)
            'navlink' => 'planning',
        ];

        // Build HTML for order options and machine options so view remains dumb
        $order_options_html = '';
        foreach ($orders as $o) {
            $val = htmlspecialchars($o->opt_value ?? ($o->id_project ?? ''), ENT_QUOTES);
            $qty = htmlspecialchars($o->opt_data_qty ?? ($o->suggested_target ?? ($o->qty_request ?? 0)), ENT_QUOTES);
            $stock = htmlspecialchars($o->opt_data_stock ?? ($o->finished_stock_qty ?? 0), ENT_QUOTES);
            $delivery = htmlspecialchars($o->opt_data_delivery ?? ($o->delivery_date ?? ''), ENT_QUOTES);
            // include product id so frontend can lookup BOM for the selected project
            $product_id = htmlspecialchars($o->id_product ?? '', ENT_QUOTES);
            $label = htmlspecialchars($o->opt_label ?? (($o->project_name ?? '') . ' — ' . ($o->product_name ?? '')), ENT_QUOTES);
            $sel = !empty($o->opt_selected) ? ' selected' : '';
            $order_options_html .= "<option value=\"{$val}\" data-qty=\"{$qty}\" data-stock=\"{$stock}\" data-delivery=\"{$delivery}\" data-product-id=\"{$product_id}\"{$sel}>{$label}</option>\n";
        }

        $machine_options_html = '';
        foreach ($machines as $m) {
            $mid = htmlspecialchars($m->id_machine ?? ($m->id ?? ''), ENT_QUOTES);
            $cap = htmlspecialchars($m->capacity ?? 0, ENT_QUOTES);
            $label = htmlspecialchars(($m->label ?? ($m->machine_name ?? ('Máy ' . ($m->id_machine ?? $m->id ?? '')))) . ' (công suất: ' . ($m->capacity ?? 0) . ')', ENT_QUOTES);
            $machine_options_html .= "<option value=\"{$mid}\" data-capacity=\"{$cap}\">{$label}</option>\n";
        }

        // expose option HTML to view
        $data['order_options_html'] = $order_options_html;
        $data['machine_options_html'] = $machine_options_html;

        $this->load->view('bod/vbackend', $data);
    }

    /**
     * POST handler: lưu Kế hoạch sản xuất mới
     * - Dữ liệu mong đợi (từ form): id_project, qty_target, end_date, create_allocate (0/1), lines (JSON)
     */
    public function storePlan()
    {
        try {
            $this->load->model('PlanModel');

            // Đọc dữ liệu form
            // start_date / finish_date: ngày bắt đầu/kết thúc do user nhập (tùy chọn)
            // end_date: hạn giao (có thể do user sửa hoặc autofil từ đơn hàng)
            $id_project = $this->input->post('id_project');
            $qty_target = $this->input->post('qty_target');
            $end_date = $this->input->post('end_date');
            $start_date = $this->input->post('start_date') ?: null;
            $finish_date = $this->input->post('finish_date') ?: null;
            $create_allocate = $this->input->post('create_allocate') ? 1 : 0;
            $auto_approve = $this->input->post('auto_approve') ? 1 : 0;

            // lines có thể được truyền dưới dạng JSON từ frontend
            $lines_json = $this->input->post('lines');
            $lines = [];
            if (!empty($lines_json)) {
                $decoded = json_decode($lines_json, true);
                if (is_array($decoded)) {
                    $lines = $decoded;
                } elseif (is_string($decoded)) {
                    // JSON string like "Dây chuyền 1" -> decoded is string
                    $lines = $decoded;
                } else {
                    // not valid JSON or other type, fallback to raw value
                    $lines = $lines_json;
                }
            }

            // Basic validation
            if (empty($id_project) || empty($qty_target)) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => 'Thiếu thông tin bắt buộc: Đơn hàng hoặc Số lượng'
                ]));
                redirect(site_url('BOD/createPlan?msg=error'));
                return;
            }

            // VALIDATION NGÀY (Server-side)
            // Nếu có `end_date` (hạn giao), thì không cho phép `start_date` hoặc `finish_date` lớn hơn `end_date`.
            // Đồng thời đảm bảo `start_date <= finish_date` nếu cả hai được cung cấp.
            // Những kiểm tra này phòng trường hợp client-side JS bị bypass.
            // Date validations: if delivery (end_date) provided, ensure start/finish are not after it
            if (!empty($end_date)) {
                if (!empty($start_date) && strtotime($start_date) > strtotime($end_date)) {
                    $this->session->set_flashdata('error_js', json_encode([
                        'message' => 'Ngày bắt đầu không được trễ hơn hạn giao'
                    ]));
                    redirect(site_url('BOD/createPlan?msg=error'));
                    return;
                }
                if (!empty($finish_date) && strtotime($finish_date) > strtotime($end_date)) {
                    $this->session->set_flashdata('error_js', json_encode([
                        'message' => 'Ngày kết thúc không được trễ hơn hạn giao'
                    ]));
                    redirect(site_url('BOD/createPlan?msg=error'));
                    return;
                }
            }

            // Ensure start <= finish when both provided
            if (!empty($start_date) && !empty($finish_date) && strtotime($start_date) > strtotime($finish_date)) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => 'Ngày bắt đầu không được sau ngày kết thúc'
                ]));
                redirect(site_url('BOD/createPlan?msg=error'));
                return;
            }

            // Thêm fields materials, note, machine_id và tính số ca đề xuất
            // Process materials: the textarea may contain plain lines or a JSON array.
            $materials_text = $this->input->post('materials');
            $materials = [];
            if (!empty($materials_text)) {
                $decoded = json_decode($materials_text, true);
                if (is_array($decoded)) {
                    $materials = $decoded;
                } else {
                    // split by new line and trim empty lines
                    $lines_mat = preg_split('/\r?\n/', $materials_text);
                    $materials = array_values(array_filter(array_map('trim', $lines_mat), function($v) { return $v !== ''; }));
                }
            }

            $note = trim($this->input->post('note')) ?: null;
            $machine_id = $this->input->post('machine_id') ?: null;

            // Accept suggested_shifts from form if user computed it, otherwise estimate
            $posted_suggested = $this->input->post('suggested_shifts');
            if ($posted_suggested !== null && $posted_suggested !== '') {
                $suggested_shifts = intval($posted_suggested);
            } else {
                $est = $this->PlanModel->estimateShiftsNeeded(intval($qty_target), $machine_id);
                $suggested_shifts = $est['shifts_needed'] ?? 0;
            }

            // plan_name: use user-provided name when present; otherwise auto-generate
            $plan_name_input = trim($this->input->post('plan_name'));
            $plan_name = !empty($plan_name_input) ? $plan_name_input : ('KH-' . $id_project . '-' . time());

            $plan_data = [
                'plan_name' => $plan_name,
                'id_project' => $id_project,
                'qty_target' => intval($qty_target),
                'end_date' => $end_date ?: null,
                'start_date' => $start_date ?: null,
                'finish_date' => $finish_date ?: null,
                'lines' => $lines,
                'create_allocate' => $create_allocate,
                'pl_status' => 0, // Lưu nháp, BGĐ sẽ phê duyệt sau
                'materials' => $materials,
                'note' => $note,
                'machine_id' => $machine_id,
                'suggested_shifts' => $suggested_shifts,
            ];

            $result = $this->PlanModel->createPlan($plan_data);

            if ($result['success']) {
                $id_plan = $result['id_plan'] ?? null;
                if ($auto_approve && $id_plan) {
                    $apr = $this->PlanModel->approvePlan($id_plan);
                    if (isset($apr['success']) && $apr['success'] === true) {
                        $this->session->set_flashdata('success_js', json_encode([
                            'title' => 'Thành công',
                            'message' => 'Kế hoạch đã được lưu và phê duyệt (ID: ' . $id_plan . ')'
                        ]));
                        redirect(site_url('BOD/planning?msg=created_and_approved'));
                    } else {
                        $this->session->set_flashdata('warning_js', json_encode([
                            'title' => 'Lưu thành công (phê duyệt thất bại)',
                            'message' => 'Kế hoạch đã được lưu nhưng không thể phê duyệt: ' . ($apr['message'] ?? '')
                        ]));
                        redirect(site_url('BOD/planning?msg=created_but_approve_failed'));
                    }
                }

                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công',
                    'message' => 'Kế hoạch đã được lưu (ID: ' . $result['id_plan'] . ')'
                ]));
                redirect(site_url('BOD/planning?msg=created'));
            } else {
                throw new Exception($result['message'] ?? 'Lỗi khi lưu kế hoạch');
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi tạo kế hoạch',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/createPlan?msg=error'));
        }
    }

    /**
     * Phê duyệt Kế hoạch (BGĐ) - action thực hiện phê duyệt
     * @param int $id_plan
     */
    public function approvePlan($id_plan = null)
    {
        try {
            $this->load->model('PlanModel');
            $id_plan = $id_plan ?: $this->input->post('id_plan');
            if (empty($id_plan)) {
                throw new Exception('Thiếu id_plan');
            }

            $res = $this->PlanModel->approvePlan($id_plan);
            if ($res['success']) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Phê duyệt',
                    'message' => 'Kế hoạch đã được phê duyệt'
                ]));
            } else {
                throw new Exception($res['message'] ?? 'Không thể phê duyệt kế hoạch');
            }

            redirect(site_url('BOD/planning?msg=approved'));

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi phê duyệt kế hoạch',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/planning?msg=error'));
        }
    }



    /**
     * Kế hoạch sản xuất (View Only - Read Only)
     * BOD chỉ xem, không chỉnh sửa
     */
    public function planning()
    {
        // Hiển thị danh sách ĐƠN HÀNG cho mục planning.
        // Sử dụng bảng `project` làm nguồn chính và LEFT JOIN `planning` để
        // hiển thị cả các đơn hàng chưa có kế hoạch.
        // Use a subquery to join only the latest planning record per project
        $sql = "
            SELECT p.*, pl.id_plan, pl.pl_status, pl.qty_target,
                   prod.product_name, prod.diameter, c.cust_name
            FROM project p
            LEFT JOIN (
                SELECT id_project, MAX(id_plan) AS id_plan
                FROM planning
                GROUP BY id_project
            ) latest ON latest.id_project = p.id_project
            LEFT JOIN planning pl ON pl.id_plan = latest.id_plan
            LEFT JOIN product prod ON p.id_product = prod.id_product
            LEFT JOIN customer c ON p.id_cust = c.id_cust
            ORDER BY p.entry_date DESC, p.id_project DESC
        ";

        $rows = $this->db->query($sql)->result();

        $data = [
            'data' => $rows,
            'content' => 'bod/planning/Planning',
            'navlink' => 'planning',
        ];

        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Danh sách Kế hoạch (plans list)
     * Hiển thị các bản ghi từ bảng `planning` gồm: tên, số lượng, hạn giao, trạng thái
     */
    public function plans()
    {
        $sql = "
            SELECT pl.*, p.project_name
            FROM planning pl
            LEFT JOIN project p ON pl.id_project = p.id_project
            ORDER BY pl.end_date ASC, pl.id_plan DESC
        ";

        $rows = $this->db->query($sql)->result();

        $data = [
            'data' => $rows,
            'content' => 'bod/planning/PlanList',
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

