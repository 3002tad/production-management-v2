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
                                <select class="form-control" id="customer_select" name="id_cust" required>
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

                            <!-- Hiển thị ghi chú khách hàng -->
                            <div id="customer_notes_section" style="display: none; margin-top: 12px;">
                                <div class="alert alert-info py-2 px-3" style="background: linear-gradient(195deg, #42424a 0%, #191919 100%); color: white;">
                                    <div class="d-flex align-items-start">
                                        <i class="material-icons opacity-10 me-2" style="font-size: 20px;">sticky_note_2</i>
                                        <div class="flex-grow-1">
                                            <strong>Ghi chú khách hàng:</strong>
                                            <div id="customer_notes_display" class="mt-1" style="font-size: 14px; line-height: 1.6;"></div>
                                            
                                            <!-- Form sửa ghi chú -->
                                            <div id="customer_notes_edit_form" style="display: none; margin-top: 8px;">
                                                <textarea id="customer_notes_input" class="form-control" rows="3" style="font-size: 13px;"></textarea>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-success" id="save_notes_btn">
                                                        <i class="material-icons" style="font-size: 16px;">check</i> Lưu
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary" id="cancel_notes_btn">
                                                        <i class="material-icons" style="font-size: 16px;">close</i> Hủy
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Nút chỉnh sửa -->
                                            <div class="mt-2" id="customer_notes_actions">
                                                <button type="button" class="btn btn-sm btn-warning" id="edit_notes_btn">
                                                    <i class="material-icons" style="font-size: 16px;">edit</i> Sửa ghi chú
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                       required
                                       readonly>
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

