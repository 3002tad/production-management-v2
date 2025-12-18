<?php
// View: Form tạo Kế hoạch sản xuất (BGĐ)
// Biến truyền vào (từ controller):
// - $order_options_html: chuỗi <option> cho select đơn hàng (mỗi option có thể đặt data-qty, data-delivery)
// - $machine_options_html: chuỗi <option> cho select dây chuyền (mỗi option có thể đặt data-capacity)
// - $materials: mảng đối tượng nguyên vật liệu (sử dụng để render bảng NVL)
// - $selected_project_id: (tùy chọn) id đơn hàng để auto-select
// Mục đích: hiển thị form tạo kế hoạch sản xuất; form gửi POST về `BOD/storePlan`.
?>
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-8 d-flex align-items-center">
                            <i class="material-icons text-white opacity-10 me-2">playlist_add</i>
                            <h6 class="text-white mb-0">Tạo Kế hoạch sản xuất</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= site_url('BOD/planning'); ?>" class="btn bg-gradient-light mb-0">Quay lại</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-4 pb-4">
                <form method="post" action="<?= isset($plan) ? site_url('BOD/updatePlan') : site_url('BOD/storePlan'); ?>">
                    <?php if (isset($plan) && !empty($plan->id_plan)): ?>
                        <input type="hidden" name="id_plan" value="<?= htmlspecialchars($plan->id_plan, ENT_QUOTES); ?>" />
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                               <!-- Chọn đơn hàng: mỗi <option> trong $order_options_html nên chứa
                                   data-qty (số lượng đơn) và có thể data-delivery (ngày giao) để JS auto-fill -->
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

                            <!-- Số lượng mục tiêu: người dùng có thể chỉnh, hoặc JS sẽ auto-fill từ option.data-qty -->
                            <div class="mb-3">
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <label class="form-label">Tên kế hoạch (tuỳ chọn)</label>
                                        <input type="text" name="plan_name" id="plan_name" class="form-control" maxlength="255" placeholder="Tên kế hoạch" value="<?= isset($plan) ? htmlspecialchars($plan->plan_name ?? '', ENT_QUOTES) : ''; ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <label class="form-label">Số lượng mục tiêu</label>
                                        <input type="number" step="1" name="qty_target" id="qty_target" class="form-control" required value="<?= isset($plan) ? htmlspecialchars($plan->qty_target ?? '', ENT_QUOTES) : ''; ?>" />
                                    </div>
                                </div>
                            </div>

                            <!--
                                Trường ngày (ghi chú):
                                - end_date: Hạn giao (có thể được auto-fill từ option.data-delivery của đơn hàng)
                                - start_date: Ngày bắt đầu (tùy chọn)
                                - finish_date: Ngày kết thúc (tùy chọn)
                                Kiểm tra phía client: start/finish không được sau end_date; kiểm tra thêm trên server là cần thiết.
                            -->
                               <!-- Chọn dây chuyền: option trong $machine_options_html nên chứa data-capacity (đơn vị: sản phẩm/giờ hoặc tương tự)
                                   JS sẽ dùng data-capacity để tính "số ca đề xuất" -->
                               <div class="mb-3">
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <label class="form-label">Hạn giao (tùy chọn)</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" value="" />
                                    </div>
                                </div>
                            </div>

                            <!-- Số ca đề xuất: JS tính dựa trên qty_target và data-capacity của máy; giả định 8 giờ/ca -->
                            <div class="mb-3">
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <label class="form-label">Ngày bắt đầu (tùy chọn)</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= isset($plan) && !empty($plan->start_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->start_date)), ENT_QUOTES) : ''; ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <label class="form-label">Ngày kết thúc (tùy chọn)</label>
                                        <input type="date" name="finish_date" id="finish_date" class="form-control" value="<?= isset($plan) && !empty($plan->finish_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->finish_date)), ENT_QUOTES) : (isset($plan) && !empty($plan->end_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->end_date)), ENT_QUOTES) : ''); ?>" />
                                    </div>
                                </div>
                            </div>

                            <!-- Automatic allocation removed: users cannot auto-create NVL allocations here -->
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
                                            <input type="number" id="suggested_shifts" name="suggested_shifts" class="form-control" min="0" value="<?= isset($plan) ? htmlspecialchars($plan->suggested_shifts ?? '', ENT_QUOTES) : ''; ?>" />
                                            <button type="button" id="calc_shifts" class="btn btn-outline-secondary">Tính số ca</button>
                                        </div>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <label class="form-label">Ghi chú</label>
                                        <textarea name="note" class="form-control" rows="3"><?= isset($plan) ? htmlspecialchars($plan->note ?? '', ENT_QUOTES) : ''; ?></textarea>
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
                                     <!-- Bảng NVL: mỗi hàng có class `material-row` và chứa data-*:
                                         - data-id: id nguyên vật liệu
                                         - data-stock: số lượng tồn
                                         - data-name: tên NVL
                                         Checkbox `.mat-select` để chọn NVL, cột `shortage-cell` sẽ do JS tính và hiển thị -->
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
                                    <textarea name="materials" id="materials" class="form-control" rows="4"><?= isset($plan) ? (is_array($plan->materials) ? htmlspecialchars(json_encode($plan->materials, JSON_UNESCAPED_UNICODE), ENT_QUOTES) : htmlspecialchars($plan->materials ?? '', ENT_QUOTES)) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                                        <!--
                                                Hidden inputs:
                                                - `lines`: lưu thông tin dây chuyền/chỉ dẫn (JS cập nhật khi thay đổi `machine_id`)
                                                    Lưu ý: backend có thể mong đợi một mảng JSON; JS hiện lưu 1 chuỗi JSON của label (xem script).
                                                - `auto_approve`: flag (0/1) do 2 nút submit điều khiển
                                        -->
                                        <input type="hidden" name="lines" id="lines" value='<?= isset($plan) ? (is_string($plan->lines) ? htmlspecialchars($plan->lines, ENT_QUOTES) : htmlspecialchars(json_encode($plan->lines ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES)) : '[]'; ?>' />
                                        <input type="hidden" name="auto_approve" id="auto_approve" value="1" />

                    <div class="text-end">
                        <a href="<?= site_url('BOD/planning'); ?>" class="btn btn-secondary">Hủy</a>
                        <button type="submit" id="btn_save_approve" class="btn btn-success ms-2">Lưu và Duyệt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script chịu trách nhiệm (tóm tắt):
    // - Auto-fill các trường từ option của select (ví dụ: data-qty, data-delivery)
    // - Tính "số ca đề xuất" dựa trên qty_target và data-capacity của máy
    // - Cập nhật cột "Số lượng thiếu" trong bảng NVL theo NVL được chọn
    // - Xây textarea `#materials` chứa danh sách NVL (plain-text)
    // - Gán flag `auto_approve` dựa trên nút submit được bấm
    // - Thực hiện kiểm tra ràng buộc ngày phía client trước khi submit
    // LƯU Ý: mọi kiểm tra phía client là thao tác UX; bắt buộc phải kiểm tra lại trên server.
    const projectSelect = document.getElementById('id_project');
    const qtyInput = document.getElementById('qty_target');
    const endDateInput = document.getElementById('end_date');
    const startDateInput = document.getElementById('start_date');
    const finishDateInput = document.getElementById('finish_date');
    const machineSelect = document.getElementById('machine_id');
    const calcBtn = document.getElementById('calc_shifts');
    const suggestedInput = document.getElementById('suggested_shifts');
    const linesInput = document.getElementById('lines');

    // Nếu có project_id trong URL, auto fill qty
    // Lưu ý client-side: phần JS phía dưới sẽ gợi ý ngày bắt đầu/kết thúc và
    // chặn submit nếu vi phạm ràng buộc ngày (start/finish không được sau end_date).
    if (projectSelect) {
        projectSelect.addEventListener('change', function() {
            const opt = projectSelect.options[projectSelect.selectedIndex];
            if (opt && opt.dataset.qty) {
                qtyInput.value = opt.dataset.qty;
            }
            // Auto-fill end_date from option data-delivery if present
            if (opt && endDateInput && opt.dataset.delivery) {
                // set only when a value exists
                endDateInput.value = opt.dataset.delivery;
            }
            // if end date filled and finish/start empty, set reasonable defaults
            if (endDateInput && endDateInput.value) {
                // prefer using order creation date if provided
                const created = (opt && opt.dataset && opt.dataset.created) ? opt.dataset.created : null;
                if (startDateInput && !startDateInput.value) {
                    if (created) {
                        // ensure created < endDate; else fallback to today
                        const createdDate = created;
                        if (new Date(createdDate) < new Date(endDateInput.value)) {
                            startDateInput.value = createdDate;
                        } else {
                            const today = new Date().toISOString().slice(0,10);
                            startDateInput.value = today < endDateInput.value ? today : endDateInput.value;
                        }
                    } else {
                        const today = new Date().toISOString().slice(0,10);
                        startDateInput.value = today < endDateInput.value ? today : endDateInput.value;
                    }
                }
                if (finishDateInput && !finishDateInput.value) {
                    // default finish to one day before end_date
                    const ed = new Date(endDateInput.value);
                    ed.setDate(ed.getDate() - 1);
                    finishDateInput.value = ed.toISOString().slice(0,10);
                }
            }
            // when project changes, if option provides product id, fetch BOM and auto-select materials
            if (opt && opt.dataset && opt.dataset.productId) {
                const productId = opt.dataset.productId;
                // fetch BOM via AJAX endpoint
                fetch('<?= site_url('BOD/getProductBom'); ?>' + '?id=' + encodeURIComponent(productId), { credentials: 'same-origin' })
                    .then(function(r){ return r.json(); })
                    .then(function(payload){
                        if (payload && payload.success && Array.isArray(payload.materials)) {
                            applyBomToMaterials(payload.materials);
                        }
                    }).catch(function(err){
                        console && console.warn && console.warn('Failed to fetch BOM', err);
                    });
            }
        });

        // tự chọn nếu có param
            // tự chọn nếu controller cung cấp `selected_project_id`
            <?php if (!empty($selected_project_id)): ?>
            (function() {
                    const id = '<?= htmlspecialchars($selected_project_id, ENT_QUOTES); ?>';
                for (let i=0;i<projectSelect.options.length;i++) {
                    if (projectSelect.options[i].value == id) { projectSelect.selectedIndex = i; projectSelect.dispatchEvent(new Event('change')); break; }
                }

                // Try to select machine when editing: prefer saved machine_id, then try to match lines label
                const existingMachineId = '<?= htmlspecialchars($plan->machine_id ?? '', ENT_QUOTES); ?>';
                const existingLines = <?= isset($plan->lines) ? json_encode($plan->lines) : 'null'; ?>;
                setTimeout(function(){
                    try {
                        if (existingMachineId && machineSelect) {
                            for (let j=0;j<machineSelect.options.length;j++) {
                                if (machineSelect.options[j].value == existingMachineId) { machineSelect.selectedIndex = j; machineSelect.dispatchEvent(new Event('change')); break; }
                            }
                        } else if (existingLines && machineSelect) {
                            // lines might be array or string
                            let labelToMatch = null;
                            if (Array.isArray(existingLines) && existingLines.length>0) labelToMatch = existingLines[0];
                            else if (typeof existingLines === 'string') {
                                try { const parsed = JSON.parse(existingLines); if (Array.isArray(parsed) && parsed.length>0) labelToMatch = parsed[0]; else labelToMatch = existingLines; } catch(e){ labelToMatch = existingLines; }
                            }
                            if (labelToMatch) {
                                let matched = false;
                                for (let j=0;j<machineSelect.options.length;j++) {
                                    const txt = (machineSelect.options[j].textContent || machineSelect.options[j].innerText || '').trim();
                                    if (txt.indexOf(labelToMatch) !== -1) { machineSelect.selectedIndex = j; machineSelect.dispatchEvent(new Event('change')); matched = true; break; }
                                }
                                if (!matched) {
                                    // Try to extract capacity number from labelToMatch and match by data-capacity
                                    const capMatch = (labelToMatch || '').match(/(\d+(?:\.\d+)?)/);
                                    if (capMatch && capMatch[1]) {
                                        const want = parseFloat(capMatch[1]);
                                        for (let j=0;j<machineSelect.options.length;j++) {
                                            const cap = parseFloat(machineSelect.options[j].dataset.capacity || 0);
                                            if (!isNaN(cap) && Math.abs(cap - want) < 0.001) { machineSelect.selectedIndex = j; machineSelect.dispatchEvent(new Event('change')); matched = true; break; }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (e) { console && console.warn && console.warn('Select saved machine failed', e); }
                }, 50);
            })();
            <?php endif; ?>
    }

    // Biến lưu giá trị số ca do hệ thống tính tự động (dùng để so sánh khi người dùng nhập tay)
    let autoCalculatedShifts = 0;

    // Tính số ca đề xuất dựa trên `qty_target` và `data-capacity` của máy (8 giờ/ca)
    // Ghi chú (tiếng Việt):
    // - `autoCalculatedShifts` lưu lại giá trị tối thiểu hệ thống đề xuất.
    // - Người dùng có thể nhập tay vào `#suggested_shifts`; khi giá trị nhập tay nhỏ hơn
    //   `autoCalculatedShifts` => nghĩa là vượt quá công suất (không đủ giờ để hoàn thành),
    //   sẽ báo lỗi khi submit.
    function computeShifts() {
        const qty = parseFloat(qtyInput.value) || 0;
        const machineOpt = machineSelect ? machineSelect.options[machineSelect.selectedIndex] : null;
        const capacity = machineOpt ? parseFloat(machineOpt.dataset.capacity || 0) : 0;
        const shiftHours = 8; // giả định

        if (!capacity || capacity <= 0) {
            alert('Không có dữ liệu công suất. Vui lòng chọn dây chuyền.');
            autoCalculatedShifts = 0;
            suggestedInput.value = '';
            return;
        }

        const hoursNeeded = qty / capacity;
        const shifts = Math.max(0, Math.ceil(hoursNeeded / shiftHours));
        autoCalculatedShifts = shifts;
        suggestedInput.value = shifts;
    }
    // Cho phép tính khi bấm nút và khi thay đổi input liên quan
    calcBtn.addEventListener('click', computeShifts);
    if (qtyInput) qtyInput.addEventListener('change', computeShifts);
    if (machineSelect) machineSelect.addEventListener('change', computeShifts);

    // Update hidden `lines` input when machine (dây chuyền) selection changes.
    function updateLinesInput() {
        if (!linesInput) return;
        if (!machineSelect) { linesInput.value = '[]'; return; }
        const opt = machineSelect.options[machineSelect.selectedIndex];
        if (!opt || !opt.value) {
            linesInput.value = '[]';
            return;
        }
        // store only the label text (e.g. "Dây chuyền 1") as a JSON string
        const label = (opt.textContent || opt.innerText || '').trim();
        linesInput.value = JSON.stringify(label);
    }
    if (machineSelect) {
        machineSelect.addEventListener('change', updateLinesInput);
    }
    // initialize on load
    updateLinesInput();

    // Apply BOM materials array to the materials table
    // Expected format: [{id_material: 123, material_name: 'Cuộn mực', quantity: 0.5, unit: 'g'}, ...]
    function applyBomToMaterials(bomMaterials) {
        if (!Array.isArray(bomMaterials)) return;
        // clear any previous BOM markers and previous missing rows
        document.querySelectorAll('tr.material-row').forEach(function(r){
            delete r.dataset.bomPerUnit;
            delete r.dataset.required;
            const cb = r.querySelector('input.mat-select');
            if (cb) { cb.checked = false; r.classList.remove('table-active'); }
        });
        document.querySelectorAll('tr.material-missing-row').forEach(function(r){ r.remove(); });

        const orderQty = getOrderQty();

        bomMaterials.forEach(function(mat){
            const id = mat.id_material || mat.id || mat.material_id || mat.materialId || null;
            const per = parseFloat(mat.quantity || mat.qty || mat.amount || mat.quantity_per_unit || 0) || 0;

            if (id) {
                const row = document.querySelector('tr.material-row[data-id="' + id + '"]');
                if (!row) {
                    // material has an id but not present in the materials table: treat as missing
                    createMissingRow(mat, per, orderQty);
                    return;
                }
                // mark per-unit requirement on row
                row.dataset.bomPerUnit = per;
                const cb = row.querySelector('input.mat-select');
                if (cb) { cb.checked = true; row.classList.add('table-active'); }
                return;
            }

            // No id provided in BOM => material not in material table. Create missing warning row.
            createMissingRow(mat, per, orderQty);
        });

        // recalc shortages display
        updateShortages();
    }

    function createMissingRow(mat, per, orderQty) {
        try {
            const tbody = document.querySelector('table.table tbody');
            if (!tbody) return;
            const name = mat.material_name || mat.name || ('Mã: ' + (mat.id_material || mat.id || '-'));
            // Compute required quantity: prefer per-unit * orderQty when per is provided
            const requiredQty = per && orderQty ? (per * orderQty) : orderQty;

            const tr = document.createElement('tr');
            tr.className = 'material-missing-row table-danger text-white';
            tr.setAttribute('data-missing', '1');
            tr.setAttribute('data-name', name);
            tr.setAttribute('data-required', requiredQty);
            tr.innerHTML = '\n                <td class="align-middle text-center">&nbsp;</td>\n                <td>-</td>\n                <td>' + name + ' <small class="text-white" style="opacity:0.9">(Chưa có trong kho)</small></td>\n                <td class="text-end stock-cell">0</td>\n                <td class="text-end shortage-cell">' + formatNumber(requiredQty) + '</td>\n            ';
            // append at end of materials table body
            tbody.appendChild(tr);
        } catch (e) {
            console && console.warn && console.warn('Failed to create missing material row', e);
        }
    }

    function formatNumber(n) {
        if (n === null || n === undefined) return '-';
        if (Number.isInteger(n)) return n.toLocaleString('en-US');
        return parseFloat(n).toFixed(2);
    }

    // Update shortage column: shortage = stock - orderQty
    // LƯU Ý: hiện tại code dùng Math.abs(stock - orderQty) => luôn hiển thị giá trị dương.
    // Nếu mong muốn chỉ hiển thị "thiếu" (khi orderQty > stock), hãy dùng: Math.max(0, orderQty - stock).
    // Đồng bộ công thức này cho cả phần hiển thị trong bảng và textarea `#materials`.
    function getOrderQty() {
        const qtyFromInput = parseFloat(qtyInput.value || 0) || 0;
        if (qtyFromInput && qtyFromInput > 0) return qtyFromInput;
        // fallback to selected project's data-qty
        if (projectSelect) {
            const opt = projectSelect.options[projectSelect.selectedIndex];
            if (opt && opt.dataset && opt.dataset.qty) {
                return parseFloat(opt.dataset.qty) || 0;
            }
        }
        return 0;
    }

    function updateShortages() {
        const orderQty = getOrderQty();
        const rows = document.querySelectorAll('tr.material-row');
        rows.forEach(function(row) {
            const stock = parseFloat(row.dataset.stock || 0) || 0;
            const checkbox = row.querySelector('input.mat-select');
            const cell = row.querySelector('.shortage-cell');
            if (!cell) return;

            if (checkbox && checkbox.checked) {
                // If BOM per-unit exists on row, prefer per * orderQty, otherwise use orderQty
                const per = parseFloat(row.dataset.bomPerUnit || 0) || 0;
                const requiredQty = per && orderQty ? (per * orderQty) : orderQty;

                const shortage = Math.max(0, requiredQty - stock);
                const display = Number.isInteger(shortage) ? shortage.toLocaleString('en-US') : shortage.toFixed(2);
                cell.textContent = display;
            } else {
                cell.textContent = '-';
            }
        });

        // Handle missing material rows (auto-warning rows): treat their stock as 0 and show required quantity
        document.querySelectorAll('tr.material-missing-row').forEach(function(row) {
            const cell = row.querySelector('.shortage-cell');
            if (!cell) return;
            const required = parseFloat(row.dataset.required || 0) || 0;
            cell.textContent = Number.isInteger(required) ? required.toLocaleString('en-US') : required.toFixed(2);
        });

        // After updating visible shortage cells, update the textarea with selected materials
        updateMaterialsTextarea();
    }

    // Build plain-text list of selected materials (format: "Tên nguyên vật liệu — Số lượng thiếu")
    // Ghi chú: công thức tính shortage ở đây phải khớp với updateShortages();
    // hiện đang sử dụng Math.abs(stock - orderQty) — xem lưu ý ở trên nếu cần khác.
    function updateMaterialsTextarea() {
        const orderQty = getOrderQty();
        const lines = [];
        document.querySelectorAll('tr.material-row').forEach(function(row) {
            const cb = row.querySelector('input.mat-select');
            if (!cb || !cb.checked) return;
            const stock = parseFloat(row.dataset.stock || 0) || 0;
            const name = row.dataset.name || (row.querySelector('td:nth-child(3)') ? row.querySelector('td:nth-child(3)').textContent.trim() : '');
            // If BOM per-unit exists on row, prefer per * orderQty for required calculation
            const per = parseFloat(row.dataset.bomPerUnit || 0) || 0;
            const requiredQty = per && orderQty ? (per * orderQty) : orderQty;
            const shortage = Math.max(0, requiredQty - stock);
            const display = Number.isInteger(shortage) ? shortage.toLocaleString('en-US') : shortage.toFixed(2);
            lines.push(name + ' — Yêu cầu: ' + (Number.isInteger(requiredQty) ? requiredQty.toLocaleString('en-US') : requiredQty.toFixed(2)) + ' — Thiếu: ' + display);
        });

        // Include missing NVL rows (always include them in the textarea so planners see what is not in stock)
        document.querySelectorAll('tr.material-missing-row').forEach(function(row) {
            const name = row.dataset.name || (row.querySelector('td:nth-child(3)') ? row.querySelector('td:nth-child(3)').textContent.trim() : '');
            const required = parseFloat(row.dataset.required || 0) || 0;
            const displayReq = Number.isInteger(required) ? required.toLocaleString('en-US') : required.toFixed(2);
            lines.push(name + ' — Yêu cầu: ' + displayReq + ' — Thiếu: ' + displayReq + ' (Chưa có trong kho)');
        });

        const ta = document.getElementById('materials');
        if (!ta) return;
        ta.value = lines.join('\n');
    }

    // Update when qty or project changes
    if (qtyInput) qtyInput.addEventListener('input', updateShortages);
    if (projectSelect) projectSelect.addEventListener('change', updateShortages);

    // Checkbox interactions: clicking row toggles checkbox; checkbox change triggers update
    document.querySelectorAll('tr.material-row').forEach(function(r) {
        const cb = r.querySelector('input.mat-select');
        r.addEventListener('click', function(e) {
            // avoid toggling twice when clicking checkbox itself
            if (e.target && e.target.tagName === 'INPUT') return;
            if (cb) cb.checked = !cb.checked;
            r.classList.toggle('table-active', cb && cb.checked);
            updateShortages();
        });
        if (cb) {
            cb.addEventListener('change', function() {
                r.classList.toggle('table-active', cb.checked);
                updateShortages();
            });
        }
    });

    // Select all checkbox
    const selectAll = document.getElementById('select_all_materials');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checked = !!selectAll.checked;
            document.querySelectorAll('input.mat-select').forEach(function(cb) {
                cb.checked = checked;
                const row = cb.closest('tr');
                if (row) row.classList.toggle('table-active', checked);
            });
            updateShortages();
        });
    }

    // Initial calculation on load
    updateShortages();

    // Client-side: control auto_approve flag via buttons, and validation
    const autoApproveInput = document.getElementById('auto_approve');
    const btnSave = document.getElementById('btn_save');
    const btnSaveApprove = document.getElementById('btn_save_approve');

    // Ensure default value
    if (autoApproveInput) autoApproveInput.value = '0';

    if (btnSave) {
        btnSave.addEventListener('click', function() {
            if (autoApproveInput) autoApproveInput.value = '0';
        });
    }
    if (btnSaveApprove) {
        btnSaveApprove.addEventListener('click', function() {
            if (autoApproveInput) autoApproveInput.value = '1';
        });
    }

    // Client-side validation: start_date and finish_date must not be later than delivery (end_date)
    const form = document.querySelector('form[method="post"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // luôn ngăn submit mặc định để chạy kiểm tra async

            const endVal = endDateInput ? endDateInput.value : '';
            const startVal = startDateInput ? startDateInput.value : '';
            const finishVal = finishDateInput ? finishDateInput.value : '';

            function isAfter(a, b) {
                if (!a || !b) return false;
                return new Date(a) > new Date(b);
            }

            // Kiểm tra ngày (client-side) — nếu sai sẽ dừng
            if (endVal) {
                if (isAfter(startVal, endVal)) {
                    alert('Ngày bắt đầu không được trễ hơn hạn giao');
                    return false;
                }
                // `finish_date` must be strictly before `end_date` (hạn giao)
                if (finishVal && endVal && (new Date(finishVal) >= new Date(endVal))) {
                    alert('Ngày kết thúc phải trước hạn giao');
                    return false;
                }
                // Ensure finish is at least 1 day before end date
                if (finishVal && endVal) {
                    const ed = new Date(endVal);
                    const f = new Date(finishVal);
                    ed.setDate(ed.getDate() - 1);
                    if (f > ed) {
                        alert('Ngày kết thúc phải trước hạn giao ít nhất 1 ngày');
                        return false;
                    }
                }
            }
            if (startVal && finishVal && isAfter(startVal, finishVal)) {
                alert('Ngày bắt đầu không được sau ngày kết thúc');
                return false;
            }
            // Ensure start is not before order creation date (if provided)
            const selOpt = projectSelect ? projectSelect.options[projectSelect.selectedIndex] : null;
            if (selOpt && selOpt.dataset && selOpt.dataset.created && startVal) {
                const createdDate = new Date(selOpt.dataset.created);
                if (new Date(startVal) < createdDate) {
                    alert('Ngày bắt đầu không được trước ngày nhận đơn hàng');
                    return false;
                }
            }

            // Kiểm tra số ca: nếu người dùng nhập tay số ca nhỏ hơn số ca hệ thống đề xuất => báo lỗi
            // Lưu ý: autoCalculatedShifts được cập nhật khi bấm 'Tính số ca' hoặc khi thay đổi qty/machine
            const userShifts = parseInt((suggestedInput && suggestedInput.value) ? suggestedInput.value : 0) || 0;
            if (autoCalculatedShifts && userShifts < autoCalculatedShifts) {
                // Thông báo tiếng Việt rõ ràng theo yêu cầu
                alert('Số ca nhập vào nhỏ hơn số ca tối thiểu đề xuất — vượt quá công suất. Vui lòng kiểm tra lại.');
                return false;
            }

            // Nếu qua hết kiểm tra, submit form
            form.submit();
            return false;
        });
    }
});
</script>
<?php if (isset($plan)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // set machine selection if available — try multiple possible property names
        var mval = '';
        try {
            mval = '<?= htmlspecialchars($plan->machine_id ?? $plan->id_machine ?? $plan->machine ?? $plan->machineId ?? '', ENT_QUOTES); ?>';
        } catch (e) { mval = '<?= htmlspecialchars($plan->machine_id ?? '', ENT_QUOTES); ?>'; }
        if (mval) {
            var ms = document.getElementById('machine_id');
            if (ms) {
                for (var i=0;i<ms.options.length;i++) {
                    if (String(ms.options[i].value) == String(mval)) {
                        ms.selectedIndex = i;
                        // trigger change so existing handlers run
                        ms.dispatchEvent(new Event('change'));
                        // also trigger qty change and calc button in case listeners need it
                        try {
                            var qtyEl = document.getElementById('qty_target');
                            if (qtyEl) qtyEl.dispatchEvent(new Event('input'));
                            var calcBtn = document.getElementById('calc_shifts');
                            if (calcBtn) calcBtn.click();
                        } catch (e) { /* ignore */ }
                        break;
                    }
                }
            }
        }
        // ensure project is selected (controller also tries to mark it)
        var pval = '<?= htmlspecialchars($plan->id_project ?? '', ENT_QUOTES); ?>';
        if (pval) {
            var ps = document.getElementById('id_project');
            if (ps) {
                for (var j=0;j<ps.options.length;j++) {
                    if (ps.options[j].value == pval) {
                        ps.selectedIndex = j;
                        // dispatch change to trigger other handlers (like BOM fetch),
                        // but restore only the plan's finish date into #finish_date so
                        // the project's delivery (auto-filled into #end_date) remains.
                        ps.dispatchEvent(new Event('change'));
                        try {
                            var planFinish = '<?= isset($plan->finish_date) && !empty($plan->finish_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->finish_date)), ENT_QUOTES) : (isset($plan->end_date) && !empty($plan->end_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->end_date)), ENT_QUOTES) : ''); ?>';
                            if (planFinish) {
                                var fd = document.getElementById('finish_date');
                                if (fd) fd.value = planFinish;
                            }
                        } catch (e) { console && console.warn && console.warn('Restore plan finish date failed', e); }
                        break;
                    }
                }
            }
        }
    } catch (e) { console && console.warn && console.warn('Prefill script error', e); }
});
</script>
<?php endif; ?>
