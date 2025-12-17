<?php
// View: Chỉnh sửa Kế hoạch sản xuất (BGĐ)
// Biến truyền vào (từ controller):
// - $plan: object hoặc array chứa dữ liệu kế hoạch hiện tại (id_plan, id_project, plan_name, qty_target, end_date, start_date, finish_date, machine_id, suggested_shifts, note, materials, lines)
// - $order_options_html, $machine_options_html, $materials
?>
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-8 d-flex align-items-center">
                            <i class="material-icons text-white opacity-10 me-2">edit</i>
                            <h6 class="text-white mb-0">Chỉnh sửa Kế hoạch sản xuất</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= site_url('BOD/planning'); ?>" class="btn bg-gradient-light mb-0">Quay lại</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-4 pb-4">
                <?php if (empty($plan)): ?>
                    <div class="alert alert-danger">Không có dữ liệu kế hoạch để chỉnh sửa.</div>
                <?php else: ?>
                <form method="post" action="<?= site_url('BOD/updatePlan'); ?>">
                    <input type="hidden" name="id_plan" value="<?= htmlspecialchars($plan->id_plan ?? ($plan['id_plan'] ?? ''), ENT_QUOTES); ?>" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Chọn đơn hàng (đã duyệt)</label>
                                                <select id="id_project" name="id_project" class="form-select" required>
                                                    <option value="">-- Chọn đơn hàng --</option>
                                                    <?= $order_options_html ?? ''; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Tên kế hoạch (tuỳ chọn)</label>
                                                <input type="text" name="plan_name" id="plan_name" class="form-control" maxlength="255" placeholder="Tên kế hoạch" value="<?= htmlspecialchars($plan->plan_name ?? ($plan['plan_name'] ?? ''), ENT_QUOTES); ?>" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Số lượng mục tiêu</label>
                                                <input type="number" step="1" name="qty_target" id="qty_target" class="form-control" required value="<?= htmlspecialchars($plan->qty_target ?? ($plan['qty_target'] ?? ''), ENT_QUOTES); ?>" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Hạn giao (tùy chọn)</label>
                                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= !empty($plan->end_date ?? ($plan['end_date'] ?? '')) ? date('Y-m-d', strtotime($plan->end_date ?? ($plan['end_date'] ?? ''))) : ''; ?>" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Ngày bắt đầu (tùy chọn)</label>
                                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= !empty($plan->start_date ?? ($plan['start_date'] ?? '')) ? date('Y-m-d', strtotime($plan->start_date ?? ($plan['start_date'] ?? ''))) : ''; ?>" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Ngày kết thúc (tùy chọn)</label>
                                                <input type="date" name="finish_date" id="finish_date" class="form-control" value="<?= !empty($plan->finish_date ?? ($plan['finish_date'] ?? '')) ? date('Y-m-d', strtotime($plan->finish_date ?? ($plan['finish_date'] ?? ''))) : ''; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Chọn dây chuyền</label>
                                                <select id="machine_id" name="machine_id" class="form-select">
                                                    <option value="">-- Không chọn --</option>
                                                    <?= $machine_options_html ?? ''; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Số ca đề xuất(8 giờ/ca)</label>
                                                <div class="input-group">
                                                    <input type="number" id="suggested_shifts" name="suggested_shifts" class="form-control" min="0" value="<?= htmlspecialchars($plan->suggested_shifts ?? ($plan['suggested_shifts'] ?? ''), ENT_QUOTES); ?>" />
                                                    <button type="button" id="calc_shifts" class="btn btn-outline-secondary">Tính số ca</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <label class="form-label">Ghi chú</label>
                                                <textarea name="note" class="form-control" rows="3"><?= htmlspecialchars($plan->note ?? ($plan['note'] ?? ''), ENT_QUOTES); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="mb-3">
                        <div class="card border-secondary mb-3">
                            <div class="card-body">
                                 <h6 class="mb-3">Bảng Nguyên vật liệu</h6>
                                <?php $materials = isset($materials) ? $materials : []; ?>
                                <?php if (!empty($materials)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width:40px;"><input type="checkbox" id="select_all_materials" title="Chọn tất cả" /></th>
                                                    <th>Mã</th>
                                                    <th>Tên nguyên vật liệu</th>
                                                    <th class="text-end">Số lượng tồn</th>
                                                    <th class="text-end">Số lượng thiếu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($materials as $mat): ?>
                                                    <?php $mat_id = $mat->id_material ?? ($mat->id ?? ''); $mat_name = $mat->material_name ?? ($mat->name ?? ''); $mat_stock = isset($mat->stock) ? $mat->stock : 0; ?>
                                                    <tr class="material-row" data-id="<?= htmlspecialchars($mat_id, ENT_QUOTES); ?>" data-stock="<?= htmlspecialchars($mat_stock, ENT_QUOTES); ?>" data-name="<?= htmlspecialchars($mat_name, ENT_QUOTES); ?>">
                                                        <td class="align-middle text-center"><input type="checkbox" class="mat-select" value="<?= htmlspecialchars($mat_id, ENT_QUOTES); ?>" /></td>
                                                        <td><?= htmlspecialchars($mat_id, ENT_QUOTES); ?></td>
                                                        <td><?= htmlspecialchars($mat_name, ENT_QUOTES); ?></td>
                                                        <td class="text-end stock-cell"><?= number_format($mat_stock); ?></td>
                                                        <td class="text-end shortage-cell">-</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Không có dữ liệu nguyên vật liệu.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card border-secondary mb-3">
                            <div class="card-body">
                                <label class="form-label">Danh sách NVL — tuỳ chọn</label>
                                <textarea name="materials" id="materials" class="form-control" rows="4"><?= htmlspecialchars(is_array($plan->materials ?? ($plan['materials'] ?? '')) ? (is_string($plan->materials ?? ($plan['materials'] ?? '')) ? $plan->materials : json_encode($plan->materials)) : ($plan->materials ?? ($plan['materials'] ?? '')), ENT_QUOTES); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="lines" id="lines" value='<?= htmlspecialchars(is_string($plan->lines ?? ($plan['lines'] ?? '')) ? ($plan->lines ?? ($plan['lines'] ?? '')) : json_encode($plan->lines ?? ($plan['lines'] ?? []), JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?>' />
                    <input type="hidden" name="auto_approve" id="auto_approve" value="0" />

                    <div class="text-end">
                        <a href="<?= site_url('BOD/planning'); ?>" class="btn btn-secondary">Hủy</a>
                        <button type="submit" id="btn_save" class="btn btn-primary">Lưu Kế hoạch</button>
                        <button type="submit" id="btn_save_approve" class="btn btn-success ms-2">Lưu và Duyệt</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('id_project');
    const qtyInput = document.getElementById('qty_target');
    const endDateInput = document.getElementById('end_date');
    const startDateInput = document.getElementById('start_date');
    const finishDateInput = document.getElementById('finish_date');
    const machineSelect = document.getElementById('machine_id');
    const calcBtn = document.getElementById('calc_shifts');
    const suggestedInput = document.getElementById('suggested_shifts');
    const linesInput = document.getElementById('lines');

    // If server provided plan values, try to select corresponding options
    (function applyServerValues() {
        try {
            var planProject = '<?= htmlspecialchars($plan->id_project ?? ($plan['id_project'] ?? ''), ENT_QUOTES); ?>';
            if (projectSelect && planProject) {
                for (let i=0;i<projectSelect.options.length;i++) {
                    if (projectSelect.options[i].value == planProject) { projectSelect.selectedIndex = i; break; }
                }
            }
            var planMachine = '<?= htmlspecialchars($plan->machine_id ?? ($plan['machine_id'] ?? ''), ENT_QUOTES); ?>';
            if (machineSelect && planMachine) {
                for (let i=0;i<machineSelect.options.length;i++) {
                    if (machineSelect.options[i].value == planMachine) { machineSelect.selectedIndex = i; break; }
                }
            }
        } catch(e) { /* ignore */ }
    })();

    // The rest of JS mirrors behavior in PlanCreate to compute shifts, update materials, validate dates
    let autoCalculatedShifts = 0;

    function computeShifts() {
        const qty = parseFloat(qtyInput.value) || 0;
        const machineOpt = machineSelect ? machineSelect.options[machineSelect.selectedIndex] : null;
        const capacity = machineOpt ? parseFloat(machineOpt.dataset.capacity || 0) : 0;
        const shiftHours = 8;

        if (!capacity || capacity <= 0) {
            autoCalculatedShifts = 0;
            if (suggestedInput) suggestedInput.value = suggestedInput.value || '';
            return;
        }

        const hoursNeeded = qty / capacity;
        const shifts = Math.max(0, Math.ceil(hoursNeeded / shiftHours));
        autoCalculatedShifts = shifts;
        if (suggestedInput) suggestedInput.value = shifts;
    }

    if (calcBtn) calcBtn.addEventListener('click', computeShifts);
    if (qtyInput) qtyInput.addEventListener('change', computeShifts);
    if (machineSelect) machineSelect.addEventListener('change', computeShifts);

    function updateLinesInput() {
        if (!linesInput) return;
        if (!machineSelect) { linesInput.value = '[]'; return; }
        const opt = machineSelect.options[machineSelect.selectedIndex];
        if (!opt || !opt.value) { linesInput.value = '[]'; return; }
        const label = (opt.textContent || opt.innerText || '').trim();
        linesInput.value = JSON.stringify(label);
    }
    if (machineSelect) machineSelect.addEventListener('change', updateLinesInput);
    updateLinesInput();

    // Materials table interactions (simplified: mirror PlanCreate behavior)
    function getOrderQty() { const v = parseFloat(qtyInput.value || 0) || 0; return v; }
    function updateShortages() {
        const orderQty = getOrderQty();
        const rows = document.querySelectorAll('tr.material-row');
        rows.forEach(function(row){
            const stock = parseFloat(row.dataset.stock || 0) || 0;
            const checkbox = row.querySelector('input.mat-select');
            const cell = row.querySelector('.shortage-cell');
            if (!cell) return;
            if (checkbox && checkbox.checked) {
                const requiredQty = orderQty;
                const shortage = Math.max(0, requiredQty - stock);
                const display = Number.isInteger(shortage) ? shortage.toLocaleString('en-US') : shortage.toFixed(2);
                cell.textContent = display;
            } else { cell.textContent = '-'; }
        });
        updateMaterialsTextarea();
    }
    function updateMaterialsTextarea() {
        const orderQty = getOrderQty();
        const lines = [];
        document.querySelectorAll('tr.material-row').forEach(function(row){
            const cb = row.querySelector('input.mat-select'); if (!cb || !cb.checked) return;
            const stock = parseFloat(row.dataset.stock || 0) || 0;
            const name = row.dataset.name || (row.querySelector('td:nth-child(3)') ? row.querySelector('td:nth-child(3)').textContent.trim() : '');
            const requiredQty = orderQty;
            const shortage = Math.max(0, requiredQty - stock);
            const display = Number.isInteger(shortage) ? shortage.toLocaleString('en-US') : shortage.toFixed(2);
            lines.push(name + ' — Yêu cầu: ' + (Number.isInteger(requiredQty) ? requiredQty.toLocaleString('en-US') : requiredQty.toFixed(2)) + ' — Thiếu: ' + display);
        });
        const ta = document.getElementById('materials'); if (!ta) return; ta.value = lines.join('\n');
    }

    document.querySelectorAll('tr.material-row').forEach(function(r){
        const cb = r.querySelector('input.mat-select');
        r.addEventListener('click', function(e){ if (e.target && e.target.tagName === 'INPUT') return; if (cb) cb.checked = !cb.checked; r.classList.toggle('table-active', cb && cb.checked); updateShortages(); });
        if (cb) cb.addEventListener('change', function(){ r.classList.toggle('table-active', cb.checked); updateShortages(); });
    });
    const selectAll = document.getElementById('select_all_materials'); if (selectAll) { selectAll.addEventListener('change', function(){ const checked = !!selectAll.checked; document.querySelectorAll('input.mat-select').forEach(function(cb){ cb.checked = checked; const row = cb.closest('tr'); if (row) row.classList.toggle('table-active', checked); }); updateShortages(); }); }
    updateShortages();

    // auto_approve control and validation
    const autoApproveInput = document.getElementById('auto_approve');
    const btnSave = document.getElementById('btn_save');
    const btnSaveApprove = document.getElementById('btn_save_approve');
    if (autoApproveInput) autoApproveInput.value = '0';
    if (btnSave) btnSave.addEventListener('click', function(){ if (autoApproveInput) autoApproveInput.value = '0'; });
    if (btnSaveApprove) btnSaveApprove.addEventListener('click', function(){ if (autoApproveInput) autoApproveInput.value = '1'; });

    const form = document.querySelector('form[method="post"]');
    if (form) {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            const endVal = endDateInput ? endDateInput.value : '';
            const startVal = startDateInput ? startDateInput.value : '';
            const finishVal = finishDateInput ? finishDateInput.value : '';
            function isAfter(a,b){ if (!a||!b) return false; return new Date(a) > new Date(b); }
            if (endVal) {
                if (isAfter(startVal, endVal)) { alert('Ngày bắt đầu không được trễ hơn hạn giao'); return false; }
                if (finishVal && endVal && (new Date(finishVal) >= new Date(endVal))) { alert('Ngày kết thúc phải trước hạn giao'); return false; }
            }
            if (startVal && finishVal && isAfter(startVal, finishVal)) { alert('Ngày bắt đầu không được sau ngày kết thúc'); return false; }
            const userShifts = parseInt((suggestedInput && suggestedInput.value) ? suggestedInput.value : 0) || 0;
            if (autoCalculatedShifts && userShifts < autoCalculatedShifts) { alert('Số ca nhập vào nhỏ hơn số ca tối thiểu đề xuất — vượt quá công suất. Vui lòng kiểm tra lại.'); return false; }
            form.submit(); return false;
        });
    }
});
</script>
