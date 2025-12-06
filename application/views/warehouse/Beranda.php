<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Kho nguyên liệu</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Kho nguyên liệu</h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
            <div class="col-6 d-flex text-end">
                <a href="<?= site_url('admin/logout'); ?>" class="btn gradient-dark mb-0">|  <?= lang('btn_logout'); ?>
                <i class="material-icons">arrow_forward</i>
                </a>
            </div>     
            </div>
        </div>
    </nav>
</br>
<div class="content">
    <div class="container-fluid">
        <div class="row pb-3">
            <?php
                $materialsCount = isset($materials) && is_array($materials) ? count($materials) : 0;
                $lowStockCount = 0;
                $needImportForExport = 0;
                if (!empty($materials) && is_array($materials)) {
                    foreach ($materials as $m) {
                        $stock = (int)($m->stock ?? 0);
                        $min   = (int)($m->min_stock ?? 0);
                        $needOut = (int)($m->qty_to_export ?? 0);
                        if ($min > 0 && $stock < $min) { $lowStockCount++; }
                        if ($needOut > $stock) { $needImportForExport++; }
                    }
                }
                $today = date('Y-m-d');
                $inToday = 0; $outToday = 0;
                if (!empty($recent_stock_in) && is_array($recent_stock_in)) {
                    foreach ($recent_stock_in as $r) {
                        $d = substr((string)($r->created_at ?? ''), 0, 10);
                        if ($d === $today) { $inToday++; }
                    }
                }
                if (!empty($recent_stock_out) && is_array($recent_stock_out)) {
                    foreach ($recent_stock_out as $r) {
                        $d = substr((string)($r->created_at ?? ''), 0, 10);
                        if ($d === $today) { $outToday++; }
                    }
                }
            ?>
            <div class="col-five col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">inventory_2</i>
                        </div>
                        <p class="card-category">Tổng số NVL</p>
                        <h3 class="card-title counter"><?= $materialsCount ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">inventory_2</i> Số nguyên vật liệu trong kho
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-five col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-danger card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">report_gmailerrorred</i>
                        </div>
                        <p class="card-category">Dưới tồn tối thiểu</p>
                        <h3 class="card-title counter"><?= $lowStockCount ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">warning</i> Số NVL cần nhập bổ sung
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-five col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">south</i>
                        </div>
                        <p class="card-category">Phiếu nhập hôm nay</p>
                        <h3 class="card-title counter"><?= $inToday ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">south</i> Số phiếu nhập đã ghi nhận
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-five col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">north</i>
                        </div>
                        <p class="card-category">Phiếu xuất hôm nay</p>
                        <h3 class="card-title counter"><?= $outToday ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">north</i> Số phiếu xuất đã ghi nhận
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-five col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">local_shipping</i>
                        </div>
                        <p class="card-category">NVL cần nhập để xuất</p>
                        <h3 class="card-title counter"><?= $needImportForExport ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">assignment</i> Số NVL thiếu để đáp ứng xuất kho
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @media (min-width: 1200px) {
                .col-five { flex: 0 0 20%; max-width: 20%; }
            }
            .card.card-stats { height: 100%; }
        </style>

        <!-- Nút tạo phiếu được dời vào tiêu đề từng bảng lịch sử -->

                <!-- Modals: Phiếu nhập/xuất kho -->
                <div class="modal fade" id="modalStockIn" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title mb-0">Tạo phiếu nhập kho</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" action="<?= site_url('warehouse/save_stock_in'); ?>" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-row g-3">
                                        <div class="form-group col-md-6">
                                            <label class="form-label text-xs mb-1">Nguyên vật liệu</label>
                                            <select name="id_material" class="form-control" required>
                                                <option value="">-- Chọn NVL --</option>
                                                <?php foreach (($materials ?? []) as $m): ?>
                                                        <option value="<?= (int)($m->id_material ?? $m->id) ?>"><?= htmlspecialchars($m->material_name ?? ($m->name ?? '')) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label text-xs mb-1">Số lượng</label>
                                            <input type="number" min="1" name="quantity" class="form-control" required />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label text-xs mb-1">Ngày nhập</label>
                                            <input type="date" name="date_entry" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label text-xs mb-1">Nhà cung cấp</label>
                                            <input type="text" name="supplier" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label text-xs mb-1">Đính kèm</label>
                                            <input type="file" name="attachment" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="redirect_to" value="<?= site_url('warehouse'); ?>" />
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn bg-gradient-primary">Lưu phiếu nhập</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalStockOut" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title mb-0">Tạo phiếu xuất kho</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" action="<?= site_url('warehouse/save_stock_out'); ?>" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-row g-3">
                                        <div class="form-group col-md-5">
                                            <label class="form-label text-xs mb-1">Kế hoạch (tuỳ chọn)</label>
                                            <select name="id_plan" class="form-control">
                                                <option value="">-- Chọn kế hoạch --</option>
                                                <?php foreach (($plans ?? []) as $p): ?>
                                                        <option value="<?= (int)$p->id_plan ?>"><?= htmlspecialchars($p->plan_name) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-label text-xs mb-1">Ca sản xuất (tuỳ chọn)</label>
                                            <select name="id_planshift" class="form-control">
                                                <option value="">-- Chọn ca --</option>
                                                <?php foreach (($shifts ?? []) as $s): ?>
                                                        <option value="<?= (int)$s->id_planshift ?>"><?= htmlspecialchars($s->id_planshift.' - '.($s->ps_name ?? '')) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label text-xs mb-1">Ngày xuất</label>
                                            <input type="date" name="date_out" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-8">
                                            <label class="form-label text-xs mb-1">Ghi chú</label>
                                            <input type="text" name="note" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-label text-xs mb-1">Đính kèm</label>
                                            <input type="file" name="attachment" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="table-responsive" style="max-height:50vh; overflow-y:auto;">
                                        <!-- Table: items by selected plan -->
                                        <table id="tablePlanItems" class="table table-sm table-hover align-items-center d-none">
                                            <thead class="text-warning" style="position: sticky; top: 0; background: #fff; z-index: 1;">
                                                <tr>
                                                    <th>NVL</th>
                                                    <th>Tồn</th>
                                                    <th>Kế hoạch</th>
                                                    <th>Đã xuất</th>
                                                    <th>Còn lại</th>
                                                    <th>SL xuất</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyPlanItems"></tbody>
                                        </table>

                                        <!-- Table: fallback all materials -->
                                        <table id="tableAllMaterials" class="table table-sm table-hover align-items-center">
                                            <thead class="text-warning" style="position: sticky; top: 0; background: #fff; z-index: 1;">
                                                <tr>
                                                    <th>NVL</th>
                                                    <th>Tồn</th>
                                                    <th>SL xuất</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (($materials ?? []) as $m): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($m->material_name ?? ($m->name ?? '')) ?></td>
                                                        <td><?= (int)($m->stock ?? 0) ?></td>
                                                        <td style="max-width:140px;">
                                                            <input type="number" min="0" class="form-control form-control-sm" name="items[<?= (int)($m->id_material ?? $m->id) ?>]" />
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="redirect_to" value="<?= site_url('warehouse'); ?>" />
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn bg-gradient-warning">Lưu phiếu xuất</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

        <!-- Tổng quan nguyên liệu: tồn kho, cần nhập/xuất, tình trạng -->
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header card-header-rose pb-0">
                        <h4 class="card-title">Tổng quan nguyên liệu</h4>
                        <p class="card-category">Tên, tồn kho, cần nhập, cần xuất và tình trạng</p>
                    </div>
                    <div class="card-body table-responsive pt-2">
                        <table class="table table-hover table-sm">
                            <thead class="text-rose">
                                <tr>
                                    <th>#</th>
                                    <th>Mã NVL</th>
                                    <th>Nguyên liệu</th>
                                    <th>Tồn kho</th>
                                    <th>Tồn tối thiểu</th>
                                    <th>Cần nhập</th>
                                    <th>Cần xuất</th>
                                    <th>Tình trạng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach (($materials ?? []) as $m):
                                    $stock = (int)($m->stock ?? 0);
                                    $min   = (int)($m->min_stock ?? 0);
                                    $needIn  = (int)($m->qty_to_import ?? 0);
                                    $needOut = (int)($m->qty_to_export ?? 0);
                                    $u = isset($m->uom) ? $m->uom : 'g';
                                    switch ($u) {
                                        case 'kg': $u_label = 'kg'; break;
                                        case 'g': $u_label = 'g'; break;
                                        case 'pcs': $u_label = 'pcs'; break;
                                        case 'm': $u_label = 'm'; break;
                                        case 'cm': $u_label = 'cm'; break;
                                        case 'box': $u_label = 'box'; break;
                                        default: $u_label = $u; break;
                                    }
                                    $status = 'Đủ';
                                    $badge  = 'success';
                                    if ($stock < $min) { $status = 'Dưới tồn tối thiểu'; $badge='danger'; }
                                    else if ($needOut > $stock) { $status = 'Không đủ để xuất'; $badge='danger'; }
                                    else if ($needIn > 0) { $status = 'Cần nhập'; $badge='warning'; }
                                ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= (int)($m->id_material ?? ($m->id ?? 0)) ?></td>
                                    <td><?= htmlspecialchars($m->material_name ?? ($m->name ?? '')) ?></td>
                                    <td><?= $stock ?> <?= $u_label ?></td>
                                    <td><?= $min ?> <?= $u_label ?></td>
                                    <td><?= $needIn ?> <?= $u_label ?></td>
                                    <td><?= $needOut ?> <?= $u_label ?></td>
                                    <td><span class="badge badge-<?= $badge ?>"><?= $status ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử nhập/xuất kho -->
        <div class="row mt-3">
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Lịch sử nhập kho</h4>
                            <p class="card-category">Các phiếu nhập gần đây</p>
                        </div>
                        <button id="btnToggleIn" class="btn btn-sm btn-success"><i class="material-icons">arrow_downward</i> Tạo phiếu nhập</button>
                    </div>
                    <div class="card-body table-responsive pt-2">
                        <table class="table table-hover table-sm">
                            <thead class="text-primary">
                                <tr>
                                    <th>Thời gian</th>
                                    <th>NVL</th>
                                    <th>SL</th>
                                    <th>Nhà Cung Cấp</th>
                                    <th>Tệp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($recent_stock_in ?? []) as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r->created_at ?? '') ?></td>
                                    <td><?= htmlspecialchars($r->material_name ?? ('#'.(int)($r->id_material ?? 0))) ?></td>
                                    <td>+<?= (int)$r->quantity ?></td>
                                    <td><?= htmlspecialchars($r->supplier ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($r->attachment)): ?>
                                            <a class="btn btn-xs btn-outline-primary" target="_blank" href="<?= base_url($r->attachment) ?>">
                                                <i class="material-icons" style="font-size:16px; vertical-align:middle;">attach_file</i> Xem
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header card-header-info pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Lịch sử xuất kho</h4>
                            <p class="card-category">Các phiếu xuất gần đây</p>
                        </div>
                        <button id="btnToggleOut" class="btn btn-sm btn-warning"><i class="material-icons">arrow_upward</i> Tạo phiếu xuất</button>
                    </div>
                    <div class="card-body table-responsive pt-2">
                        <table class="table table-hover table-sm">
                            <thead class="text-info">
                                <tr>
                                    <th>Thời gian</th>
                                    <th>NVL</th>
                                    <th>SL</th>
                                    <th>Kế hoạch</th>
                                    <th>Tệp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($recent_stock_out ?? []) as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r->created_at ?? '') ?></td>
                                    <td><?= htmlspecialchars($r->material_name ?? ('#'.(int)($r->id_material ?? 0))) ?></td>
                                    <td>-<?= (int)$r->quantity ?></td>
                                    <td><?= htmlspecialchars($r->plan_name ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($r->attachment)): ?>
                                            <a class="btn btn-xs btn-outline-primary" target="_blank" href="<?= base_url($r->attachment) ?>">
                                                <i class="material-icons" style="font-size:16px; vertical-align:middle;">attach_file</i> Xem
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

                <style>
                    /* Subtle spacing for modal forms */
                    .modal .form-row .form-group { margin-bottom: 10px; }
                </style>
                <script>
                    (function(){
                        var btnIn = document.getElementById('btnToggleIn');
                        var btnOut = document.getElementById('btnToggleOut');
                        function showModal(id){
                            var el = document.getElementById(id);
                            if(!el) return;
                            if (window.bootstrap && bootstrap.Modal) { new bootstrap.Modal(el).show(); }
                            else { el.classList.add('show'); el.style.display = 'block'; }
                        }
                        if(btnIn){ btnIn.addEventListener('click', function(){ showModal('modalStockIn'); }); }
                        if(btnOut){ btnOut.addEventListener('click', function(){ showModal('modalStockOut'); }); }
                        // Auto open by query ?open=stock_out or ?open=stock_in
                        var params = new URLSearchParams(window.location.search);
                        var open = params.get('open');
                        if(open === 'stock_out'){ showModal('modalStockOut'); }
                        if(open === 'stock_in'){ showModal('modalStockIn'); }

                        // Plan -> auto populate remaining items
                        var planSelect = document.querySelector('#modalStockOut select[name="id_plan"]');
                        var tablePlan = document.getElementById('tablePlanItems');
                        var tbodyPlan = document.getElementById('tbodyPlanItems');
                        var tableAll = document.getElementById('tableAllMaterials');
                        function setDisabledInputs(container, disabled){
                            if(!container) return;
                            var inputs = container.querySelectorAll('input[name^="items["]');
                            inputs.forEach(function(inp){ inp.disabled = !!disabled; });
                        }
                        function setTableMode(usePlan){
                            if(usePlan){
                                if(tablePlan){ tablePlan.classList.remove('d-none'); setDisabledInputs(tablePlan, false); }
                                if(tableAll){ tableAll.classList.add('d-none'); setDisabledInputs(tableAll, true); }
                            }else{
                                if(tablePlan){ tablePlan.classList.add('d-none'); setDisabledInputs(tablePlan, true); }
                                if(tableAll){ tableAll.classList.remove('d-none'); setDisabledInputs(tableAll, false); }
                            }
                        }
                        async function loadPlanItems(planId){
                            if(!planId){
                                // show all materials
                                if(tbodyPlan) tbodyPlan.innerHTML = '';
                                setTableMode(false);
                                return;
                            }
                            try{
                                const res = await fetch('<?= site_url('warehouse/plan_remaining_materials'); ?>?id_plan=' + encodeURIComponent(planId));
                                const data = await res.json();
                                if(!data || !Array.isArray(data.items)) throw new Error('Bad response');
                                // Build rows
                                let html = '';
                                data.items.forEach(function(item){
                                    const u = item.uom || '';
                                    const stock = parseInt(item.stock||0);
                                    const planned = parseInt(item.planned||0);
                                    const exported = parseInt(item.exported||0);
                                    const remaining = Math.max(0, parseInt(item.remaining||0));
                                    const mid = parseInt(item.id_material||0);
                                    if(mid <= 0) return; // skip unmapped
                                    // Default export qty suggestion: min(stock, remaining)
                                    const suggest = Math.max(0, Math.min(stock, remaining));
                                    const maxAllow = Math.max(0, Math.min(stock, remaining));
                                    html += '<tr>'+
                                        '<td>' + (item.material_name||('#'+mid)) + '</td>'+
                                        '<td>' + stock + ' ' + u + '</td>'+
                                        '<td>' + planned + ' ' + u + '</td>'+
                                        '<td>' + exported + ' ' + u + '</td>'+
                                        '<td>' + remaining + ' ' + u + '</td>'+
                                        '<td style=\"max-width:140px;\"><input type=\"number\" min=\"0\" max=\"'+maxAllow+'\" value=\"'+suggest+'\" class=\"form-control form-control-sm plan-export-input\" name=\"items['+mid+']\" /></td>'+
                                    '</tr>';
                                });
                                if(tbodyPlan) tbodyPlan.innerHTML = html;
                                setTableMode(true);
                            }catch(e){
                                // On error, show all as fallback
                                if(tbodyPlan) tbodyPlan.innerHTML = '';
                                setTableMode(false);
                            }
                        }
                        if(planSelect){
                            planSelect.addEventListener('change', function(){ loadPlanItems(this.value); });
                            // If opened by query with a chosen plan, you can pre-load here later
                        }
                        // init mode: all materials enabled
                        setTableMode(false);
                    })();
                </script>
    </div>
</div>
