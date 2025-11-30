<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC2: Product Management - INDEX VIEW                                        ║
║  Material Design 3.0 với BOM status indicator                               ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <!-- Card Header -->
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-6 d-flex align-items-center">
                            <i class="material-icons-round text-white opacity-10 me-2" style="font-size: 24px;">lan</i>
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Quản lý Sản phẩm & BOM</h6>
                        </div>
                        <div class="col-6 text-end">
                            <!-- Search Box -->
                            <form action="<?= site_url('BOD/product/search'); ?>" method="GET" class="d-inline-block me-2" style="width: 250px;">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="keyword" class="form-control form-control-sm" 
                                           placeholder="Tìm sản phẩm..." 
                                           value="<?= isset($keyword) ? htmlspecialchars($keyword) : ''; ?>"
                                           style="font-family: 'Poppins', sans-serif; border-radius: 8px 0 0 8px;">
                                    <button class="btn btn-sm bg-white mb-0" type="submit" style="border-radius: 0 8px 8px 0;">
                                        <i class="material-icons-round text-dark" style="font-size: 18px;">search</i>
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Add Button -->
                            <a href="<?= site_url('BOD/product/add'); ?>" 
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
                        "<?= htmlspecialchars($keyword); ?>" - Tìm thấy <?= count($data); ?> sản phẩm
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive p-3">
                    <table id="productTable" class="table align-items-center justify-content-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">STT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Mã SP</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="font-family: 'Poppins', sans-serif;">Tên sản phẩm</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Đường kính</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="font-family: 'Poppins', sans-serif;">Ứng dụng</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">BOM</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Đơn hàng</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Trạng thái</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($data as $product): ?>
                                    <tr>
                                        <!-- STT -->
                                        <td>
                                            <span class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;"><?= $no++; ?></span>
                                        </td>

                                        <!-- Mã SP -->
                                        <td>
                                            <span class="text-sm font-weight-bold text-primary" style="font-family: 'Poppins', sans-serif;">
                                                <?= $product->id_product; ?>
                                            </span>
                                        </td>

                                        <!-- Tên sản phẩm -->
                                        <td>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm" style="font-family: 'Poppins', sans-serif;">
                                                    <?= htmlspecialchars($product->product_name); ?>
                                                </h6>
                                                <?php if (!empty($product->summary)): ?>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <?= substr(htmlspecialchars($product->summary), 0, 40); ?>...
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Đường kính -->
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-secondary" style="font-family: 'Poppins', sans-serif;">
                                                <?= isset($product->diameter_display) ? $product->diameter_display : (isset($product->diameter) ? $product->diameter . 'mm' : 'N/A'); ?>
                                            </span>
                                        </td>

                                        <!-- Ứng dụng (Color coding) -->
                                        <td>
                                            <?php
                                            // Map màu ứng dụng với màu badge Bootstrap
                                            // Priority: từ cụ thể đến chung (xanh lá/xanh dương trước "xanh")
                                            $color_map = [
                                                'Xanh lá' => 'success',      // Green
                                                'Xanh lục' => 'success',     // Green
                                                'Xanh dương' => 'info',      // Blue
                                                'Xanh da trời' => 'info',    // Sky blue
                                                'Xanh biển' => 'info',       // Ocean blue
                                                'Xanh' => 'info',            // Default blue (phải sau xanh lá)
                                                'Đỏ' => 'danger',            // Red
                                                'Đen' => 'dark',             // Black
                                                'Vàng' => 'warning',         // Yellow
                                                'Cam' => 'warning',          // Orange
                                                'Tím' => 'purple',           // Purple
                                                'Hồng' => 'pink',            // Pink
                                                'Trắng' => 'light text-dark', // White
                                                'Xám' => 'secondary',        // Gray
                                            ];
                                            
                                            $app = htmlspecialchars($product->application ?? '');
                                            $badge_color = 'secondary'; // Default gray
                                            
                                            // Tìm màu phù hợp (ưu tiên keyword dài hơn)
                                            foreach ($color_map as $keyword => $color) {
                                                if (stripos($app, $keyword) !== false) {
                                                    $badge_color = $color;
                                                    break; // Dừng ở match đầu tiên
                                                }
                                            }
                                            ?>
                                            <span class="badge badge-sm bg-gradient-<?= $badge_color; ?>" style="font-family: 'Poppins', sans-serif;">
                                                <?= $app ?: 'Chưa xác định'; ?>
                                            </span>
                                        </td>

                                        <!-- BOM Status -->
                                        <td class="align-middle text-center">
                                            <?php if (!empty($product->bom) && $product->bom != 'null'): ?>
                                                <?php 
                                                $bom_data = json_decode($product->bom, true);
                                                // Support cả 2 formats: ['materials' => [...]] và [{}, {}]
                                                if (isset($bom_data['materials'])) {
                                                    $material_count = count($bom_data['materials']);
                                                } elseif (is_array($bom_data)) {
                                                    $material_count = count($bom_data);
                                                } else {
                                                    $material_count = 0;
                                                }
                                                ?>
                                                <span class="badge badge-sm bg-gradient-success" 
                                                      data-bs-toggle="tooltip" 
                                                      title="<?= $material_count; ?> nguyên liệu"
                                                      style="font-family: 'Poppins', sans-serif;">
                                                    <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">check_circle</i>
                                                    Có (<?= $material_count; ?>)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-secondary" style="font-family: 'Poppins', sans-serif;">
                                                    <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">remove_circle</i>
                                                    Chưa có
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Thống kê đơn hàng -->
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-info" style="font-family: 'Poppins', sans-serif;">
                                                <?= $product->total_orders ?? 0; ?> đơn
                                            </span>
                                        </td>

                                        <!-- Trạng thái -->
                                        <td class="align-middle text-center">
                                            <?php if (isset($product->is_active) && $product->is_active == 1): ?>
                                                <span class="badge badge-sm bg-gradient-success" style="font-family: 'Poppins', sans-serif;">
                                                    <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">check_circle</i>
                                                    Hoạt động
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-secondary" style="font-family: 'Poppins', sans-serif;">
                                                    <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">pause_circle</i>
                                                    Ngừng
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Thao tác -->
                                        <td class="align-middle text-center">
                                            <a href="<?= site_url('BOD/product/edit/' . $product->id_product); ?>" 
                                               class="btn btn-sm bg-gradient-warning mb-0 me-1"
                                               onclick="window.location.href=this.href; return false;"
                                               style="font-family: 'Poppins', sans-serif;">
                                                <i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">edit</i>
                                                Sửa
                                            </a>
                                            <a href="<?= site_url('BOD/product/delete/' . $product->id_product); ?>" 
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
                                        <i class="material-icons-round text-secondary" style="font-size: 48px;">inventory_2</i>
                                        <p class="text-muted mt-2" style="font-family: 'Poppins', sans-serif;">
                                            Không có dữ liệu sản phẩm
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

