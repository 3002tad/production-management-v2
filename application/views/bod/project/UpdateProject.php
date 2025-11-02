<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- UPDATE PROJECT - Cập nhật đơn hàng                                   -->
<!-- ═══════════════════════════════════════════════════════════════════ -->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">edit</i>
                        Cập nhật đơn hàng
                    </h6>
                </div>
            </div>

            <div class="card-body px-4 pb-2">
                
                <form action="<?= site_url('BOD/updateProject'); ?>" method="post">
                    <input type="hidden" name="id_project" value="<?= $detail->id_project; ?>">

                    <div class="row">
                        <!-- Tên đơn hàng -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đơn hàng</label>
                            <div class="input-group input-group-outline">
                                <input type="text" 
                                       name="project_name" 
                                       class="form-control" 
                                       value="<?= $detail->project_name; ?>"
                                       required>
                            </div>
                        </div>

                        <!-- Hạn giao -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hạn giao <span class="text-danger">*</span></label>
                            <div class="input-group input-group-outline">
                                <input type="date" 
                                       name="entry_date" 
                                       class="form-control" 
                                       value="<?= $detail->entry_date; ?>"
                                       min="<?= date('Y-m-d'); ?>"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Khách hàng -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" name="id_cust" required>
                                    <?php if (!empty($customer)): ?>
                                        <?php foreach ($customer as $c): ?>
                                            <option value="<?= $c->id_cust; ?>" 
                                                    <?= ($c->id_cust == $detail->id_cust) ? 'selected' : ''; ?>>
                                                <?= $c->cust_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Sản phẩm -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" id="product_select" name="id_product" required>
                                    <?php if (!empty($product)): ?>
                                        <?php foreach ($product as $p): ?>
                                            <option value="<?= $p->id_product; ?>" 
                                                    data-diameter="<?= $p->diameter; ?>"
                                                    <?= ($p->id_product == $detail->id_product) ? 'selected' : ''; ?>>
                                                <?= $p->product_name; ?> - Ø <?= $p->diameter; ?>mm
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Số lượng -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <div class="input-group input-group-outline">
                                <input type="number" 
                                       name="qty_request" 
                                       class="form-control" 
                                       value="<?= $detail->qty_request; ?>"
                                       min="1"
                                       required>
                                <span class="input-group-text">chiếc</span>
                            </div>
                        </div>

                        <!-- Đường kính -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đường kính <span class="text-danger">*</span></label>
                            <div class="input-group input-group-outline">
                                <input type="number" 
                                       step="0.1" 
                                       id="diameter_input" 
                                       name="diameter" 
                                       class="form-control" 
                                       value="<?= $detail->diameter; ?>"
                                       min="0.1"
                                       required>
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>
                    </div>

                    <!-- Yêu cầu khách hàng -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Yêu cầu khách hàng</label>
                            <div class="input-group input-group-outline">
                                <textarea name="customer_request" 
                                          class="form-control" 
                                          rows="3"><?= isset($detail->customer_request) ? $detail->customer_request : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <a href="<?= site_url('BOD/project'); ?>" class="btn btn-outline-secondary mb-0">
                                <i class="material-icons opacity-10">arrow_back</i>
                                Hủy
                            </a>
                            <button type="submit" class="btn btn-warning mb-0">
                                <i class="material-icons opacity-10">save</i>
                                Cập nhật
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Auto-fill diameter
    $('#product_select').on('change', function() {
        var diameter = $(this).find('option:selected').data('diameter');
        if (diameter) {
            $('#diameter_input').val(diameter);
        }
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
    // Kiểm tra URL parameter - CHỈ hiển thị toast khi có ?msg=
    var urlParams = new URLSearchParams(window.location.search);
    var hasMsg = urlParams.has('msg');
    
    // SessionStorage backup
    var toastShown = sessionStorage.getItem('toast_shown_updateproject');
    
    if (hasMsg && !toastShown) {
        <?php if ($this->session->flashdata('error_js')): ?>
            const errorData = <?= $this->session->flashdata('error_js'); ?>;
            showToast({
                type: 'error',
                title: 'Lỗi!',
                message: errorData.message,
                details: errorData.details || [],
                duration: 6000
            });
            
            sessionStorage.setItem('toast_shown_updateproject', 'true');
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>
    }
    
    // Xóa flag khi navigate
    window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('toast_shown_updateproject');
    });
});

/**
 * Hiển thị toast notification tự động đóng
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
</script>
