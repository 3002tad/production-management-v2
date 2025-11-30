<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC1: Customer Management - INDEX VIEW                                       ║
║  Material Design 3.0 + Bootstrap Grid                                        ║
║  Font: Poppins + Material Icons Round                                        ║
║  CACHE BUSTING VERSION: v<?= time(); ?> - 9 COLUMNS WITH ACTIONS            ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->
<?php 
// FORCE PHP TO RELOAD THIS FILE - DO NOT CACHE!
if (function_exists('opcache_invalidate')) {
    opcache_invalidate(__FILE__, true);
}
?>

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <!-- Card Header với gradient -->
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-6 d-flex align-items-center">
                            <i class="material-icons-round text-white opacity-10 me-2" style="font-size: 24px;">people</i>
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Quản lý Khách hàng</h6>
                        </div>
                        <div class="col-6 text-end">
                            <!-- Search Box -->
                            <form action="<?= site_url('BOD/customer/search'); ?>" method="GET" class="d-inline-block me-2" style="width: 250px;">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="keyword" class="form-control form-control-sm" 
                                           placeholder="Tìm khách hàng..." 
                                           value="<?= isset($keyword) ? htmlspecialchars($keyword) : ''; ?>"
                                           style="font-family: 'Poppins', sans-serif; border-radius: 8px 0 0 8px;">
                                    <button class="btn btn-sm bg-white mb-0" type="submit" style="border-radius: 0 8px 8px 0;">
                                        <i class="material-icons-round text-dark" style="font-size: 18px;">search</i>
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Add Button -->
                            <a href="<?= site_url('BOD/customer/add'); ?>" 
                               class="btn bg-gradient-success mb-0"
                               onclick="window.location.href=this.href; return false;"
                               style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">add_circle</i>
                                Thêm mới
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body px-0 pb-2">
                <?php if (isset($keyword)): ?>
                    <div class="alert alert-info alert-dismissible fade show mx-4 mb-3" role="alert">
                        <strong><i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">search</i> Kết quả tìm kiếm:</strong> 
                        "<?= htmlspecialchars($keyword); ?>" - Tìm thấy <?= count($data); ?> khách hàng
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive p-3">
                    <table id="customerTable" class="table align-items-center justify-content-center mb-0">
                        <thead style="background: linear-gradient(195deg, #EC407A 0%, #D81B60 100%) !important;">
                            <tr>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif; background: transparent !important;">STT</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Mã KH</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder ps-2" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Tên khách hàng</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder ps-2" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Email</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder ps-2" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Điện thoại</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder ps-2" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Địa chỉ</th>
                                <th class="text-center text-uppercase text-white text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Đơn hàng</th>
                                <th class="text-center text-uppercase text-white text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Trạng thái</th>
                                <th class="text-center text-uppercase text-white text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif; background: transparent !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($data as $customer): ?>
                                    <tr>
                                        <!-- STT -->
                                        <td>
                                            <span class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;"><?= $no++; ?></span>
                                        </td>

                                        <!-- Mã KH -->
                                        <td>
                                            <span class="text-sm font-weight-bold text-primary" style="font-family: 'Poppins', sans-serif;">
                                                <?= $customer->id_cust; ?>
                                            </span>
                                        </td>

                                        <!-- Tên khách hàng -->
                                        <td>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm" style="font-family: 'Poppins', sans-serif;">
                                                    <?= htmlspecialchars($customer->cust_name); ?>
                                                </h6>
                                                <?php if (!empty($customer->notes)): ?>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <i class="material-icons-round" style="font-size: 12px;">notes</i>
                                                        <?= substr(htmlspecialchars($customer->notes), 0, 30); ?>...
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Email -->
                                        <td>
                                            <span class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                                <?= !empty($customer->email) ? htmlspecialchars($customer->email) : '<span class="text-muted">Chưa có</span>'; ?>
                                            </span>
                                        </td>

                                        <!-- Điện thoại -->
                                        <td>
                                            <span class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                                <?= !empty($customer->telp) ? htmlspecialchars($customer->telp) : '<span class="text-muted">Chưa có</span>'; ?>
                                            </span>
                                        </td>

                                        <!-- Địa chỉ -->
                                        <td>
                                            <span class="text-xs" style="font-family: 'Poppins', sans-serif;">
                                                <?= !empty($customer->address) ? (strlen($customer->address) > 30 ? substr(htmlspecialchars($customer->address), 0, 30) . '...' : htmlspecialchars($customer->address)) : '<span class="text-muted">Chưa có</span>'; ?>
                                            </span>
                                        </td>

                                        <!-- Thống kê đơn hàng -->
                                        <td class="align-middle text-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="badge badge-sm bg-gradient-info mb-1" style="font-family: 'Poppins', sans-serif;">
                                                    <?= $customer->total_orders ?? 0; ?> đơn
                                                </span>
                                                <?php if (isset($customer->last_order_date) && $customer->last_order_date): ?>
                                                    <small class="text-xs text-muted" style="font-family: 'Poppins', sans-serif;">
                                                        <?= date('d/m/Y', strtotime($customer->last_order_date)); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Trạng thái với gradient -->
                                        <td class="align-middle text-center">
                                            <?php if ($customer->is_active == 1): ?>
                                                <span class="badge badge-sm bg-gradient-success" 
                                                      style="font-family: 'Poppins', sans-serif;">
                                                    <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">check_circle</i>
                                                    Hoạt động
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-secondary" 
                                                      style="font-family: 'Poppins', sans-serif;">
                                                    <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">pause_circle</i>
                                                    Ngừng
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Thao tác -->
                                        <td class="align-middle text-center">
                                            <a href="<?= site_url('BOD/customer/edit/' . $customer->id_cust); ?>" 
                                               class="btn btn-sm bg-gradient-warning mb-0 me-1"
                                               onclick="window.location.href=this.href; return false;"
                                               style="font-family: 'Poppins', sans-serif;">
                                                <i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">edit</i>
                                                Sửa
                                            </a>
                                            <a href="<?= site_url('BOD/customer/delete/' . $customer->id_cust); ?>" 
                                               class="btn btn-sm bg-gradient-danger mb-0"
                                               onclick="window.location.href=this.href; return false;"
                                               style="font-family: 'Poppins', sans-serif;">
                                                <i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">delete</i>
                                                Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="material-icons-round text-secondary" style="font-size: 48px;">folder_off</i>
                                        <p class="text-muted mt-2" style="font-family: 'Poppins', sans-serif;">
                                            Không có dữ liệu khách hàng
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