/* Custom badge colors for additional color support */
.bg-gradient-purple {
    background: linear-gradient(195deg, #7b1fa2 0%, #9c27b0 100%) !important;
    color: white !important;
}

.bg-gradient-pink {
    background: linear-gradient(195deg, #ec407a 0%, #f48fb1 100%) !important;
    color: white !important;
}
</style>

<!-- DataTables Script -->
<script>
/**
 * Toast Notification Functions
 * @param {Object} options - {type, title, message, details, duration}
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
    var msgType = urlParams.get('msg'); // Get msg value
    var toastShown = sessionStorage.getItem('toast_shown_' + window.location.pathname);
    
    console.log('🔍 Toast Debug:', {
        msgType: msgType, 
        toastShown: toastShown,
        hasSuccessFlash: <?= $this->session->flashdata('success_js') ? 'true' : 'false' ?>,
        hasErrorFlash: <?= $this->session->flashdata('error_js') ? 'true' : 'false' ?>,
        url: window.location.href
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
                console.log('\u2705 Success Data:', successData);
                
                let details = successData.product_name ? [
                    '\ud83d\udce6 S\u1ea3n ph\u1ea9m: ' + successData.product_name
                ] : [];
                
                showToast({
                    type: 'success',
                    title: successData.title,
                    message: successData.message,
                    details: details,
                    duration: 3000
                });
                sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        <?php elseif ($this->session->flashdata('error_js')): ?>
            // ERROR - Only if msg=error
            if (msgType === 'error') {
                const errorData = <?= $this->session->flashdata('error_js'); ?>;
                console.error('\u274c Error Data:', errorData);
                
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
        $('#productTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Vietnamese.json"
            },
            "pageLength": 25,
            "order": [[1, 'asc']] // Sort by Mã SP ascending (sản phẩm mới nhất ở cuối)
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
