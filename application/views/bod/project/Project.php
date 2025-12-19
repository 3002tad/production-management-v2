<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <!-- Card Header -->
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-8 d-flex align-items-center">
                            <i class="material-icons text-white opacity-10 me-2">task</i>
                            <h6 class="text-white mb-0">Danh sách Đơn hàng</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= site_url('BOD/project/addproject'); ?>" 
                               class="btn bg-gradient-light mb-0">
                                <i class="material-icons opacity-10">add</i>
                                Tạo đơn hàng mới
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body px-0 pb-2">
                <!-- Filters for Orders -->
                <div class="px-4 mb-3">
                    <form class="row g-2 align-items-end" method="GET" action="<?= site_url('BOD/project'); ?>">
                        <div class="col-md-3">
                            <label class="form-label text-sm">Tìm kiếm</label>
                            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Mã đơn / Khách hàng / Sản phẩm" value="<?= htmlspecialchars($filters['keyword'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-sm">Sản phẩm</label>
                            <select name="product_id" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <?php foreach ($products ?? [] as $p): ?>
                                    <option value="<?= $p->id_product; ?>" <?= (isset($filters['product_id']) && $filters['product_id']==$p->id_product) ? 'selected' : ''; ?>><?= htmlspecialchars($p->product_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-sm">Khách hàng</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <?php foreach ($customers ?? [] as $c): ?>
                                    <option value="<?= $c->id_cust; ?>" <?= (isset($filters['customer_id']) && $filters['customer_id']==$c->id_cust) ? 'selected' : ''; ?>><?= htmlspecialchars($c->cust_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-sm">Trạng thái</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <option value="0" <?= (isset($filters['status']) && $filters['status'] === '0') ? 'selected' : ''; ?>>Hủy</option>
                                <option value="1" <?= (isset($filters['status']) && $filters['status'] === '1') ? 'selected' : ''; ?>>Đã duyệt</option>
                                <option value="2" <?= (isset($filters['status']) && $filters['status'] === '2') ? 'selected' : ''; ?>>Đang sản xuất</option>
                                <option value="3" <?= (isset($filters['status']) && $filters['status'] === '3') ? 'selected' : ''; ?>>Đã sản xuất</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-sm">Từ ngày</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_from'] ?? ''); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-sm">Đến ngày</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_to'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                            <a href="<?= site_url('BOD/project'); ?>" class="btn btn-sm btn-outline-secondary">Xóa</a>
                        </div>
                    </form>

                    <!-- Active filters badges -->
                    <div class="mt-2">
                        <?php if (!empty($filters)): ?>
                            <?php
                                $filter_labels = [
                                    'keyword' => 'Tìm',
                                    'product_id' => 'Sản phẩm',
                                    'customer_id' => 'Khách hàng',
                                    'status' => 'Trạng thái',
                                    'date_from' => 'Từ',
                                    'date_to' => 'Đến'
                                ];
                                $status_map = ['0' => 'Hủy', '1' => 'Đã duyệt', '2' => 'Đang sản xuất', '3' => 'Hoàn thành'];
                                $current_query = $_GET;
                            ?>
                            <?php foreach ($filters as $k=>$v): if ($v === '' || $v === null) continue; ?>
                                <?php
                                    $label = $filter_labels[$k] ?? $k;
                                    $value = $v;
                                    if ($k === 'product_id') {
                                        $prodArr = array_filter($products ?? [], function($i) use ($v) { return $i->id_product == $v; });
                                        $value = $prodArr ? htmlspecialchars(array_values($prodArr)[0]->product_name) : $v;
                                    }
                                    if ($k === 'customer_id') {
                                        $custArr = array_filter($customers ?? [], function($i) use ($v) { return $i->id_cust == $v; });
                                        $value = $custArr ? htmlspecialchars(array_values($custArr)[0]->cust_name) : $v;
                                    }
                                    if ($k === 'status') $value = $status_map[$v] ?? $v;
                                    $params = $current_query;
                                    unset($params[$k]);
                                    $remove_url = site_url('BOD/project') . (empty($params) ? '' : ('?' . http_build_query($params)));
                                ?>
                                <a href="<?= $remove_url; ?>" class="badge rounded-pill bg-gradient-primary text-white me-1 py-2" style="text-decoration:none;">
                                    <i class="material-icons-round" style="font-size:14px;vertical-align:middle;margin-right:6px;">filter_alt</i>
                                    <?= htmlspecialchars($label . ': ' . $value); ?>
                                    <i class="material-icons-round" style="font-size:14px;vertical-align:middle;margin-left:8px;">close</i>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table id="table" class="table align-items-center justify-content-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mã đơn hàng</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Khách hàng</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sản phẩm</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Đường kính</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Số lượng</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hạn giao</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cảnh báo</th>
                                <th class="text-secondary opacity-7">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($data as $order): ?>

                                    <tr>
                                        <!-- STT -->
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Mã đơn hàng -->
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $order->project_name; ?></p>
                                            <p class="text-xs text-secondary mb-0">ID: <?= $order->id_project; ?></p>
                                        </td>

                                        <!-- Khách hàng -->
                                        <td>
                                            <span class="text-sm font-weight-bold"><?= $order->cust_name; ?></span>
                                        </td>

                                        <!-- Sản phẩm -->
                                        <td>
                                            <span class="text-sm"><?= $order->product_name; ?></span>
                                        </td>

                                        <!-- Đường kính -->
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-secondary"><?= $order->diameter_display; ?></span>
                                        </td>

                                        <!-- Số lượng -->
                                        <td class="align-middle text-center">
                                            <span class="text-sm font-weight-bold"><?= number_format($order->qty_request); ?></span>
                                            <small class="text-muted"> chiếc</small>
                                        </td>

                                        <!-- Hạn giao -->
                                        <td class="align-middle text-center">
                                            <span class="text-xs"><?= date('d/m/Y', strtotime($order->entry_date)); ?></span>
                                        </td>

                                        <!-- Trạng thái duyệt -->
                                        <td class="align-middle text-center text-sm">
                                            <?php
                                                $status = intval($order->pr_status);
                                                switch ($status) {
                                                    case 0:
                                                        echo '<span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>';
                                                        break;
                                                    case 1:
                                                        echo '<span class="badge badge-sm bg-gradient-success">Đã duyệt</span>';
                                                        break;
                                                    case 2:
                                                        echo '<span class="badge badge-sm bg-gradient-info">Đang sản xuất</span>';
                                                        break;
                                                    case 3:
                                                        echo '<span class="badge badge-sm bg-gradient-primary">Đã sản xuất</span>';
                                                        break;
                                                    case 4:
                                                        echo '<span class="badge badge-sm bg-gradient-danger">Hủy</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-sm bg-gradient-secondary">Chờ duyệt</span>';
                                                        break;
                                                }
                                            ?>
                                        </td>

                                        <!-- Warning flag (Cảnh báo công suất, NVL, tồn kho) -->
                                        <td class="align-middle text-center text-sm">
                                            <?php
                                                // Standardized warning name mapping
                                                $warning_name_mapping = [
                                                    'product_not_found' => 'Sản phẩm không tồn tại',
                                                    'bom_missing' => 'BOM thiếu NVL',
                                                    'new_material_shortage' => 'NVL mới cần nhập',
                                                    'material_shortage' => 'NVL không đủ',
                                                    'deadline_overdue' => 'Quá hạn',
                                                    'deadline_too_close' => 'Gần deadline',
                                                    'low_stock_warning' => 'Cần nhập thêm hàng',
                                                    'stock_available' => 'Có sẵn kho',
                                                    'capacity_overload' => 'Vượt công suất',
                                                    'level_2_required' => 'Level 2',
                                                    'level_2_feasible' => 'Level 2 khả thi',
                                                    'ok' => 'Bình thường',
                                                    'normal' => 'Bình thường',
                                                    'capacity_exceeded' => 'Vượt công suất'
                                                ];

                                                // Luôn hiển thị badge dựa trên warning_type
                                                $warning_type = $order->warning_type ?? 'normal';
                                                $badge_text = $warning_name_mapping[$warning_type] ?? 'Bình thường';

                                                // Determine badge color and icon based on warning type
                                                $badge_color = 'success';
                                                $badge_icon = '✓';

                                                if (in_array($warning_type, ['product_not_found', 'bom_missing', 'capacity_overload', 'capacity_exceeded'])) {
                                                    $badge_color = 'danger';
                                                    $badge_icon = '⚠️';
                                                } elseif (in_array($warning_type, ['material_shortage', 'new_material_shortage', 'deadline_too_close', 'level_2_required', 'level_2_feasible'])) {
                                                    $badge_color = 'warning';
                                                    $badge_icon = '⚠️';
                                                } elseif ($warning_type === 'deadline_overdue') {
                                                    $badge_color = 'dark';
                                                    $badge_icon = '⏰';
                                                } elseif ($warning_type === 'stock_available') {
                                                    $badge_color = 'info';
                                                    $badge_icon = '🏪';
                                                }
                                                // 'ok', 'normal', 'low_stock_warning' use default success styling

                                                // Hiển thị badge với tooltip nếu có warning_details
                                                $tooltip = '';
                                                if (!empty($order->warning_details)) {
                                                    $warnings = json_decode($order->warning_details, true);
                                                    if (is_array($warnings)) {
                                                        $detail_lines = [];
                                                        if (isset($warnings['finished_stock_info'])) {
                                                            $detail_lines[] = '🏪 TỒN KHO: ' . $warnings['finished_stock_info'];
                                                        }
                                                        if (isset($warnings['capacity_warning'])) {
                                                            $detail_lines[] = '⚙️ CÔNG SUẤT: ' . $warnings['capacity_warning'];
                                                        }
                                                        if (isset($warnings['material_warning'])) {
                                                            $detail_lines[] = '📦 NGUYÊN VẬT LIỆU: ' . $warnings['material_warning'];
                                                        }
                                                        if (isset($warnings['deadline_warning'])) {
                                                            $detail_lines[] = '⏰ DEADLINE: ' . $warnings['deadline_warning'];
                                                        }
                                                        $tooltip = implode("\n", $detail_lines);
                                                    }
                                                }
?>
                                                <span class="badge badge-sm bg-gradient-<?= $badge_color; ?>" 
                                                  title="<?= htmlspecialchars($tooltip); ?>">
                                                <?= $badge_icon; ?> <?= $badge_text; ?>
                                                </span>
                                                                                    </td>

                                        <!-- Thao tác -->
                                        <td class="align-middle">
                                            <a href="<?= site_url('BOD/project/view/' . $order->id_project); ?>" 
                                               class="text-info font-weight-bold text-xs" 
                                               data-toggle="tooltip" 
                                               data-original-title="Xem chi tiết đơn hàng">
                                                <i class="material-icons opacity-10">visibility</i>
                                            </a>
                                            <a href="<?= site_url('BOD/project/updateproject/' . $order->id_project); ?>" 
                                               class="text-secondary font-weight-bold text-xs ms-2" 
                                               data-toggle="tooltip" 
                                               data-original-title="Sửa đơn hàng">
                                                <i class="material-icons opacity-10">edit</i>
                                            </a>
                                            <a href="javascript:void(0);" 
                                               onclick="confirmDeleteOrder(<?= $order->id_project; ?>, '<?= htmlspecialchars($order->project_name); ?>')" 
                                               class="text-danger font-weight-bold text-xs ms-2" 
                                               data-toggle="tooltip" 
                                               data-original-title="Xóa đơn hàng">
                                                <i class="material-icons opacity-10">delete</i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <p class="text-sm text-secondary mb-0">
                                            <i class="material-icons opacity-10">inbox</i><br>
                                            Chưa có đơn hàng nào
                                        </p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- TOAST NOTIFICATION - Auto-hide sau 3 giây                        -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<style>
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 350px;
    max-width: 500px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    z-index: 9999;
    animation: slideInRight 0.5s ease-out;
    font-family: 'Poppins', sans-serif;
}

.toast-notification.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.toast-notification.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.toast-notification.error {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.toast-notification .toast-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.toast-notification .toast-icon {
    font-size: 32px;
    margin-right: 15px;
}

.toast-notification .toast-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.toast-notification .toast-body {
    font-size: 14px;
    line-height: 1.6;
    margin-top: 10px;
}

.toast-notification .toast-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s;
}

.toast-notification .toast-close:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.toast-notification .toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: rgba(255,255,255,0.5);
    width: 100%;
    border-radius: 0 0 12px 12px;
    animation: progressBar 3s linear forwards;
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}

@keyframes progressBar {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra URL parameter ?msg= và giá trị cụ thể
    var urlParams = new URLSearchParams(window.location.search);
    var msgType = urlParams.get('msg'); // 'success', 'warning_then_success', 'error', etc.
    
    // Kiểm tra sessionStorage để tránh hiển thị lại khi refresh
    var toastShown = sessionStorage.getItem('toast_shown_' + window.location.pathname);
    
    // Chỉ hiển thị toast khi:
    // 1. Có msg parameter trong URL (redirect từ action)
    // 2. Chưa được hiển thị trong session này
    if (msgType) {
        <?php if ($this->session->flashdata('success_js')): ?>
            // Chỉ hiển thị success toast nếu msg=success hoặc msg=warning_then_success
            if (msgType === 'success' || msgType === 'warning_then_success') {
                // Parse dữ liệu từ session
                const successData = <?= $this->session->flashdata('success_js'); ?>;
                
                // Hàm hiển thị success toast
                const showSuccessToast = () => {
const details = [];
                if (successData.project_name) {
                    details.push('📦 Mã đơn hàng: ' + successData.project_name);
                }
                // Chỉ hiển thị cảnh báo nếu THỰC SỰ có risk_flag = 1
                // KHÔNG hiển thị khi chỉ có thông tin tồn kho (warning_flag = 0)
                
                showToast({
                    type: 'success',
                    title: successData.title,
                    message: successData.message,
                    details: details,
                    duration: 3000
                });
                
                sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
                window.history.replaceState({}, document.title, window.location.pathname);
            };
            
                // Nếu có delay (hiển thị sau warning), chờ trước khi hiển thị
                if (successData.delay && successData.delay > 0) {
                    setTimeout(showSuccessToast, successData.delay);
                } else {
                    showSuccessToast();
                }
            }
        <?php endif; ?>

        <?php if ($this->session->flashdata('warning_js')): ?>
            // Chỉ hiển thị warning toast nếu msg=warning hoặc msg=warning_then_success
            if (msgType === 'warning' || msgType === 'warning_then_success') {
                const warningData = <?= $this->session->flashdata('warning_js'); ?>;
                
                // Hiển thị warning toast
                    showToast({
                    type: 'warning',
                    title: warningData.title || 'Cảnh báo!',
                    message: warningData.message,
                    duration: warningData.duration || 6000
                });
                
                sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        <?php endif; ?>

        <?php if ($this->session->flashdata('error_js')): ?>
            // Chỉ hiển thị error toast nếu msg=error
            if (msgType === 'error') {
                const errorData = <?= $this->session->flashdata('error_js'); ?>;
                    showToast({
                    type: 'error',
                    title: 'Lỗi!',
                    message: errorData.message,
                    duration: 5000
                });
                
                sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        <?php endif; ?>
    } // END: if (hasMsg && !toastShown)
    
    // Xóa flag khi navigate sang trang khác (cho phép toast hiện lại lần sau)
    window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('toast_shown_' + window.location.pathname);
    });
});

/**
 * Hiển thị modal chi tiết cảnh báo khi click vào badge
 */
function showWarningDetail(warnings) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    let contentHTML = '';
    
    // Hiển thị thông tin theo warning_type
    if (warnings.warning_type) {
        const warningType = warnings.warning_type;
        let typeTitle = '';
        let typeColor = '';
        let typeIcon = '';
        
        switch (warningType) {
            case 'stock_available':
                typeTitle = 'CÓ SẴN KHO';
                typeColor = '#1565c0';
                typeIcon = '🏪';
                break;
            case 'level_1_feasible':
                typeTitle = 'LEVEL 1 KHẢ THI';
                typeColor = '#2e7d32';
                typeIcon = '✓';
                break;
            case 'level_2_feasible':
                typeTitle = 'LEVEL 2 KHẢ THI';
                typeColor = '#e65100';
                typeIcon = '⚙️';
                break;
            case 'material_shortage':
                typeTitle = 'THIẾU NGUYÊN VẬT LIỆU';
                typeColor = '#c62828';
                typeIcon = '📦';
                break;
            case 'capacity_overload':
                typeTitle = 'VƯỢT CÔNG SUẤT';
                typeColor = '#c62828';
                typeIcon = '🚫';
                break;
            case 'deadline_tight':
                typeTitle = 'DEADLINE GẮP';
                typeColor = '#e65100';
                typeIcon = '⏰';
                break;
            case 'deadline_overdue':
                typeTitle = 'QUÁ DEADLINE';
                typeColor = '#c62828';
                typeIcon = '⏰';
                break;
            case 'bom_missing':
                typeTitle = 'BOM THIẾU NVL';
                typeColor = '#c62828';
                typeIcon = '⚠️';
                break;
            case 'low_stock_warning':
                typeTitle = 'TỒN KHO THẤP';
                typeColor = '#e65100';
                typeIcon = '🏪';
                break;
            default:
                typeTitle = 'THÔNG TIN ĐƠN HÀNG';
                typeColor = '#2e7d32';
                typeIcon = 'ℹ️';
                break;
        }
        
        contentHTML += `
            <div style="margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid ${typeColor};">
                <strong style="color: ${typeColor}; font-size: 16px;">${typeIcon} ${typeTitle}</strong>
            </div>
        `;
    }
    
    // Thông tin chung (cho badge OK)
    if (warnings.general_info) {
        contentHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: #e3f2fd; border-radius: 8px;">
                <strong style="color: #1565c0;">ℹ️ THÔNG TIN CHUNG</strong><br>
                <span style="font-size: 14px;">${warnings.general_info}</span>
            </div>
        `;
    }
    
    // Trạng thái (cho badge OK)
    if (warnings.status) {
        contentHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: #f3e5f5; border-radius: 8px;">
                <strong style="color: #6a1b9a;">📌 TRẠNG THÁI</strong><br>
                <span style="font-size: 14px;">${warnings.status}</span>
            </div>
        `;
    }
    
    // Tồn kho thành phẩm
    if (warnings.finished_stock_info) {
        contentHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: #e8f5e9; border-radius: 8px;">
                <strong style="color: #2e7d32;">🏪 TỒN KHO THÀNH PHẨM</strong><br>
                <span style="font-size: 14px;">${warnings.finished_stock_info}</span>
            </div>
        `;
    }
    
    // Tính toán và hiển thị tồn kho chi tiết
    if (warnings.material_shifts_available !== undefined || warnings.finished_stock_available !== undefined) {
        let stockDetailHTML = '';
        
        // Tồn NVL đủ cho X ca sản xuất
        if (warnings.material_shifts_available !== undefined && warnings.material_shifts_available > 0) {
            const shifts = warnings.material_shifts_available;
            const days = Math.ceil(shifts / 2); // Giả sử 2 ca/ngày
            stockDetailHTML += `
                <div style="margin-bottom: 10px; padding: 8px; background: #e3f2fd; border-radius: 6px;">
                    <strong style="color: #1565c0;">📦 Tồn NVL đủ cho ${shifts} ca sản xuất</strong>
                    <br><span style="font-size: 12px; color: #666;">(~${days} ngày với 2 ca/ngày)</span>
                </div>
            `;
        }
        
        // Tồn thành phẩm: Y sp (đủ giao Z ngày)
        if (warnings.finished_stock_available !== undefined) {
            const availableStock = warnings.finished_stock_available;
            const dailyDemand = 1000; // Giả sử nhu cầu trung bình 1000 sp/ngày, có thể điều chỉnh
            const daysCovered = availableStock > 0 ? Math.floor(availableStock / dailyDemand) : 0;
            
            if (availableStock > 0) {
                stockDetailHTML += `
                    <div style="margin-bottom: 10px; padding: 8px; background: #e8f5e9; border-radius: 6px;">
                        <strong style="color: #2e7d32;">🏭 Tồn thành phẩm: ${availableStock.toLocaleString()} sp</strong>
                        <br><span style="font-size: 12px; color: #666;">(đủ giao ${daysCovered} ngày với nhu cầu ${dailyDemand.toLocaleString()} sp/ngày)</span>
                    </div>
                `;
            }
        }
        
        if (stockDetailHTML) {
            contentHTML += `
                <div style="margin-bottom: 15px; padding: 10px; background: #fff3e0; border-radius: 8px;">
                    <strong style="color: #e65100;">📊 TÍNH TOÁN TỒN KHO CHI TIẾT</strong><br>
                    ${stockDetailHTML}
                </div>
            `;
        }
    }
    
    // Công suất - HIỂN THỊ RÕ SỐ SẢN PHẨM/CA
    if (warnings.capacity_info) {
        const capInfo = warnings.capacity_info;
        const level = capInfo.level || 1;
        const productsPerShift1 = capInfo.products_per_shift_level1 || 3200;
        const productsPerShift2 = capInfo.products_per_shift_level2 || 5100;
        const machineCapacity = capInfo.total_machine_capacity || 500;
        
        let capHTML = '';
        let bgColor = '#e3f2fd';
        let textColor = '#1565c0';
        
        if (level == 0) {
            // Không cần sản xuất
            capHTML = `
                <div style="margin-bottom: 15px; padding: 10px; background: #e8f5e9; border-radius: 8px;">
                    <strong style="color: #2e7d32;">⚙️ CÔNG SUẤT SẢN XUẤT</strong><br>
                    <span style="font-size: 14px;">✓ Không cần sản xuất (đủ tồn kho)</span>
                </div>
            `;
        } else if (level == 1) {
            // Level 1
            bgColor = '#e8f5e9';
            textColor = '#2e7d32';
            capHTML = `
                <div style="margin-bottom: 15px; padding: 10px; background: ${bgColor}; border-radius: 8px;">
                    <strong style="color: ${textColor};">⚙️ CÔNG SUẤT SẢN XUẤT</strong><br>
                    <span style="font-size: 14px;">✓ Level 1: 8 giờ × 2 ca/ngày</span><br>
                    <span style="font-size: 13px; color: #666;">
                        → Công suất máy: ${machineCapacity} sp/h<br>
                        → Sản phẩm/ca: <strong>${productsPerShift1.toLocaleString()} cái</strong> (${machineCapacity} × 8h × 80%)<br>
                        ${warnings.estimated_shifts ? `→ Cần ${warnings.estimated_shifts} ca (~${warnings.estimated_days || 'N/A'} ngày)` : ''}
                    </span>
                </div>
            `;
        } else if (level == 2) {
            // Level 2
            bgColor = '#fff3e0';
            textColor = '#e65100';
            capHTML = `
                <div style="margin-bottom: 15px; padding: 10px; background: ${bgColor}; border-radius: 8px;">
                    <strong style="color: ${textColor};">⚠️ CÔNG SUẤT SẢN XUẤT</strong><br>
                    <span style="font-size: 14px;">${warnings.capacity_warning || 'Vượt Level 1, chuyển sang Level 2'}</span><br>
                    <span style="font-size: 13px; color: #666;">
                        → Công suất máy: ${machineCapacity} sp/h<br>
                        → Level 1: ${productsPerShift1.toLocaleString()} cái/ca (${machineCapacity} × 8h × 80%)<br>
                        → Level 2: <strong>${productsPerShift2.toLocaleString()} cái/ca</strong> (${machineCapacity} × 12h × 85%)<br>
                        ${warnings.estimated_shifts ? `→ Cần ${warnings.estimated_shifts} ca Level 2 (~${warnings.estimated_days || 'N/A'} ngày)` : ''}
                    </span>
                </div>
            `;
        }
        
        contentHTML += capHTML;
    } else if (warnings.capacity_warning) {
        // Fallback cho trường hợp chỉ có capacity_warning (không có capacity_info)
        contentHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: #fff3e0; border-radius: 8px;">
                <strong style="color: #e65100;">⚙️ CÔNG SUẤT SẢN XUẤT</strong><br>
                <span style="font-size: 14px;">${warnings.capacity_warning}</span>
                ${warnings.estimated_shifts ? `<br><span style="font-size: 13px; color: #666;">→ Cần ${warnings.estimated_shifts} ca (~${warnings.estimated_days || 'N/A'} ngày)</span>` : ''}
            </div>
        `;
    }
    
    // HƯỚNG 2: BOM thiếu NVL (ƯU TIÊN CAO - hiển thị đầu tiên)
    if (warnings.missing_materials_warning) {
        let missingList = '';
        if (warnings.missing_materials_list) {
            const materials = warnings.missing_materials_list.split(', ');
            missingList = materials.map(m => `<br><span style="font-size: 13px; color: #666;">   → ${m}</span>`).join('');
        }
        contentHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: #fff3e0; border-radius: 8px; border-left: 4px solid #f57c00;">
                <strong style="color: #e65100;">⚠️ BOM THIẾU NVL</strong><br>
                <span style="font-size: 14px;">${warnings.missing_materials_warning}</span>
                ${missingList}
            </div>
        `;
    }
    
    // Nguyên vật liệu - CHI TIẾT TỪNG NVL
    if (warnings.material_warning || warnings.material_status || warnings.material_details) {
        let materialHTML = '';
        let bgColor = '#e8f5e9'; // Mặc định xanh (đủ)
        let textColor = '#2e7d32';
        let icon = '📦';
        
        // Kiểm tra có thiếu NVL không
        if (warnings.material_warning) {
            bgColor = '#ffebee'; // Đỏ (thiếu)
            textColor = '#c62828';
        }
        
        materialHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: ${bgColor}; border-radius: 8px;">
                <strong style="color: ${textColor};">${icon} NGUYÊN VẬT LIỆU</strong><br>
        `;
        
        // Hiển thị chi tiết từng NVL
        if (warnings.material_details && warnings.material_details.length > 0) {
            materialHTML += `<div style="margin-top: 10px; font-size: 13px;">`;
            
            warnings.material_details.forEach(function(mat) {
                let statusIcon = '';
                let statusColor = '';
                let statusText = '';
                
                if (mat.is_bottleneck) {
                    statusIcon = '⚠️';
                    statusColor = '#ff6f00';
                    statusText = '← GIỚI HẠN';
                } else if (!mat.is_sufficient) {
                    statusIcon = '❌';
                    statusColor = '#c62828';
                    statusText = 'THIẾU';
                } else if (mat.shifts_possible < 10) {
                    statusIcon = '⚠️';
                    statusColor = '#f57c00';
                    statusText = 'GẦN HẾT';
                } else {
                    statusIcon = '✓';
                    statusColor = '#2e7d32';
                    statusText = `ĐỦ - CÒN ${mat.shifts_after_order} CA`;
                }
                
                materialHTML += `
                    <div style="margin-bottom: 10px; padding: 8px; background: rgba(255,255,255,0.7); border-radius: 6px; border-left: 4px solid ${statusColor};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-weight: 600; font-size: 14px;">${statusIcon} ${mat.material_name}</span>
                            <span style="color: ${statusColor}; font-weight: bold; font-size: 13px; background: rgba(255,255,255,0.9); padding: 2px 8px; border-radius: 3px;">${statusText}</span>
                        </div>
                        <div style="color: #555; font-size: 13px; line-height: 1.6;">
                `;
                
                // Nếu có BOM (quantity_per_unit), hiển thị chi tiết tính toán
                if (mat.quantity_per_unit) {
                    materialHTML += `
                            <div style="background: #f5f5f5; padding: 6px; border-radius: 4px; margin-bottom: 6px;">
                                <strong>📦 Tồn kho:</strong> ${mat.stock.toLocaleString()} ${mat.uom}<br>
                                <strong>📏 Định mức BOM:</strong> ${mat.quantity_per_unit} ${mat.uom}/sản phẩm<br>
                                <strong>🎯 Cần cho ĐH này:</strong> ${mat.quantity_needed.toLocaleString()} ${mat.uom}
                            </div>
                    `;
                    
                    if (mat.quantity_shortage > 0) {
                        materialHTML += `
                            <div style="background: #ffebee; padding: 6px; border-radius: 4px; border: 1px solid #ef5350; margin-bottom: 6px;">
                                <span style="color: #c62828; font-weight: 600;">⚠️ THIẾU ${mat.quantity_shortage.toLocaleString()} ${mat.uom}</span><br>
                                <span style="font-size: 12px; color: #666;">→ Cần nhập thêm trước khi sản xuất</span>
                            </div>
                        `;
                    } else {
                        materialHTML += `
                            <div style="background: #e8f5e9; padding: 6px; border-radius: 4px; border: 1px solid #66bb6a; margin-bottom: 6px;">
                                <span style="color: #2e7d32; font-weight: 600;">✓ ĐỦ NVL</span><br>
                                <span style="font-size: 12px; color: #555;">→ Sau khi trừ ĐH: còn <strong>${mat.stock_after_order.toLocaleString()} ${mat.uom}</strong></span>
                            </div>
                        `;
                    }
                    
                    materialHTML += `
                            <div style="font-size: 12px; color: #666; padding-top: 4px; border-top: 1px dashed #ddd;">
                                <strong>Công suất sau khi trừ ĐH này:</strong><br>
                    `;
                    
                    // Hiển thị thông minh dựa trên số lượng còn lại
                    if (mat.shifts_after_order >= 1) {
                        // Nếu >= 1 ca, hiển thị số ca
                        materialHTML += `
                                → Còn đủ cho: <strong>${mat.shifts_after_order} ca</strong> (~${mat.days_possible} ngày)<br>
                                → Tương đương: <strong>${mat.products_possible.toLocaleString()} sản phẩm</strong>
                        `;
                    } else if (mat.products_possible > 0) {
                        // Nếu < 1 ca nhưng > 0 sản phẩm
                        const productsAfter = Math.floor(mat.stock_after_order / mat.quantity_per_unit);
                        materialHTML += `
                                → Còn đủ cho: <strong>~${productsAfter.toLocaleString()} sản phẩm nữa</strong><br>
                                → Chưa đủ 1 ca sản xuất (1 ca = 3,200 sp)
                        `;
                    } else {
                        // Hết sạch hoặc thiếu
                        materialHTML += `
                                → <span style="color: #c62828;">Đã hết sau đơn này</span>
                        `;
                    }
                    
                    materialHTML += `
                            </div>
                    `;
                } else {
                    // Không có BOM, chỉ hiển thị ước tính ca
                    materialHTML += `
                            <div style="background: #fff3e0; padding: 6px; border-radius: 4px; margin-bottom: 6px;">
                                <strong>📦 Tồn kho:</strong> ${mat.stock.toLocaleString()} ${mat.uom}<br>
                                <span style="font-size: 12px; color: #e65100;">⚠️ Chưa có định mức BOM - chỉ hiển thị ước tính</span>
                            </div>
                            <div style="font-size: 12px; color: #666; padding-top: 4px; border-top: 1px dashed #ddd;">
                                <strong>Công suất ước tính:</strong><br>
                                → Đủ cho: <strong>${mat.shifts_possible} ca</strong><br>
                                → Sau ĐH: còn <strong>${mat.shifts_after_order} ca</strong> (~${mat.days_possible} ngày)
                            </div>
                    `;
                }
                
                materialHTML += `
                        </div>
                    </div>
                `;
            });
            
            materialHTML += `</div>`;
            
            // Hiển thị NVL thiếu trong BOM (NULL)
            if (warnings.missing_materials_list) {
                const missingMats = warnings.missing_materials_list.split(', ');
                materialHTML += `
                    <div style="margin-top: 10px; padding: 8px; background: #ffebee; border-radius: 4px; font-size: 13px; border-left: 3px solid #c62828;">
                        <strong style="color: #c62828;">⚠️ BOM THIẾU NVL (chưa có trong kho):</strong>
                        <ul style="margin: 5px 0 0 20px; padding: 0;">
                `;
                missingMats.forEach(function(matName) {
                    materialHTML += `<li style="color: #c62828;">${matName}</li>`;
                });
                materialHTML += `
                        </ul>
                        <span style="font-size: 12px; color: #666;">→ Cần bổ sung NVL vào kho trước khi sản xuất</span>
                    </div>
                `;
            }
            
            // Hiển thị tổng kết
            if (warnings.bottleneck_material) {
                materialHTML += `
                    <div style="margin-top: 10px; padding: 8px; background: #fff3e0; border-radius: 4px; font-size: 13px;">
                        <strong style="color: #ff6f00;">⚠️ NVL giới hạn sản xuất:</strong> ${warnings.bottleneck_material}
                        <br><span style="font-size: 12px; color: #666;">→ NVL này sẽ cạn kiệt trước, cần ưu tiên nhập thêm</span>
                    </div>
                `;
            }
        } else {
            // Fallback: Hiển thị warning/status cũ
            if (warnings.material_warning) {
                materialHTML += `<span style="font-size: 14px;">${warnings.material_warning}</span>`;
                if (warnings.material_shortage_details) {
                    materialHTML += `<br><span style="font-size: 13px; color: #666;">→ ${warnings.material_shortage_details}</span>`;
                }
            } else if (warnings.material_status) {
                materialHTML += `<span style="font-size: 14px;">${warnings.material_status}</span>`;
            }
            
            // Hiển thị NVL missing kể cả khi không có material_details
            if (warnings.missing_materials_list) {
                const missingMats = warnings.missing_materials_list.split(', ');
                materialHTML += `
                    <div style="margin-top: 10px; padding: 8px; background: #ffebee; border-radius: 4px; font-size: 13px;">
                        <strong style="color: #c62828;">⚠️ BOM THIẾU NVL:</strong>
                        <ul style="margin: 5px 0 0 20px;">
                `;
                missingMats.forEach(function(matName) {
                    materialHTML += `<li>${matName}</li>`;
                });
                materialHTML += `</ul></div>`;
            }
        }
        
        materialHTML += `</div>`;
        contentHTML += materialHTML;
    }
    
    // Deadline
    if (warnings.deadline_warning) {
        contentHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: #fff3e0; border-radius: 8px;">
                <strong style="color: #e65100;">⏰ DEADLINE</strong><br>
                <span style="font-size: 14px;">${warnings.deadline_warning}</span>
            </div>
        `;
    } else if (warnings.deadline_status) {
        contentHTML += `
            <div style="margin-bottom: 15px; padding: 10px; background: #e3f2fd; border-radius: 8px;">
                <strong style="color: #1565c0;">⏰ DEADLINE</strong><br>
                <span style="font-size: 14px;">${warnings.deadline_status}</span>
            </div>
        `;
    }
    
    modal.id = 'warningDetailModal';
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; padding: 25px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px;">
                <h4 style="margin: 0; color: #333;">📋 CHI TIẾT ĐƠN HÀNG</h4>
                <button onclick="closeWarningModal()" 
                        style="background: #f44336; color: white; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-size: 18px; line-height: 1;">✕</button>
            </div>
            ${contentHTML || '<p style="color: #999; text-align: center;">Không có thông tin chi tiết</p>'}
            <div style="text-align: right; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                <button onclick="closeWarningModal()" 
                        style="background: #2196F3; color: white; border: none; padding: 10px 30px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500;">Đóng</button>
            </div>
        </div>
    `;
    
    // Click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target.id === 'warningDetailModal') {
            closeWarningModal();
        }
    });
    
    document.body.appendChild(modal);
}

