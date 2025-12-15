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


    
    
    
    
    
    
    
    
    
    
    
    
}