<script>
window.allCustomers = <?= json_encode($customer); ?>;
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
    var msgType = urlParams.get('msg'); // Get msg value
    
    // SessionStorage backup - tránh hiển thị lại khi refresh
    var toastShown = sessionStorage.getItem('toast_shown_addproject');
    
    if (msgType && !toastShown) {
        <?php if ($this->session->flashdata('success_js')): ?>
            // SUCCESS - Only if msg=success
            if (msgType === 'success') {
                const successData = <?= $this->session->flashdata('success_js'); ?>;
                
                showToast({
                type: 'success',
                title: successData.title,
                message: successData.message,
                details: [
                    '📦 Mã đơn hàng: ' + successData.project_name,
                    successData.risk_flag == 1
                        ? '⚠️ Trạng thái: Cảnh báo trễ hạn' 
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
            }
        <?php endif; ?>

        <?php if ($this->session->flashdata('error_js')): ?>
            // ERROR - Only if msg=error
            if (msgType === 'error') {
                const errorData = <?= $this->session->flashdata('error_js'); ?>;
                showToast({
                    type: 'error',
                    title: 'Lỗi!',
                    message: errorData.message,
                    duration: 6000
                });
                
                sessionStorage.setItem('toast_shown_addproject', 'true');
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        <?php endif; ?>
    }
    
    // Xóa flag khi navigate sang trang khác
    window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('toast_shown_addproject');
    });

    // ========================================
    // CUSTOMER NOTES MANAGEMENT
    // ========================================
    const customerSelect = document.getElementById('customer_select');
    const notesSection = document.getElementById('customer_notes_section');
    const notesDisplay = document.getElementById('customer_notes_display');
    const notesEditForm = document.getElementById('customer_notes_edit_form');
    const notesInput = document.getElementById('customer_notes_input');
    const notesActions = document.getElementById('customer_notes_actions');
    const editNotesBtn = document.getElementById('edit_notes_btn');
    const saveNotesBtn = document.getElementById('save_notes_btn');
    const cancelNotesBtn = document.getElementById('cancel_notes_btn');

    let currentCustomerId = null;
    let currentNotes = '';

    // Khi chọn khách hàng
    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        if (!customerId) {
            notesSection.style.display = 'none';
            return;
        }

        currentCustomerId = customerId;

        // Find the selected customer in the allCustomers array
        const selectedCustomer = allCustomers.find(cust => cust.id_cust == customerId);

        if (selectedCustomer) {
            currentNotes = selectedCustomer.notes || '';
            
            if (currentNotes.trim()) {
                notesDisplay.innerHTML = currentNotes.replace(/\n/g, '<br>');
                notesSection.style.display = 'block';
            } else {
                notesDisplay.innerHTML = '<em style="color: #ccc;">Chưa có ghi chú</em>';
                notesSection.style.display = 'block';
            }
            
            // Reset form
            notesEditForm.style.display = 'none';
            notesActions.style.display = 'block';
        } else {
            notesSection.style.display = 'none';
        }
    });

    // Nút chỉnh sửa
    editNotesBtn.addEventListener('click', function() {
        notesInput.value = currentNotes;
        notesEditForm.style.display = 'block';
        notesActions.style.display = 'none';
        notesInput.focus();
    });

    // Nút hủy
    cancelNotesBtn.addEventListener('click', function() {
        notesEditForm.style.display = 'none';
        notesActions.style.display = 'block';
    });

    // Nút lưu (prevent duplicate handlers)
    if (!saveNotesBtn.dataset.notesHandlerAttached) {
        saveNotesBtn.addEventListener('click', function() {
            if (saveNotesBtn.dataset.saving === '1') return; // prevent double clicks
            saveNotesBtn.dataset.saving = '1';
            saveNotesBtn.disabled = true;

            const newNotes = notesInput.value;

            // Abort any in-flight notes request to avoid overlapping responses
            if (window._notesAbortController) {
                try { window._notesAbortController.abort(); } catch (e) { /* ignore */ }
            }
            window._notesAbortController = new AbortController();

            const _reqId = Date.now() + '-' + Math.random().toString(36).slice(2,8);
            console.log('Sending updateCustomerNotes request', { reqId: _reqId, id_cust: currentCustomerId });
            fetch('<?= site_url("BOD/updateCustomerNotes"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Request-Id': _reqId
                },
                credentials: 'same-origin',
                signal: window._notesAbortController.signal,
                body: 'id_cust=' + currentCustomerId + '&notes=' + encodeURIComponent(newNotes) + '&ajax=1'
            })
            .then(response => response.text().then(body => ({ status: response.status, ok: response.ok, headers: response.headers, body })))
            .then(({ status, ok, headers, body }) => {
                console.log('updateCustomerNotes response', { status, ok, contentType: headers.get('content-type'), bodyPreview: body.slice(0, 500) });
                const ct = headers.get('content-type') || '';
                if (ok && (ct.includes('application/json') || body.trim().startsWith('{') || body.trim().startsWith('['))) {
                    try {
                        const data = JSON.parse(body);
                        return data;
                    } catch (e) {
                        throw new Error('Server returned invalid JSON response. Possibly session expired.');
                    }
                }

                if (!ok) {
                    if (ct.includes('application/json')) {
                        try {
                            const data = JSON.parse(body);
                            throw new Error(data.message || JSON.stringify(data));
                        } catch (e) {
                            throw new Error(body || 'Server error: ' + status);
                        }
                    }
                    throw new Error(body || 'Server error: ' + status);
                }

                throw new Error('Server returned non-JSON response. Possible session timeout — please reload and login.');
            })
            .then(data => {
                try {
                    console.log('updateCustomerNotes parsed', data);
                    if (data && data.success) {
                        currentNotes = newNotes;
                        notesDisplay.innerHTML = currentNotes ? currentNotes.replace(/\n/g, '<br>') : '<em style="color: #ccc;">Chưa có ghi chú</em>';
                        notesEditForm.style.display = 'none';
                        notesActions.style.display = 'block';

                        showToast({ type: 'success', title: 'Thành công', message: 'Đã cập nhật ghi chú khách hàng', duration: 3000 });
                    } else {
                        showToast({ type: 'error', title: 'Lỗi', message: data && data.message ? data.message : 'Không thể cập nhật ghi chú', duration: 6000 });
                    }
                } catch (e) {
                    console.error('Error in success handler:', e);
                    showToast({ type: 'error', title: 'Lỗi', message: 'Lỗi nội bộ khi xử lý kết quả. Vui lòng kiểm tra console.', duration: 6000 });
                }
            })
            .catch(error => {
                if (error && error.name === 'AbortError') {
                    console.warn('updateCustomerNotes request aborted');
                    return;
                }
                console.error('Error:', error);
                showToast({ type: 'error', title: 'Lỗi', message: error.message || 'Có lỗi xảy ra khi lưu ghi chú', duration: 6000 });
            })
            .finally(() => {
                saveNotesBtn.dataset.saving = '0';
                saveNotesBtn.disabled = false;
                if (window._notesAbortController) { window._notesAbortController = null; }
            });
        });
        saveNotesBtn.dataset.notesHandlerAttached = '1';
    }

    // ========================================================================
    // PRODUCT -> AUTO-FILL DIAMETER
    // Basic Flow Bước 2 - Tự động điền Đường kính khi chọn sản phẩm
    // ========================================================================
    const productSelect = document.getElementById('product_select');
    const diameterInput = document.getElementById('diameter_input');

    function syncDiameterFromProduct() {
        if (!productSelect || !diameterInput) return;
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption) return;
        const d = selectedOption.getAttribute('data-diameter');
        if (d) {
            diameterInput.value = d;
            diameterInput.classList.add('is-valid');
            setTimeout(() => diameterInput.classList.remove('is-valid'), 1500);
        }
    }

    if (productSelect) {
        productSelect.addEventListener('change', syncDiameterFromProduct);
        // Initialize on load in case browser preserved selection
        syncDiameterFromProduct();
    }

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
