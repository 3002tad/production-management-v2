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
                    <input type="hidden" name="id_project" value="<?= $order->id_project; ?>">

                    <div class="row">
                        <!-- Tên đơn hàng -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đơn hàng</label>
                            <div class="input-group input-group-outline">
                                <input type="text" 
                                       name="project_name" 
                                       class="form-control" 
                                       value="<?= $order->project_name; ?>"
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
                                       value="<?= $order->entry_date; ?>"
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
                                <select class="form-control" id="customer_select" name="id_cust" required>
                                    <?php if (!empty($customer)): ?>
                                        <?php foreach ($customer as $c): ?>
                                            <option value="<?= $c->id_cust; ?>" 
                                                    <?= ($c->id_cust == $order->id_cust) ? 'selected' : ''; ?>>
                                                <?= $c->cust_name; ?>
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
                        <!-- Sản phẩm -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" id="product_select" name="id_product" required>
                                    <?php if (!empty($product)): ?>
                                        <?php foreach ($product as $p): ?>
                                            <option value="<?= $p->id_product; ?>" 
                                                    data-diameter="<?= $p->diameter; ?>"
                                                    <?= ($p->id_product == $order->id_product) ? 'selected' : ''; ?>>
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
                                       value="<?= $order->qty_request; ?>"
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
                                       value="<?= $order->diameter; ?>"
                                       min="0.1"
                                       required
                                       readonly>
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
                                          rows="3"><?= isset($order->customer_request) ? $order->customer_request : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin cảnh báo (nếu có) -->
                    <?php if (isset($order->warning_flag) && $order->warning_flag == 1 && !empty($order->warning_details)): ?>
                        <?php 
                            $warnings = json_decode($order->warning_details, true);
                            
                            // Phân biệt giữa thông tin tích cực và cảnh báo thực sự
                            $has_real_warning = isset($warnings['capacity_warning']) || 
                                                isset($warnings['material_warning']) || 
                                                isset($warnings['deadline_warning']);
                            
                            $alert_class = $has_real_warning ? 'alert-warning' : 'alert-info';
                            $alert_icon = $has_real_warning ? 'warning' : 'info';
                            $alert_title = $has_real_warning ? 'Cảnh báo đơn hàng' : 'Thông tin đơn hàng';
                        ?>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="alert <?= $alert_class; ?>" role="alert">
                                    <h6 class="alert-heading">
                                        <i class="material-icons" style="vertical-align: middle;"><?= $alert_icon; ?></i>
                                        <?= $alert_title; ?>
                                    </h6>
                                    <hr>
                                    <ul class="mb-0">
                                        <?php if (isset($warnings['finished_stock_info'])): ?>
                                            <li><strong>Tồn kho:</strong> <?= $warnings['finished_stock_info']; ?></li>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($warnings['capacity_warning'])): ?>
                                            <li><strong>Công suất:</strong> <?= $warnings['capacity_warning']; ?>
                                                <?php if (isset($warnings['estimated_shifts'])): ?>
                                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;→ Cần <?= $warnings['estimated_shifts']; ?> ca (~<?= $warnings['estimated_days'] ?? 'N/A'; ?> ngày)
                                                <?php endif; ?>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($warnings['deadline_warning'])): ?>
                                            <li><strong>Deadline:</strong> <?= $warnings['deadline_warning']; ?></li>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($warnings['material_warning'])): ?>
                                            <li><strong>Nguyên vật liệu:</strong> <?= $warnings['material_warning']; ?>
                                                <?php if (isset($warnings['material_shortage_details'])): ?>
                                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;→ <?= $warnings['material_shortage_details']; ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                    
                                    <?php if (isset($order->capacity_level_used) && $order->capacity_level_used > 0): ?>
                                        <div class="mt-2">
                                            <span class="badge badge-sm bg-gradient-<?= $order->capacity_level_used == 1 ? 'info' : 'warning'; ?>">
                                                Capacity Level <?= $order->capacity_level_used; ?> 
                                                (<?= $order->capacity_level_used == 1 ? '8h×2ca=16h/ngày' : '12h×2ca=24h/ngày'; ?>)
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

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


<script>
window.allCustomers = <?= json_encode($customer); ?>;
    window.currentProjectId = <?= $order->id_project; ?>;