/**
 * Đóng modal chi tiết cảnh báo
 */
function closeWarningModal() {
    const modal = document.getElementById('warningDetailModal');
    if (modal) {
        modal.remove();
    }
}

/**
 * Hiển thị toast notification tự động đóng
 * @param {Object} options - {type, title, message, details, duration}
 */
function showToast(options) {
    // Mark global and path-specific toast shown flag to avoid duplicate toasts
    try { sessionStorage.setItem('toast_shown', 'true'); sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true'); } catch(e) { /* ignore */ }

    // Icon theo loại thông báo
    const icons = {
        success: '✅',
        warning: '⚠️',
        error: '❌',
        info: 'ℹ️'
    };

    // Prevent exact duplicate toasts (same type + message)
    try {
        var existingToasts = document.querySelectorAll('.toast-notification');
        for (var i = 0; i < existingToasts.length; i++) {
            var t = existingToasts[i];
            if (t.classList.contains(options.type) && t.innerText && t.innerText.indexOf((options.message||'').trim()) !== -1) {
                return; // duplicate detected, skip
            }
        }
    } catch(e) { /* ignore DOM errors */ }

    // Tạo HTML cho toast
    const toast = document.createElement('div');
    toast.className = `toast-notification ${options.type}`;
    
    let detailsHTML = '';
    if (options.details && options.details.length > 0) {
        detailsHTML = '<div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.3);">';
        options.details.forEach(detail => {
            detailsHTML += `<div style="margin: 5px 0;">${detail}</div>`;
        });
        detailsHTML += '</div>';
    }
    
    toast.innerHTML = `
        <button class="toast-close" onclick="closeToast(this)">✕</button>
        <div class="toast-header">
            <span class="toast-icon">${icons[options.type] || icons.info}</span>
            <h5 class="toast-title">${options.title}</h5>
        </div>
        <div class="toast-body">
            ${options.message}
            ${detailsHTML}
        </div>
        <div class="toast-progress"></div>
    `;
    
    // Thêm vào body
    document.body.appendChild(toast);
    
    // Auto-hide sau duration (mặc định 3 giây)
    const duration = options.duration || 3000;
    setTimeout(() => {
        closeToast(toast);
    }, duration);
}

/**
 * Đóng toast notification
 * @param {Element} element - Toast element hoặc button close
 */
function closeToast(element) {
    const toast = element.classList 
        ? (element.classList.contains('toast-notification') ? element : element.closest('.toast-notification'))
        : element.parentElement.closest('.toast-notification');
    
    if (toast) {
        toast.style.animation = 'slideOutRight 0.5s ease-out forwards';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 500);
    }
}