<!-- DataTables Script - Optimized -->
<script>
/**
 * Toast Notification Functions
 */
function showToast(options) {
    const icons = {
        success: '✅',
        warning: '⚠️',
        error: '❌',
        info: 'ℹ️'
    };

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
    
    document.body.appendChild(toast);
    
    const duration = options.duration || 3000;
    setTimeout(() => {
        closeToast(toast);
    }, duration);
}

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

// Toast Notification Handler - Run immediately (before jQuery loads)
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    var msgType = urlParams.get('msg'); // Get msg value, not just check existence
    var toastShown = sessionStorage.getItem('toast_shown_' + window.location.pathname);
    
    console.log('🔍 Customer Toast Debug:', {
        msgType: msgType, 
        toastShown: toastShown,
        hasSuccessFlash: <?= $this->session->flashdata('success_js') ? 'true' : 'false' ?>,
        hasErrorFlash: <?= $this->session->flashdata('error_js') ? 'true' : 'false' ?>
    });
    
    // Force clear sessionStorage for testing
    if (msgType) {
        sessionStorage.removeItem('toast_shown_' + window.location.pathname);
        toastShown = null;
    }
    
    if (msgType && !toastShown) {
        <?php if ($this->session->flashdata('success_js')): ?>
            // SUCCESS - Only if msg=success
            if (msgType === 'success') {
                const successData = <?= $this->session->flashdata('success_js'); ?>;
                console.log('\u2705 Customer Success Data:', successData);
                
                showToast({
                    type: 'success',
                    title: successData.title,
                    message: successData.message,
                    details: successData.cust_name ? [
                        '\ud83d\udc64 Kh\u00e1ch h\u00e0ng: ' + successData.cust_name
                    ] : [],
                    duration: 3000
                });
                sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        <?php elseif ($this->session->flashdata('error_js')): ?>
            // ERROR - Only if msg=error
            if (msgType === 'error') {
                const errorData = <?= $this->session->flashdata('error_js'); ?>;
                console.error('\u274c Customer Error Data:', errorData);
                
                showToast({
                    type: 'error',
                    title: 'L\u1ed7i!',
                    message: errorData.message,
                    details: errorData.details || [],
                    duration: 5000
                });
                sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        <?php endif; ?>
    }
    
    window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('toast_shown_' + window.location.pathname);
    });
})();

// DataTables initialization - Wait for jQuery
window.addEventListener('load', function() {
    if (typeof jQuery !== 'undefined') {
        $('#customerTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Vietnamese.json"
            },
            "pageLength": 25,
            "order": [[1, 'asc']], // Sort by Mã KH ascending (tăng dần)
            "deferRender": true, // Lazy rendering for performance
            "processing": false,
            "dom": 'lrtip' // Remove default search box (we have custom one)
        });

        // Initialize tooltips (lazy)
        $('body').tooltip({
            selector: '[data-bs-toggle="tooltip"]',
            trigger: 'hover'
        });
    }
});
</script>
