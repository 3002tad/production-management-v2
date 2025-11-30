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
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nguy cơ</th>
                                <th class="text-secondary opacity-7">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php $i = 1; ?>
                                <?php foreach (array_reverse($data) as $order): ?>
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
                                            <span class="badge badge-sm bg-gradient-secondary"><?= $order->diameter; ?> mm</span>
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
                                            <?php if ($order->pr_status == 1): ?>
                                                <span class="badge badge-sm bg-gradient-success">Đã duyệt</span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Risk flag (AF 6.1.2 - Nguy cơ trễ hạn) -->
                                        <td class="align-middle text-center text-sm">
                                            <?php if (isset($order->risk_flag) && $order->risk_flag == 1): ?>
                                                <span class="badge badge-sm bg-gradient-danger" 
                                                      data-bs-toggle="tooltip" 
                                                      title="Đơn hàng có nguy cơ trễ hạn do vượt công suất">
                                                    ⚠️ Nguy cơ
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-info">✓ OK</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Thao tác -->
                                        <td class="align-middle">
                                            <a href="<?= site_url('BOD/project/updateproject/' . $order->id_project); ?>" 
                                               class="text-secondary font-weight-bold text-xs" 
                                               data-toggle="tooltip" 
                                               data-original-title="Sửa đơn hàng">
                                                <i class="material-icons opacity-10">edit</i>
                                            </a>
                                            <a href="<?= site_url('BOD/project/deleteproject/' . $order->id_project); ?>" 
                                               class="text-secondary font-weight-bold text-xs ms-2" 
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
    if (msgType && !toastShown) {
        <?php if ($this->session->flashdata('success_js')): ?>
            // Chỉ hiển thị success toast nếu msg=success hoặc msg=warning_then_success
            if (msgType === 'success' || msgType === 'warning_then_success') {
                // Parse dữ liệu từ session
                const successData = <?= $this->session->flashdata('success_js'); ?>;
                
                // Hàm hiển thị success toast
                const showSuccessToast = () => {
                showToast({
                    type: 'success',
                    title: successData.title,
                    message: successData.message,
                    details: successData.project_name ? [
                        '📦 Mã đơn hàng: ' + successData.project_name,
                        successData.risk_flag == 1 ? '⚠️ Đã đánh dấu: NGUY CƠ TRỄ HẠN' : ''
                    ].filter(d => d) : [],
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
 * Hiển thị toast notification tự động đóng
 * @param {Object} options - {type, title, message, details, duration}
 */
function showToast(options) {
    // Icon theo loại thông báo
    const icons = {
        success: '✅',
        warning: '⚠️',
        error: '❌',
        info: 'ℹ️'
    };

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
</script>