/**
 * Xác nhận xóa đơn hàng (với validation backend)
 * Kiểm tra validation trước khi hiển thị modal
 * 
 * @param {number} id_project - ID đơn hàng
 * @param {string} project_name - Tên đơn hàng
 */
function confirmDeleteOrder(id_project, project_name) {
    // Kiểm tra validation từ backend trước
    fetch('<?= site_url("BOD/checkCanDeleteOrder/"); ?>' + id_project)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Có thể xóa - Hiển thị modal xác nhận
                showDeleteConfirmModal(id_project, project_name, 'hard');
            } else {
                // Không thể xóa - Hiển thị lý do và đề xuất soft delete
                showCannotDeleteModal(id_project, project_name, data);
            }
        })
        .catch(error => {
            console.error('Error checking delete validation:', error);
            showToast({
                type: 'error',
                title: 'Lỗi hệ thống',
                message: 'Không thể kiểm tra trạng thái đơn hàng',
                duration: 3000
            });
        });
}

/**
 * Hiển thị modal xác nhận xóa (cho đơn hàng trắng)
 */
function showDeleteConfirmModal(id_project, project_name, deleteType) {
    const modal = document.createElement('div');
    modal.id = 'deleteConfirmModal';
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; display: flex;
        align-items: center; justify-content: center;
    `;
    
    const deleteUrl = deleteType === 'soft' 
        ? '<?= site_url("BOD/softDeleteProject/"); ?>' + id_project
        : '<?= site_url("BOD/deleteProject/"); ?>' + id_project;
    
    const actionText = deleteType === 'soft' ? 'HỦY' : 'XÓA';
    const actionColor = deleteType === 'soft' ? 'warning' : 'danger';
    
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="material-icons" style="font-size: 64px; color: #f44336;">warning</i>
                <h4 style="margin: 16px 0 8px; font-family: 'Poppins', sans-serif;">
                    Xác nhận ${actionText.toLowerCase()} đơn hàng
                </h4>
                <p style="margin: 0; color: #666; font-family: 'Poppins', sans-serif;">
                    ${project_name}
                </p>
            </div>
            
            ${deleteType === 'soft' ? `
                <div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    <strong>💡 Lý do (tùy chọn):</strong>
                    <textarea id="deleteReason" class="form-control mt-2" rows="3" 
                              placeholder="VD: Khách hủy đơn, sai thông tin..."></textarea>
                </div>
            ` : `
                <div style="background: #ffebee; border-left: 4px solid #f44336; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    <strong>⚠️ Cảnh báo:</strong> Thao tác này không thể hoàn tác!
                </div>
            `}
            
            <div style="display: flex; gap: 12px;">
                <button onclick="closeDeleteModal()" 
                        class="btn btn-outline-secondary" style="flex: 1; font-family: 'Poppins', sans-serif;">
                    Hủy
                </button>
                <button onclick="executeDelete('${deleteUrl}', '${deleteType}')" 
                        class="btn bg-gradient-${actionColor} text-white" style="flex: 1; font-family: 'Poppins', sans-serif;">
                    ${actionText}
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// DataTables initialization - Wait for jQuery
window.addEventListener('load', function() {
    if (typeof jQuery !== 'undefined') {
        // Guard: only init DataTable if it hasn't been initialised already
        if (!$.fn.DataTable || !$.fn.DataTable.isDataTable || !$.fn.DataTable.isDataTable('#table')) {
            <?php $dt_lang = @file_get_contents(FCPATH . 'asset/Backend/json/Vietnamese.json'); ?>
            $('#table').DataTable({
                "language": <?= $dt_lang ? $dt_lang : json_encode(['url' => base_url('asset/Backend/json/Vietnamese.json')]); ?>,
                "order": [[1, 'asc']],
                "pagingType": "simple_numbers",
                "drawCallback": function(settings) {
                    // Recalculate STT (first column) on each draw so numbering matches visible order
                    var api = this.api();
                    api.column(0, {page:'current'}).nodes().each(function(cell, i) {
                        var $h = cell.querySelector('h6');
                        if ($h) $h.textContent = (i+1);
                        else cell.textContent = (i+1);
                    });
                }
            });
        } else {
            // If already initialised, check if language is English; if so destroy+reinit to apply Vietnamese.
            try {
                var table = $('#table').DataTable();
                var sInfo = table.settings && table.settings()[0] && table.settings()[0].oLanguage && (table.settings()[0].oLanguage.sInfo || table.settings()[0].oLanguage.sLengthMenu);
                var needsReinit = false;
                if (!table.settings()[0].oLanguage) needsReinit = true;
                if (sInfo && (sInfo.indexOf('Showing') !== -1 || sInfo.indexOf('Show') !== -1)) needsReinit = true;
                if (needsReinit) {
                    table.destroy();
                    <?php $dt_lang = @file_get_contents(FCPATH . 'asset/Backend/json/Vietnamese.json'); ?>
                    $('#table').DataTable({
                        "language": <?= $dt_lang ? $dt_lang : json_encode(['url' => base_url('asset/Backend/json/Vietnamese.json')]); ?>,
                        "pageLength": 10,
                        "order": [[1, 'asc']],
                        "pagingType": "simple_numbers",
                        "drawCallback": function(settings) {
                            var api = this.api();
                            api.column(0, {page:'current'}).nodes().each(function(cell, i) {
                                var $h = cell.querySelector('h6');
                                if ($h) $h.textContent = (i+1);
                                else cell.textContent = (i+1);
                            });
                        }
                    });
                } else {
                    table.draw(false);
                }
            } catch (e) {
                console.warn('DataTable draw/reinit failed:', e);
            }
        }

        // Initialize Bootstrap tooltips (for all tooltip attributes)
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

/**
 * Hiển thị modal không thể xóa (đơn hàng đã có sản xuất)
 */
function showCannotDeleteModal(id_project, project_name, validationData) {
    const modal = document.createElement('div');
    modal.id = 'deleteConfirmModal';
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; display: flex;
        align-items: center; justify-content: center;
    `;
    
    let detailsHTML = '';
    if (validationData.details && validationData.details.length > 0) {
        detailsHTML = '<ul style="text-align: left; margin: 10px 0;">';
        validationData.details.forEach(issue => {
            detailsHTML += `<li>${issue.message}</li>`;
        });
        detailsHTML += '</ul>';
    }
    
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; padding: 24px; max-width: 600px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="material-icons" style="font-size: 64px; color: #ff9800;">block</i>
                <h4 style="margin: 16px 0 8px; font-family: 'Poppins', sans-serif;">
                    Không thể xóa đơn hàng
                </h4>
                <p style="margin: 0; color: #666; font-family: 'Poppins', sans-serif;">
                    ${project_name}
                </p>
            </div>
            
            <div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
                <strong>⚠️ Lý do:</strong>
                <p style="margin: 8px 0 0;">${validationData.message}</p>
                ${detailsHTML}
            </div>
            
            <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 16px; margin-bottom: 20px; border-radius: 4px;">
                <strong>💡 Giải pháp:</strong>
                <p style="margin: 8px 0 0;">${validationData.suggestion || 'Bạn có thể đánh dấu đơn hàng là "Đã hủy" để giữ lại lịch sử.'}</p>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button onclick="closeDeleteModal()" 
                        class="btn btn-outline-secondary" style="flex: 1; font-family: 'Poppins', sans-serif;">
                    Đóng
                </button>
                <button onclick="closeDeleteModal(); showDeleteConfirmModal(${id_project}, '${project_name}', 'soft')" 
                        class="btn bg-gradient-warning text-white" style="flex: 1; font-family: 'Poppins', sans-serif;">
                    <i class="material-icons" style="font-size: 16px; vertical-align: middle;">cancel</i>
                    HỦY ĐỐN HÀNG
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

/**
 * Thực hiện xóa hoặc hủy đơn hàng
 */
function executeDelete(url, deleteType) {
    const reason = deleteType === 'soft' ? document.getElementById('deleteReason')?.value : '';
    
    // Nếu là soft delete, gửi POST với lý do
    if (deleteType === 'soft') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    } else {
        // Hard delete - redirect
        window.location.href = url;
    }
}

/**
 * Đóng modal xác nhận xóa
 */
function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        modal.remove();
    }
}
</script>
