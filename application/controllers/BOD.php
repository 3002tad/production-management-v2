<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * BOD Controller - Ban Giám Đốc (Board of Directors)
 * @author Do Cong Danh
 */
class BOD extends CI_Controller
{
    /**
     * Constructor - Kiểm tra phân quyền
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->model('OrderModel');
        $this->load->model('CustomerModel');
        $this->load->model('ProductModel');
        $this->load->model('PlanModel');
        $this->load->library('session');
        
        if (!$this->session->userdata('user_id')) {
            // Trường hợp AJAX: trả về HTTP 401 JSON thay vì redirect về trang login
            if ($this->input->is_ajax_request() || $this->input->post('ajax') === '1' || $this->input->get('ajax') === '1') {
                log_message('warning', 'BOD::__construct - AJAX request with expired session from ' . $this->input->server('REMOTE_ADDR'));
                $this->output->set_status_header(401)
                             ->set_content_type('application/json')
                             ->set_output(json_encode(['success' => false, 'message' => 'Session expired']));
                exit;
            }
            redirect('login/');
            return;
        }
        
        $role_name = $this->session->userdata('role_name');
        $level = $this->session->userdata('level');
        
        if (!($role_name === 'bod' || $role_name === 'system_admin' || $level >= 90)) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_status_header(403)
                             ->set_content_type('application/json')
                             ->set_output(json_encode(['success' => false, 'message' => 'Access Denied - BOD/Admin Access Required']));
                exit;
            } else {
                show_error('Access Denied - BOD/Admin Access Required', 403, 'Forbidden');
            }
        }
    }

    /**
     * Dashboard - Trang chủ Ban Giám Đốc
     */
    public function index()
    {
        $data = [
            'finished' => $this->db->query('SELECT fr.id_finished, fr.total_finished, fr.fdate, p.project_name, p.qty_request, c.cust_name FROM finished_report fr JOIN project p ON fr.id_project = p.id_project JOIN customer c ON p.id_cust = c.id_cust ORDER BY fr.id_finished DESC LIMIT 10')->result(),
            'sorting' => $this->db->query('SELECT sr.id_sorting, sr.finished, sr.waste, (sr.finished + sr.waste) as qty_output, ps.id_plan, s.staff_name FROM sorting_report sr JOIN plan_shift ps ON sr.id_planshift = ps.id_planshift JOIN staff s ON ps.id_staff = s.id_staff JOIN planning pl ON ps.id_plan = pl.id_plan ORDER BY sr.id_sorting DESC LIMIT 10')->result(),
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
     */
    public function customer($action = 'index', $id = null)
    {
        $data = ['navlink' => 'customer'];
        
        switch ($action) {
            case 'add':
                $data['content'] = 'bod/customer/customer_add';
                break;
            case 'edit':
            case 'view':
            case 'delete':
                $customer = $this->CustomerModel->getCustomerById($id);
                if (!$customer) show_404();
                $data['customer'] = $customer;
                if ($action === 'view') $data['orders'] = $this->OrderModel->getOrdersByCustomer($id);
                $data['content'] = "bod/customer/customer_{$action}" . ($action === 'delete' ? '_confirm' : '');
                break;
            default:
                // Read filters from GET params for customers
                $filters = [];
                $keyword = $this->input->get('keyword', TRUE);
                if ($keyword !== null && $keyword !== '') $filters['keyword'] = $keyword;
                $is_active = $this->input->get('is_active', TRUE);
                if ($is_active !== null && $is_active !== '') $filters['is_active'] = $is_active;
                $min_orders = $this->input->get('min_orders', TRUE);
                if ($min_orders !== null && $min_orders !== '') $filters['min_orders'] = (int)$min_orders;

                $data['filters'] = $filters;
                $data['data'] = $this->CustomerModel->getAllCustomers($filters);
                $data['content'] = 'bod/customer/Customer';
        }
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Quản lý Sản phẩm - UC2
     */
    public function product($action = 'index', $id = null)
    {
        $data = ['navlink' => 'product'];

        switch ($action) {
            case 'add':
                $data['materials'] = $this->ProductModel->getMaterialsList();
                $data['diameters'] = $this->OrderModel->getDiameters();
                $data['content'] = 'bod/product/product_add';
                break;
            case 'edit':
            case 'view':
            case 'delete':
                // Sử dụng phương thức mới để lấy cả thông tin BOM
                $product = $this->ProductModel->getProductByIdWithBom($id);
                if (!$product) show_404();
                // Count related orders for UI and checks
                $product->total_orders = $this->db->where('id_product', $id)->count_all_results('project');
                // Count blocking orders (statuses that prevent editing)
                $product->blocking_orders = $this->db->where('id_product', $id)->where_in('pr_status', [1,2,3])->count_all_results('project');
                // Find other referencing tables (finished_stock, etc.)
                $product->references = $this->ProductModel->findReferences($id);

                // If user requested edit but product has blocking orders, prevent access and notify
                if ($action === 'edit' && $product->blocking_orders > 0) {
                    $this->session->set_flashdata('error_js', json_encode([
                        'message' => 'Không thể sửa sản phẩm vì đã có ' . $product->blocking_orders . ' đơn đã duyệt/đang sản xuất/hoàn thành. Vui lòng hủy/hoàn tất các đơn liên quan hoặc tạo sản phẩm mới.'
                    ]));
                    redirect(site_url('BOD/product') . '?msg=error');
                    return;
                }

                $data['product'] = $product;
                if ($action === 'edit') {
                    $data['materials'] = $this->ProductModel->getMaterialsList();
                    $data['diameters'] = $this->OrderModel->getDiameters();
                }
                if ($action === 'view') $data['orders'] = $this->OrderModel->getOrdersByProduct($id);
                // Use delete confirmation variant when action === 'delete'
                $data['content'] = "bod/product/product_" . ($action === 'delete' ? 'delete_confirm' : $action);
                break;
            default:
                // Read filters from GET (safe XSS-clean)
                $filters = [];
                $kw = $this->input->get('keyword', TRUE);
                if ($kw) $filters['keyword'] = $kw;
                $has_bom = $this->input->get('has_bom', TRUE);
                if ($has_bom !== null) $filters['has_bom'] = $has_bom;
                $min_orders = $this->input->get('min_orders', TRUE);
                if ($min_orders !== null) $filters['min_orders'] = $min_orders;
                $is_active = $this->input->get('is_active', TRUE);
                if ($is_active !== null) $filters['is_active'] = $is_active;

                $data['filters'] = $filters; // pass back to view for UI
                $data['data'] = $this->ProductModel->getAllProducts($filters);
                $data['content'] = 'bod/product/Product';
        }
        
        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Lưu Khách hàng - Xử lý POST của form thêm
     */
    public function storeCustomer()
    {
        try {
            $data = $this->input->post(null, TRUE); // Lấy tất cả dữ liệu POST và XSS clean
            $result = $this->CustomerModel->addCustomer($data);
            
            if ($result['success']) {
                // Set success flash for toast display on index view
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Thêm khách hàng thành công.',
                    'cust_name' => $data['cust_name'] ?? ''
                ]));
                redirect(site_url('BOD/customer') . '?msg=success');
            } else {
                throw new Exception($result['message'] ?? 'Không thể thêm khách hàng.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi thêm khách hàng', 'details' => [$e->getMessage()]]));
            redirect(site_url('BOD/customer/add') . '?msg=error');
        }
    }

