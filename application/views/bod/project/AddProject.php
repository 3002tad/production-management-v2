<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- USE CASE: TIẾP NHẬN & TẠO ĐƠN HÀNG BÚT BI                            -->
<!-- Actor: Ban Giám Đốc (BOD)                                            -->
<!-- Basic Flow: 8 bước | Alternative Flow: 4.1, 6.1 | Exception: 5.1, 5.2-->
<!-- ═══════════════════════════════════════════════════════════════════ -->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">add_task</i>
                        Tiếp nhận & Tạo đơn hàng mới
                    </h6>
                </div>
            </div>

            <div class="card-body px-4 pb-2">
                
                <!-- ============================================================ -->
                <!-- FORM TIẾP NHẬN ĐƠN HÀNG                                     -->
                <!-- Basic Flow Bước 2 & 3                                       -->
                <!-- Toast notification sẽ hiển thị tự động ở góc phải          -->
                <!-- ============================================================ -->
                
                <form id="order_form" action="<?= site_url('BOD/addProject'); ?>" method="post">
                    <input type="hidden" name="pr_status" value="1">

                    <div class="row">
                        <!-- Tên đơn hàng (auto hoặc manual) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đơn hàng</label>
                            <small class="text-muted"> - Để trống để tạo tự động (ORD-{id_cust}-{date}-{seq})</small>
                            <div class="input-group input-group-outline">
                                <input type="text" 
                                       name="project_name" 
                                       class="form-control" 
                                       placeholder="ORD-... (tự động tạo nếu bỏ trống)">
                            </div>
                        </div>

                        <!-- Hạn giao (REQUIRED) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Hạn giao <span class="text-danger">*</span>
                            </label>
                            <small class="text-muted"> - Phải từ hôm nay trở đi</small>
                            <div class="input-group input-group-outline">
                                <input type="date" 
                                       name="entry_date" 
                                       class="form-control" 
                                       required
                                       min="<?= date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Khách hàng (REQUIRED) -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Khách hàng <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" name="id_cust" required>
                                    <option value="" disabled selected>-- Chọn khách hàng --</option>
                                    <?php if (!empty($customer)): ?>
                                        <?php foreach ($customer as $c): ?>
                                            <option value="<?= $c->id_cust; ?>">
                                                <?= $c->cust_name; ?> 
                                                <?php if (!empty($c->cust_code)): ?>
                                                    (<?= $c->cust_code; ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Sản phẩm (REQUIRED) -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Sản phẩm <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" 
                                        id="product_select" 
                                        name="id_product" 
                                        required>
                                    <option value="" disabled selected>-- Chọn sản phẩm --</option>
                                    <?php if (!empty($product)): ?>
                                        <?php foreach ($product as $p): ?>
                                            <option value="<?= $p->id_product; ?>" 
                                                    data-diameter="<?= $p->diameter; ?>">
                                                <?= $p->product_name; ?>
                                                <?php if (!empty($p->diameter)): ?>
                                                    - Ø <?= $p->diameter; ?>mm
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Số lượng (REQUIRED) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Số lượng <span class="text-danger">*</span>
                            </label>
                            <small class="text-muted"> - Phải lớn hơn 0</small>
                            <div class="input-group input-group-outline">
                                <input type="number" 
                                       name="qty_request" 
                                       class="form-control" 
                                       placeholder="Ví dụ: 10000"
                                       min="1"
                                       required>
                                <span class="input-group-text">chiếc</span>
                            </div>
                        </div>

                        <!-- Đường kính (REQUIRED) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Đường kính <span class="text-danger">*</span>
                            </label>
                            <small class="text-muted"> - Tự động điền khi chọn sản phẩm</small>
                            <div class="input-group input-group-outline">
                                <input type="number" 
                                       step="0.1" 
                                       id="diameter_input" 
                                       name="diameter" 
                                       class="form-control" 
                                       placeholder="0.0"
                                       min="0.1"
                                       required>
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- YÊU CẦU KHÁCH HÀNG (Optional)                           -->
                    <!-- Basic Flow Bước 3 - Ghi chú yêu cầu đặc biệt            -->
                    <!-- ======================================================== -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Yêu cầu của khách hàng (nếu có)</label>
                            <small class="text-muted"> - Ví dụ: màu sắc, bao bì, thời gian giao đặc biệt...</small>
                            <div class="input-group input-group-outline">
                                <textarea name="customer_request" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="Nhập các yêu cầu đặc biệt của khách hàng (nếu có)..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <a href="<?= site_url('BOD/project'); ?>" 
                               class="btn btn-outline-secondary mb-0">
                                <i class="material-icons opacity-10">arrow_back</i>
                                Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary mb-0">
                                <i class="material-icons opacity-10">save</i>
                                Lưu và duyệt đơn hàng
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════ -->
<!-- JAVASCRIPT VALIDATION & CONFIRM DIALOG                                -->
<!-- Alternative Flow 4.1 - Kiểm tra thiếu dữ liệu bắt buộc                -->
<!-- Exception 5.1 - Hủy đơn trước khi lưu                                 -->
<!-- ════════════════════════════════════════════════════════════════════ -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    
    // ========================================================================
    // AUTO-FILL DIAMETER KHI CHỌN PRODUCT
    // Basic Flow Bước 2 - Hiển thị gợi ý hợp lệ
    // ========================================================================
    $('#product_select').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var diameter = selectedOption.data('diameter');
        
        if (diameter) {
            // Tự động điền diameter vào input
            $('#diameter_input').val(diameter);
            
            // Hiệu ứng highlight
            $('#diameter_input').addClass('is-valid');
            setTimeout(function() {
                $('#diameter_input').removeClass('is-valid');
            }, 1500);
        }
    });
    
    // ========================================================================
    // CLIENT-SIDE VALIDATION
    // Alternative Flow 4.1 - Thiếu dữ liệu bắt buộc
    // ========================================================================
    $('#order_form').on('submit', function(e) {
        // Lấy giá trị từ form
        const id_cust = $('select[name="id_cust"]').val();
        const id_product = $('select[name="id_product"]').val();
        const diameter = $('input[name="diameter"]').val();
        const qty_request = parseInt($('input[name="qty_request"]').val());
        const entry_date = $('input[name="entry_date"]').val();
        
        let errorMessage = '';
        
        // Kiểm tra khách hàng
        if (!id_cust || id_cust === '') {
            errorMessage += '• Vui lòng chọn khách hàng\n';
        }
        
        // Kiểm tra sản phẩm
        if (!id_product || id_product === '') {
            errorMessage += '• Vui lòng chọn sản phẩm\n';
        }
        
        // Kiểm tra đường kính
        if (!diameter || diameter === '' || parseFloat(diameter) <= 0) {
            errorMessage += '• Vui lòng nhập đường kính hợp lệ (> 0)\n';
        }
        
        // Kiểm tra số lượng (AF 4.1 - Số lượng phải > 0)
        if (!qty_request || isNaN(qty_request) || qty_request <= 0) {
            e.preventDefault();
            alert('⚠️ LỖI: Số lượng phải lớn hơn 0\n\nVui lòng nhập lại.');
            $('input[name="qty_request"]').focus();
            return false;
        }
        
        // Kiểm tra hạn giao (AF 4.1 - Hạn giao phải >= hôm nay)
        if (!entry_date || entry_date === '') {
            errorMessage += '• Vui lòng nhập hạn giao\n';
        } else {
            // FIX: Parse ngày đúng cách (tránh lỗi timezone UTC)
            const entryDateParts = entry_date.split('-'); // ['2025', '11', '01']
            const entryDateObj = new Date(entryDateParts[0], entryDateParts[1] - 1, entryDateParts[2]); // Local time
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (entryDateObj < today) {
                e.preventDefault();
                const todayStr = today.getFullYear() + '-' + 
                                String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                                String(today.getDate()).padStart(2, '0');
                alert('⚠️ LỖI: Hạn giao phải từ hôm nay trở đi\n\nNgày bạn chọn: ' + entry_date + '\nNgày hôm nay: ' + todayStr);
                $('input[name="entry_date"]').focus();
                return false;
            }
        }
        
        // Nếu có lỗi validation
        if (errorMessage !== '') {
            e.preventDefault();
            alert('⚠️ LỖI: Thiếu dữ liệu bắt buộc\n\n' + errorMessage + '\nVui lòng nhập đầy đủ thông tin.');
            return false;
        }
        
        // ====================================================================
        // CONFIRM DIALOG - Exception 5.1: BGĐ hủy đơn trước khi lưu
        // ====================================================================
        const confirmMessage = 
            '🎯 XÁC NHẬN TẠO ĐƠN HÀNG\n\n' +
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n' +
            '📦 Sản phẩm: ' + $('select[name="id_product"] option:selected').text() + '\n' +
            '👤 Khách hàng: ' + $('select[name="id_cust"] option:selected').text() + '\n' +
            '📊 Số lượng: ' + qty_request.toLocaleString() + ' chiếc\n' +
            '📏 Đường kính: ' + diameter + ' mm\n' +
            '📅 Hạn giao: ' + entry_date + '\n' +
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n' +
            '✅ Bấm OK để LƯU VÀ DUYỆT đơn hàng\n' +
            '❌ Bấm Cancel để HỦY và quay lại';
        
        if (!confirm(confirmMessage)) {
            // Exception 5.1.2 - Hiển thị thông báo xác nhận
            // Exception 5.1.3 - Ban giám đốc xác nhận hủy
            e.preventDefault();
            alert('❌ Đã hủy tạo đơn hàng.\n\nBạn có thể tiếp tục chỉnh sửa hoặc quay lại.');
            // Exception 5.1.4 - Kết thúc use case
            return false;
        }
        
        // Nếu confirm = OK → Submit form (tiếp tục Basic Flow)
        return true;
    });
});
</script>

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
    // Kiểm tra URL parameter - CHỈ hiển thị toast khi có ?msg= (redirect từ submit thất bại)
    var urlParams = new URLSearchParams(window.location.search);
    var hasMsg = urlParams.has('msg');
    
    // SessionStorage backup - tránh hiển thị lại khi refresh
    var toastShown = sessionStorage.getItem('toast_shown_addproject');
    
    if (hasMsg && !toastShown) {
        <?php if ($this->session->flashdata('success_js')): ?>
            // Parse dữ liệu từ session
            const successData = <?= $this->session->flashdata('success_js'); ?>;
            
            // Tạo toast notification
            showToast({
                type: 'success',
                title: successData.title,
                message: successData.message,
                details: [
                    '📦 Mã đơn hàng: ' + successData.project_name,
                    successData.risk_flag == 1 
                        ? '⚠️ Trạng thái: Nguy cơ trễ hạn' 
                        : '✅ Trạng thái: Bình thường'
                ],
                duration: 3000 // 3 giây
            });
            
            // Đánh dấu đã hiển thị
            sessionStorage.setItem('toast_shown_addproject', 'true');
            
            // Xóa parameter khỏi URL
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>

        <?php if ($this->session->flashdata('warning_js')): ?>
            const warningData = <?= $this->session->flashdata('warning_js'); ?>;
            showToast({
                type: 'warning',
                title: 'Cảnh báo công suất!',
                message: warningData.message,
                details: warningData.details || [],
                duration: 5000
            });
            
            sessionStorage.setItem('toast_shown_addproject', 'true');
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>

        <?php if ($this->session->flashdata('error_js')): ?>
            const errorData = <?= $this->session->flashdata('error_js'); ?>;
            showToast({
                type: 'error',
                title: 'Lỗi!',
                message: errorData.message,
                duration: 6000
            });
            
            sessionStorage.setItem('toast_shown_addproject', 'true');
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>
    }
    
    // Xóa flag khi navigate sang trang khác
    window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('toast_shown_addproject');
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