document.addEventListener('DOMContentLoaded', function() {
    // Toast handling for redirects with ?msg (show server flashdata as toast, per-page flag)
    (function() {
        var urlParams = new URLSearchParams(window.location.search);
        var msgType = urlParams.get('msg');
        // Define toastShown default to avoid ReferenceError from other scripts
        var toastShown = sessionStorage.getItem('toast_shown') || sessionStorage.getItem('toast_shown_' + window.location.pathname) || null;

        // Minimal fallback showToast if global function not available
        if (typeof showToast !== 'function') {
            window.showToast = function(opts) {
                var icons = { success: '✅', warning: '⚠️', error: '❌', info: 'ℹ️' };
                var toast = document.createElement('div');
                toast.className = 'toast-notification ' + (opts.type || 'info');
                toast.innerHTML = '<button class="toast-close" onclick="(function(el){el.parentElement.removeChild(el);})(this);">✕</button>' +
                                  '<div class="toast-header"><span class="toast-icon">' + (icons[opts.type]||icons.info) + '</span><h5 class="toast-title">' + (opts.title||'') + '</h5></div>' +
                                  '<div class="toast-body">' + (opts.message||'') + '</div>' +
                                  '<div class="toast-progress"></div>';
                document.body.appendChild(toast);
                setTimeout(function(){ if (toast.parentElement) toast.parentElement.removeChild(toast); }, opts.duration || 3000);
            };
        }

        if (msgType) {
            <?php if ($this->session->flashdata('success_js')): ?>
                if (msgType === 'success') {
                    const successData = <?= $this->session->flashdata('success_js'); ?>;
                    showToast({ type: 'success', title: successData.title, message: successData.message, duration: 3000 });
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            <?php endif; ?>

            <?php if ($this->session->flashdata('warning_js')): ?>
                const warningData = <?= $this->session->flashdata('warning_js'); ?>;
                showToast({ type: 'warning', title: 'Cảnh báo', message: warningData.message, duration: 5000 });
                window.history.replaceState({}, document.title, window.location.pathname);
            <?php endif; ?>

            <?php if ($this->session->flashdata('error_js')): ?>
                if (msgType === 'error') {
                    const errorData = <?= $this->session->flashdata('error_js'); ?>;
                    showToast({ type: 'error', title: 'Lỗi', message: errorData.message || 'Có lỗi xảy ra', duration: 6000 });
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            <?php endif; ?>
        }
    })();

    // Customer notes inline editing (similar to AddProject)
        const customerSelect = document.getElementById('customer_select');
        const notesSection = document.getElementById('customer_notes_section');
        const notesDisplay = document.getElementById('customer_notes_display');
        const notesEditForm = document.getElementById('customer_notes_edit_form');
        const notesInput = document.getElementById('customer_notes_input');
        const notesActions = document.getElementById('customer_notes_actions');
        const editNotesBtn = document.getElementById('edit_notes_btn');
        const saveNotesBtn = document.getElementById('save_notes_btn');
        const cancelNotesBtn = document.getElementById('cancel_notes_btn');

        let currentCustomerId = customerSelect.value;
        let currentNotes = '';

        function loadCustomerNotes(id) {
                const selected = allCustomers.find(c => c.id_cust == id);
            currentNotes = selected ? (selected.notes || '') : '';
            if (currentNotes.trim()) {
                notesDisplay.innerHTML = currentNotes.replace(/\n/g, '<br>');
                notesSection.style.display = 'block';
            } else {
                notesDisplay.innerHTML = '<em style="color: #ccc;">Chưa có ghi chú</em>';
                notesSection.style.display = 'block';
            }
            notesEditForm.style.display = 'none';
            notesActions.style.display = 'block';
        }

        if (currentCustomerId) loadCustomerNotes(currentCustomerId);

        customerSelect.addEventListener('change', function() {
            currentCustomerId = this.value;
            if (!currentCustomerId) { notesSection.style.display = 'none'; return; }
            loadCustomerNotes(currentCustomerId);
        });

        editNotesBtn.addEventListener('click', function() {
            notesInput.value = currentNotes;
            notesEditForm.style.display = 'block';
            notesActions.style.display = 'none';
            notesInput.focus();
});

cancelNotesBtn.addEventListener('click', function() {
            notesEditForm.style.display = 'none';
            notesActions.style.display = 'block';
        });

        if (!saveNotesBtn.dataset.notesHandlerAttached) {
            saveNotesBtn.addEventListener('click', function() {
                if (saveNotesBtn.dataset.saving === '1') return; // prevent double clicks
                saveNotesBtn.dataset.saving = '1';
                saveNotesBtn.disabled = true;

    const newNotes = notesInput.value;
                if (!currentCustomerId) { alert('Vui lòng chọn khách hàng trước'); saveNotesBtn.dataset.saving = '0'; saveNotesBtn.disabled = false; return; }
                // Abort any previous in-flight customer-notes request
                if (window._notesAbortController) {
                    try { window._notesAbortController.abort(); } catch (e) { /* ignore */ }
                }
                window._notesAbortController = new AbortController();

                const _reqId = Date.now() + '-' + Math.random().toString(36).slice(2,8);
                console.log('Sending updateCustomerNotes request', { reqId: _reqId, id_cust: currentCustomerId });
                fetch('<?= site_url("BOD/updateCustomerNotes"); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-Request-Id': _reqId },
                    credentials: 'same-origin',
                    signal: window._notesAbortController.signal,
                    body: 'id_cust=' + currentCustomerId + '&notes=' + encodeURIComponent(newNotes) + '&ajax=1'
                })
                .then(res => res.text().then(body => ({ status: res.status, ok: res.ok, headers: res.headers, body })))
                .then(({ status, ok, headers, body }) => {
                    console.log('updateCustomerNotes response', { status, ok, contentType: headers.get('content-type'), bodyPreview: body.slice(0, 500) });
                    const ct = headers.get('content-type') || '';
                    if (ok && (ct.includes('application/json') || body.trim().startsWith('{') || body.trim().startsWith('['))) {
                        try { return JSON.parse(body); } catch (e) { throw new Error('Server returned invalid JSON response. Possibly session expired.'); }
                    }
                    if (!ok) {
                        if (ct.includes('application/json')) {
                            try { const data = JSON.parse(body); throw new Error(data.message || JSON.stringify(data)); } catch (e) { throw new Error(body || 'Server error: ' + status); }
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
                .catch(err => { if (err && err.name === 'AbortError') { console.warn('updateCustomerNotes request aborted'); return; } console.error(err); showToast({ type: 'error', title: 'Lỗi', message: err.message || 'Có lỗi xảy ra khi lưu ghi chú', duration: 6000 }); })
                .finally(() => { saveNotesBtn.dataset.saving = '0'; saveNotesBtn.disabled = false; if (window._notesAbortController) { window._notesAbortController = null; } });
            });
            saveNotesBtn.dataset.notesHandlerAttached = '1';
            }

    // ========================================================================
    // PRODUCT -> AUTO-FILL DIAMETER
    // When product selection changes, update diameter input to product's standard diameter
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
        // Initialize to sync current selection
        syncDiameterFromProduct();
    }

        });
    </script>