    /**
     * Cập nhật Khách hàng - Xử lý POST của form chỉnh sửa
     */
    public function updateCustomer()
    {
        $id = $this->input->post('id_cust');
        try {
            $data = $this->input->post(null, TRUE);
            $result = $this->CustomerModel->updateCustomer($id, $data);
            
            if ($result['success']) {
                $this->session->set_flashdata('success_js', json_encode(['title' => 'Thành công!', 'message' => 'Cập nhật khách hàng thành công']));
                redirect(site_url('BOD/customer') . '?msg=success');
            } else {
                throw new Exception($result['message'] ?? 'Không thể cập nhật khách hàng.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi cập nhật khách hàng', 'details' => [$e->getMessage()]]));
            redirect(site_url('BOD/customer/edit/' . $id) . '?msg=error');
        }
    }

    /**
     * Xóa Khách hàng - Xử lý POST xóa
     */
    public function destroyCustomer()
    {
        $id = $this->input->post('id_cust');
        $redirect_to = site_url('BOD/customer'); // Default redirect
        $msg_type = '';

        try {
            $result = $this->CustomerModel->deleteCustomer($id);
            if ($result['success']) {
                $this->session->set_flashdata('success_js', json_encode(['title' => 'Thành công!', 'message' => 'Xóa khách hàng thành công']));
                $msg_type = 'success';
            } else {
                // If model returns an error, it will be caught by the outer catch block
                throw new Exception($result['message'] ?? 'Không thể xóa khách hàng (Có đơn hàng liên quan).');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi xóa khách hàng', 'details' => [$e->getMessage()]]));
            $msg_type = 'error';
        }
        redirect($redirect_to . ($msg_type ? '?msg=' . $msg_type : ''));
    }

    /**
     * Lưu Sản phẩm - Xử lý POST của form thêm
     */
    public function storeProduct()
    {
        try {
            // Parse BOM data from form fields (supports both add/edit naming variations)
            $bom_ids = $this->input->post('bom_materials', TRUE) ?? [];
            $bom_names = $this->input->post('bom_material_names', TRUE) ?? [];
            $bom_quantities = $this->input->post('bom_quantities', TRUE) ?? [];
            $bom_uoms = $this->input->post('bom_units', TRUE) ?? $this->input->post('bom_uoms', TRUE) ?? [];

            $materials = [];
            $maxRows = max(count($bom_ids), count($bom_names), count($bom_quantities), count($bom_uoms));
            for ($i = 0; $i < $maxRows; $i++) {
                $id = isset($bom_ids[$i]) ? trim($bom_ids[$i]) : '';
                $name = isset($bom_names[$i]) ? trim($bom_names[$i]) : '';
                $qty = isset($bom_quantities[$i]) ? floatval($bom_quantities[$i]) : 0;
                $uom = isset($bom_uoms[$i]) ? trim($bom_uoms[$i]) : '';

                // Only keep rows with a positive quantity and a name or id
                if ($qty <= 0) continue;
                if ($id === '' && $name === '') continue;

                $materials[] = [
                    'id_material' => $id ?: null,
                    'material_name' => $name ?: null,
                    'quantity_per_unit' => $qty,
                    'uom' => $uom ?: null,
                ];
            }

            // --- VALIDATION: Bắt buộc phải có BOM ---
            if (empty($materials) || !is_array($materials) || count($materials) == 0) {
                throw new Exception('Không thể tạo sản phẩm. Vui lòng định mức Nguyên vật liệu (BOM) với số lượng > 0.');
            }

            // Chuẩn bị dữ liệu sản phẩm
            $productData = [
                'id_product'   => $this->crudModel->generateCode(1, 'id_product', 'product'),
                'product_name' => trim($this->input->post('product_name', TRUE)),
                'summary'      => trim($this->input->post('summary', TRUE)),
                'application'  => trim($this->input->post('application', TRUE)),
                'diameter'     => floatval($this->input->post('diameter', TRUE)),
            ];

            // Server-side validation: ensure required fields are present and valid
            if (empty($productData['product_name']) || strlen($productData['product_name']) > 50) {
                throw new Exception('Tên sản phẩm không hợp lệ (bắt buộc, tối đa 50 ký tự).');
            }
            if (!is_numeric($productData['diameter']) || $productData['diameter'] <= 0 || $productData['diameter'] > 10) {
                throw new Exception('Đường kính không hợp lệ (0.01 - 10 mm).');
            }

            // Gọi phương thức của model để tạo sản phẩm và BOM
            $newProductId = $this->ProductModel->createProductWithBom($productData, $materials);

            if ($newProductId) {
                // Set a descriptive success flash so the product index toast shows correct message
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công!',
                    'message' => 'Thêm sản phẩm thành công.',
                    'product_name' => $productData['product_name'] ?? ''
                ]));
                redirect(site_url('BOD/product') . '?msg=success');
            } else {
                throw new Exception('Lỗi khi lưu vào cơ sở dữ liệu.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi thêm sản phẩm', 'details' => [$e->getMessage()]]));
            redirect(site_url('BOD/product/add') . '?msg=error');
        }
    }

    /**
     * Cập nhật Sản phẩm - Xử lý POST của form chỉnh sửa
     */
    public function updateProduct()
    {
        $productId = $this->input->post('id_product', TRUE);
        try {
            // Server-side block: Do not allow editing products that have approved/active/completed orders
            $blockingCount = $this->db->where('id_product', $productId)->where_in('pr_status', [1,2,3])->count_all_results('project');
            if ($blockingCount > 0) {
                throw new Exception('Không thể cập nhật: đã có ' . $blockingCount . ' đơn đã duyệt/đang sản xuất/hoàn thành sử dụng sản phẩm này.');
            }

            // Parse BOM data from form fields (supports both add/edit naming variations)
            $bom_ids = $this->input->post('bom_materials', TRUE) ?? [];
            $bom_names = $this->input->post('bom_material_names', TRUE) ?? [];
            $bom_quantities = $this->input->post('bom_quantities', TRUE) ?? [];
            $bom_uoms = $this->input->post('bom_units', TRUE) ?? $this->input->post('bom_uoms', TRUE) ?? [];

            $materials = [];
            $maxRows = max(count($bom_ids), count($bom_names), count($bom_quantities), count($bom_uoms));
            for ($i = 0; $i < $maxRows; $i++) {
                $id = isset($bom_ids[$i]) ? trim($bom_ids[$i]) : '';
                $name = isset($bom_names[$i]) ? trim($bom_names[$i]) : '';
                $qty = isset($bom_quantities[$i]) ? floatval($bom_quantities[$i]) : 0;
                $uom = isset($bom_uoms[$i]) ? trim($bom_uoms[$i]) : '';

                // Only keep rows with a positive quantity and a name or id
                if ($qty <= 0) continue;
                if ($id === '' && $name === '') continue;

                $materials[] = [
                    'id_material' => $id ?: null,
                    'material_name' => $name ?: null,
                    'quantity_per_unit' => $qty,
                    'uom' => $uom ?: null,
                ];
            }

            // --- VALIDATION: Bắt buộc phải có BOM ---
            if (empty($materials) || !is_array($materials) || count($materials) == 0) {
                throw new Exception('Không thể cập nhật. Sản phẩm phải có ít nhất một Nguyên vật liệu trong Định mức (BOM) với số lượng > 0.');
            }

            // Chuẩn bị dữ liệu sản phẩm để cập nhật
            $productData = [
                'product_name' => trim($this->input->post('product_name', TRUE)),
                'summary'      => trim($this->input->post('summary', TRUE)),
                'application'  => trim($this->input->post('application', TRUE)),
                'diameter'     => floatval($this->input->post('diameter', TRUE)),
            ];

            // Server-side validation for update
            if (empty($productData['product_name']) || strlen($productData['product_name']) > 50) {
                throw new Exception('Tên sản phẩm không hợp lệ (bắt buộc, tối đa 50 ký tự).');
            }
            if (!is_numeric($productData['diameter']) || $productData['diameter'] <= 0 || $productData['diameter'] > 10) {
                throw new Exception('Đường kính không hợp lệ (0.01 - 10 mm).');
            }

            // Gọi phương thức của model để cập nhật sản phẩm và BOM
            $success = $this->ProductModel->updateProductWithBom($productId, $productData, $materials);

            if ($success) {
                // Product updated successfully. Do not display the background refresh counts in the toast.
                $this->session->set_flashdata('success_js', json_encode(['title' => 'Thành công!', 'message' => 'Cập nhật sản phẩm thành công.']));
                redirect(site_url('BOD/product') . '?msg=success');
            } else {
                throw new Exception('Lỗi khi cập nhật sản phẩm trong cơ sở dữ liệu.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi cập nhật sản phẩm', 'details' => [$e->getMessage()]]));
            redirect(site_url('BOD/product/edit/' . $productId) . '?msg=error');
        }
    }

    /**
     * AJAX endpoint to update customer notes.
     * Expected POST data: id_cust, notes
     */
    public function updateCustomerNotes()
    {
        // Ensure this is an AJAX request for security/API usage clarity
        // Một số proxy/cấu hình server có thể loại bỏ header; chấp nhận tham số POST fallback 'ajax=1'
            if (!$this->input->is_ajax_request() && $this->input->post('ajax') !== '1') {
                // Log useful diagnostics to help debug header/session issues
                $hdr = [
                    'HTTP_X_REQUESTED_WITH' => $this->input->server('HTTP_X_REQUESTED_WITH'),
                    'REMOTE_ADDR' => $this->input->server('REMOTE_ADDR'),
                    'REFERER' => $this->input->server('HTTP_REFERER'),
                    'ajax_post' => $this->input->post('ajax')
                ];
                log_message('warning', 'updateCustomerNotes direct access denied: ' . json_encode($hdr));

                $this->output->set_status_header(403)
                             ->set_content_type('application/json')
                             ->set_output(json_encode(['success' => false, 'message' => 'Direct access denied']));
                return;
        }

        $id_cust = $this->input->post('id_cust', TRUE);
        $notes = $this->input->post('notes', TRUE);

        $req_id = $this->input->server('HTTP_X_REQUEST_ID') ?: uniqid('req_', true);

        // Basic validation
        if (empty($id_cust)) {
            echo json_encode(['success' => false, 'message' => 'Mã khách hàng không hợp lệ.']);
            return;
        }

        try {
            $update_data = ['notes' => $notes];
            // Use specialized method to update only notes (bypass full validation)
            $result = $this->CustomerModel->updateCustomerNotes($id_cust, $notes);

            if ($result['success']) {
                log_message('info', 'Customer notes updated: id_cust=' . $id_cust . ' user=' . $this->session->userdata('user_id') . ' req_id=' . $req_id . ' from ' . $this->input->server('REMOTE_ADDR'));
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(['success' => true, 'message' => 'Cập nhật ghi chú thành công.']));
            } else {
                log_message('warning', 'Customer notes update failed: id_cust=' . $id_cust . ' user=' . $this->session->userdata('user_id') . ' req_id=' . $req_id . ' reason=' . ($result['message'] ?? 'unknown'));
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(['success' => false, 'message' => $result['message'] ?? 'Không thể cập nhật ghi chú.']));
            }
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]));
        }
    }

    /**
     * Xóa Sản phẩm - Xử lý POST xóa
     */
    public function destroyProduct()
    {
        $id = $this->input->post('id_product');
        $redirect_to = site_url('BOD/product'); // Default redirect
        $msg_type = '';

        try {
            $result = $this->ProductModel->deleteProduct($id);
            if ($result['success']) {
                $this->session->set_flashdata('success_js', json_encode(['title' => 'Thành công!', 'message' => 'Xóa sản phẩm thành công.']));
                $msg_type = 'success';
                redirect($redirect_to . '?msg=' . $msg_type);
                return;
            } else {
                // Return the user to the delete confirmation with the model's message so they see exact blockers
                $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi xóa sản phẩm', 'details' => [$result['message']]]));
                // Keep any detailed refs in flash for debugging display (optional)
                if (!empty($result['refs'])) {
                    $this->session->set_flashdata('error_refs', json_encode($result['refs']));
                }
                redirect(site_url('BOD/product/delete/' . $id) . '?msg=error');
                return;
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi xóa sản phẩm', 'details' => [$e->getMessage()]]));
            redirect(site_url('BOD/product/delete/' . $id) . '?msg=error');
            return;
        }
    }

    /**
     * Quản lý Dự án / Đơn hàng
     */
    public function project($action = 'index', $id = null)
    {
        $data = ['navlink' => 'project'];
        
        switch ($action) {
            case 'addproject':
                $data['customer'] = $this->OrderModel->getCustomers();
                $data['product'] = $this->OrderModel->getProducts();
                $data['diameters'] = $this->OrderModel->getDiameters();
                $data['content'] = 'bod/project/AddProject';
                break;
            case 'view':
            case 'updateproject':
            case 'deleteproject':
                $order = $this->OrderModel->getOrderById($id);
                if (!$order) {
                    show_404();
                    exit; // Ensure script stops here.
                }
                $data['order'] = $order;
                                unset($order); // Explicitly unset the local $order variable
                if($action === 'updateproject'){
                    $data['customer'] = $this->OrderModel->getCustomers();
                    $data['product'] = $this->OrderModel->getProducts();
                    $data['diameters'] = $this->OrderModel->getDiameters();
                }
                $data['content'] = "bod/project/" . ($action === 'updateproject' ? 'UpdateProject' : ($action === 'deleteproject' ? 'DeleteProject' : 'project_view'));
                break;
            default:
                // Read filters from GET params and pass to model
                $filters = [];
                $keyword = $this->input->get('keyword', TRUE);
                if ($keyword !== null && $keyword !== '') $filters['keyword'] = $keyword;
                $product_id = $this->input->get('product_id', TRUE);
                if ($product_id !== null && $product_id !== '') $filters['product_id'] = $product_id;
                $customer_id = $this->input->get('customer_id', TRUE);
                if ($customer_id !== null && $customer_id !== '') $filters['customer_id'] = $customer_id;
                $status = $this->input->get('status', TRUE);
                if ($status !== null && $status !== '') $filters['status'] = $status;
                $date_from = $this->input->get('date_from', TRUE);
                if ($date_from !== null && $date_from !== '') $filters['date_from'] = $date_from;
                $date_to = $this->input->get('date_to', TRUE);
                if ($date_to !== null && $date_to !== '') $filters['date_to'] = $date_to;

                $data['filters'] = $filters;
                $data['customers'] = $this->OrderModel->getCustomers();
                $data['products'] = $this->OrderModel->getProducts();
                $data['data'] = $this->OrderModel->getAllOrders($filters);
                $data['content'] = 'bod/project/Project';
        }
        
        $this->load->view('bod/vbackend', $data);
    }
    
    /**
     * Thêm mới một đơn hàng (Project) - ĐÃ TÁI CẤU TRÚC
     */
    public function addProject()
    {
        try {
            $post_data = $this->input->post(null, TRUE);

            // 1. Validate dữ liệu đầu vào
            $validation = $this->OrderModel->validateOrderData($post_data);
            if (!$validation['valid']) {
                 $this->session->set_flashdata('error_js', json_encode(['message' => $validation['message']]));
                 redirect(site_url('BOD/project/addproject') . '?msg=error');
                 return;
            }

            // 2. UC7: Kiểm tra tính khả thi và tạo cảnh báo
            $capacity_check = $this->OrderModel->checkCapacity(
                $post_data['id_product'],
                $post_data['qty_request'],
                $post_data['entry_date']
            );

            if (!$capacity_check['feasible']) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => 'Không thể tạo đơn hàng',
                    'details' => [$capacity_check['message']]
                ]));
                redirect(site_url('BOD/project/addproject'));
                return;
            }

            // 3. Chuẩn bị dữ liệu sạch để đưa vào Model
            // Controller chỉ chịu trách nhiệm thu thập và xác thực dữ liệu người dùng.
            // Mọi logic nghiệp vụ, tính toán kho, tạo tên... đều do Model xử lý.
            $order_data = [
                'id_cust'         => $post_data['id_cust'],
                'id_product'      => $post_data['id_product'],
                'diameter'        => $post_data['diameter'],
                'qty_request'     => $post_data['qty_request'],
                'entry_date'      => $post_data['entry_date'],
                'pr_status'       => $post_data['pr_status'] ?? 1, // Mặc định là "Đã duyệt"
                'customer_request' => $post_data['customer_request'] ?? '',
                'project_name'    => $post_data['project_name'] ?? '', // Model sẽ tự tạo nếu rỗng
                // UC7 fields (use safe defaults)
                'warning_flag'           => $capacity_check['warning_flag'] ?? 0,
                'warning_type'           => $capacity_check['warning_type'] ?? 'ok',
                'warning_details'        => $capacity_check['warning_details'] ?? null,
                'capacity_level_used'    => $capacity_check['capacity_level_used'] ?? null,
                'finished_stock_available' => $capacity_check['finished_stock_available'] ?? 0,
                'material_shifts_available' => $capacity_check['material_shifts_available'] ?? null,
                'stock_allocation'       => $capacity_check['stock_allocation'] ?? null
            ];

            // 4. Gọi phương thức createOrder đã được tối ưu của Model
            $result = $this->OrderModel->createOrder($order_data);

            if ($result['success']) {
                // 5. Re-fetch saved project and set flashdata based on final saved warnings
                $saved_project = $this->OrderModel->getOrderById($result['id_project']);
                $saved_warnings = !empty($saved_project->warning_details) ? json_decode($saved_project->warning_details, true) : [];

                if (!empty($saved_warnings)) {
                    $message = $saved_warnings['deadline_warning'] ?? $saved_warnings['material_warning'] ?? $saved_warnings['finished_stock_info'] ?? ($capacity_check['message'] ?? 'Đơn hàng được tạo với cảnh báo');
                    $this->session->set_flashdata('warning_js', json_encode([
                        'title' => 'Tạo đơn thành công với cảnh báo!',
                        'message' => $message
                    ]));
                    $msg_type = 'warning';
                } else {
                     // Thông báo thành công mang tính thông tin - kèm message về năng lực nếu có
                     $success_message = $capacity_check['message'] ?? 'Đơn hàng đã được tạo và phân bổ kho thành công.';
                     $this->session->set_flashdata('success_js', json_encode([
                         'title' => 'Tạo đơn thành công!',
                         'message' => $success_message
                     ]));
                     $msg_type = 'success';
                }

                redirect(site_url('BOD/project') . '?msg=' . $msg_type);
            } else {
                throw new Exception($result['message']);
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Không thể tạo đơn hàng', 'details' => ['Lỗi: ' . $e->getMessage()]]));
            redirect(site_url('BOD/project/addproject'));
        }
    }

    /**
     * Cập nhật một đơn hàng (Project) - ĐÃ TÁI CẤU TRÚC
     */
    public function updateProject()
    {
        $id_project = $this->input->post('id_project');
        try {
            $post_data = $this->input->post(null, TRUE);
            
            // 1. Validate dữ liệu
            $validation = $this->OrderModel->validateOrderData($post_data);
             if (!$validation['valid']) {
                 $this->session->set_flashdata('error_js', json_encode(['message' => $validation['message']]));
                 redirect(site_url('BOD/project/updateproject/' . $id_project) . '?msg=error');
                 return;
            }

            // 2. Kiểm tra xem đơn hàng có đang được sản xuất không.
            // Nếu đã có báo cáo sản xuất, không cho phép thay đổi các thông tin cốt lõi.
            $has_production = $this->db->where('id_project', $id_project)->count_all_results('finished_report') > 0;
            $current_order = $this->OrderModel->getOrderById($id_project);
            $is_critical_change = (
                $current_order->id_product != $post_data['id_product'] || 
                $current_order->qty_request != $post_data['qty_request'] || 
                $current_order->diameter != $post_data['diameter']
            );

            if ($has_production && $is_critical_change) {
                $this->session->set_flashdata('error_js', json_encode(['message' => 'Không thể sửa đổi thông tin cốt lõi (sản phẩm, số lượng, đường kính) của đơn hàng đã bắt đầu sản xuất.']));
                redirect(site_url('BOD/project/updateproject/' . $id_project) . '?msg=error');
                return;
            }
            
            // 3. UC7: Kiểm tra tính khả thi và tạo cảnh báo (khi có thay đổi ảnh hưởng đến tính khả thi)
            $capacity_check = null;
            $is_feasibility_affected_change = (
                $current_order->id_product != $post_data['id_product'] || 
                $current_order->qty_request != $post_data['qty_request'] || 
                $current_order->diameter != $post_data['diameter'] ||
                $current_order->entry_date != $post_data['entry_date']
            );
            
            if ($is_feasibility_affected_change) {
                $capacity_check = $this->OrderModel->checkCapacity(
                    $post_data['id_product'],
                    $post_data['qty_request'],
                    $post_data['entry_date'],
                    $id_project // Exclude current order from calculation
                );

                if (!$capacity_check['feasible']) {
                    $this->session->set_flashdata('error_js', json_encode([
                        'message' => 'Không thể cập nhật đơn hàng',
                        'details' => [$capacity_check['message']]
                    ]));
                    redirect(site_url('BOD/project/updateproject/' . $id_project));
                    return;
                }
            }
            
            // 4. Chuẩn bị dữ liệu sạch để cập nhật
            $update_data = [
                'project_name'    => $post_data['project_name'],
                'entry_date'      => $post_data['entry_date'],
                'id_cust'         => $post_data['id_cust'],
                'id_product'      => $post_data['id_product'],
                'diameter'        => $post_data['diameter'],
                'qty_request'     => $post_data['qty_request'],
                'pr_status'       => $post_data['pr_status'] ?? 1,
                'customer_request'=> $post_data['customer_request'],
            ];
            
            // 5. Thêm UC7 fields nếu có thay đổi ảnh hưởng đến tính khả thi
            if ($capacity_check) {
                $update_data['warning_flag'] = $capacity_check['warning_flag'] ?? 0;
                $update_data['warning_type'] = $capacity_check['warning_type'] ?? 'ok';
                $update_data['warning_details'] = $capacity_check['warning_details'] ?? null;
                $update_data['capacity_level_used'] = $capacity_check['capacity_level_used'] ?? null;
                $update_data['finished_stock_available'] = $capacity_check['finished_stock_available'] ?? 0;
                $update_data['material_shifts_available'] = $capacity_check['material_shifts_available'] ?? null;
                $update_data['stock_allocation'] = $capacity_check['stock_allocation'] ?? null;
            }
            
            // 6. Gọi phương thức updateOrder đã được tối ưu
            $result = $this->OrderModel->updateOrder($id_project, $update_data);
            
            if ($result['success']) {
                // 7. Re-fetch project and set flashdata based on saved warnings to ensure consistency
                $saved_project = $this->OrderModel->getOrderById($id_project);
                $saved_warnings = !empty($saved_project->warning_details) ? json_decode($saved_project->warning_details, true) : [];

                if (!empty($saved_warnings) || ($saved_project->warning_flag ?? 0) == 1) {
                    $message = $saved_warnings['deadline_warning'] ?? $saved_warnings['material_warning'] ?? $saved_warnings['finished_stock_info'] ?? 'Đơn hàng có cảnh báo sau khi cập nhật.';
                    $this->session->set_flashdata('warning_js', json_encode([
                        'title' => 'Cập nhật thành công với cảnh báo!',
                        'message' => $message
                    ]));
                    $msg_type = 'warning';
                } else {
                    // Use capacity_check message for confirmation if available
                    $success_message = $capacity_check['message'] ?? ('Đơn hàng "' . $post_data['project_name'] . '" đã được cập nhật.');
                    $this->session->set_flashdata('success_js', json_encode([
                        'title' => 'Cập nhật thành công!',
                        'message' => $success_message
                    ]));
                    $msg_type = 'success';
                }

                redirect(site_url('BOD/project') . '?msg=' . $msg_type);
            } else {
                throw new Exception($result['message']);
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Không thể cập nhật đơn hàng', 'details' => ['Lỗi: ' . $e->getMessage()]]));
            redirect(site_url('BOD/project/updateproject/' . $id_project) . '?msg=error');
        }
    }

    /**
     * Xóa một đơn hàng (Project)
     */
    public function deleteProject($id_project)
    {
        $redirect_to = site_url('BOD/project'); // Default redirect
        $msg_type = '';

        try {
            $result = $this->OrderModel->deleteOrder($id_project);
            if (!$result['success']) {
                // Now $result['message'] will contain the detailed DB error message
                throw new Exception($result['message'], 1);
            }
            $this->session->set_flashdata('success_js', json_encode(['title' => 'Xóa thành công!', 'message' => 'Đơn hàng đã được xóa khỏi hệ thống.']));
            $msg_type = 'success';
        } catch (Exception $e) {
             $this->session->set_flashdata('error_js', json_encode(['message' => 'Không thể xóa đơn hàng', 'details' => [$e->getMessage()]]));
             $msg_type = 'error';
        }
        redirect($redirect_to . ($msg_type ? '?msg=' . $msg_type : ''));
    }

    /**
     * AJAX endpoint to check if an order can be hard-deleted.
     * Checks if there are any associated production reports.
     * @param string $id_project
     */
    public function checkCanDeleteOrder($id_project)
    {
        $this->output->set_content_type('application/json');
        
        if (empty($id_project)) {
            echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ.']);
            return;
        }

        $order = $this->OrderModel->getOrderById($id_project);
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng.', 'suggestion' => 'Đơn hàng này có thể đã bị xóa hoặc không tồn tại.']);
            return;
        }

        // Check for associated production reports
        $has_finished_reports = $this->db->where('id_project', $id_project)->count_all_results('finished_report') > 0;
        // You might also check other related tables like 'sorting_report', 'planning', etc.
        // $has_sorting_reports = $this->db->where('id_project', $id_project)->count_all_results('sorting_report') > 0;

        if ($has_finished_reports /* || $has_sorting_reports */) {
            echo json_encode([
                'success' => false,
                'message' => 'Đơn hàng này đã có báo cáo sản xuất liên quan.',
                'details' => [
                    ['message' => 'Không thể xóa vĩnh viễn đơn hàng đã có dữ liệu sản xuất để bảo toàn lịch sử.']
                ],
                'suggestion' => 'Bạn chỉ có thể HỦY đơn hàng này (soft delete) thay vì xóa vĩnh viễn.'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Đơn hàng có thể được xóa vĩnh viễn.'
            ]);
        }
    }

    /**
     * Performs a soft delete (cancel) for an order.
     * Updates pr_status to 4 ('Hủy') and optionally stores a reason.
     * @param string $id_project
     */
    public function softDeleteProject($id_project)
    {
        $reason = $this->input->post('reason', TRUE) ?? 'Không có lý do được cung cấp.';
        $redirect_to = site_url('BOD/project'); // Default redirect
        $msg_type = '';

        try {
            $update_data = [
                'pr_status' => 4, // 'Hủy'
                'cancel_reason' => $reason,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $result = $this->OrderModel->updateOrder($id_project, $update_data); // Use updateOrder to manage inventory reversal

            if ($result['success']) {
                $this->session->set_flashdata('success_js', json_encode(['title' => 'Hủy đơn hàng thành công!', 'message' => 'Đơn hàng đã được đánh dấu là HỦY.']));
                $msg_type = 'success';
            } else {
                throw new Exception($result['message'] ?? 'Không thể hủy đơn hàng.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi hủy đơn hàng', 'details' => [$e->getMessage()]]));
            $msg_type = 'error';
        }
        redirect($redirect_to . ($msg_type ? '?msg=' . $msg_type : ''));
    }

    /**
     * AJAX: Refresh project analysis
     */
    public function refreshProjectAnalysis()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
=======

    
    
    
    
    
    
    
    
    
    
    
    
        $id_project = $this->input->post('id_project', TRUE);
        if (!$id_project) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID đơn hàng']);
            return;
        }

        $result = $this->OrderModel->refreshProjectWarnings($id_project);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * AJAX: Refresh all projects for a product
     */
    public function refreshProductAnalysis()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id_product = $this->input->post('id_product', TRUE);
        if (!$id_product) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
            return;
        }

        $result = $this->OrderModel->refreshProductWarnings($id_product);

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}