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
                <a href="<?= site_url('warehouse/logout'); ?>" class="btn gradient-dark mb-0">|  <?= lang('btn_logout'); ?>
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
            
            .history-container {
                max-height: 280px;
                overflow: hidden;
                transition: max-height 0.3s ease-in-out;
                position: relative;
            }
            .history-container.expanded {
                max-height: 1000px;
            }
            .history-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 40px;
                background: linear-gradient(transparent, rgba(255,255,255,0.8));
                pointer-events: none;
                display: block;
            }
            .history-container.expanded .history-overlay {
                display: none;
            }
            
            /* Style for filter inputs */
            .filter-row .input-group {
                margin-bottom: 0;
            }
            .filter-row select.form-control, 
            .filter-row input.form-control {
                border: 1px solid #d2d6da !important;
                border-radius: 4px !important;
                padding: 4px 8px !important;
                background-image: none !important;
            }
            .filter-row select.form-control:focus, 
            .filter-row input.form-control:focus {
                border-color: #e91e63 !important;
                box-shadow: inset 0 0 0 1px #e91e63 !important;
            }
            .filter-row label {
                margin-bottom: 2px;
                font-size: 12px;
                font-weight: 600;
                color: #7b809a;
            }
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
                                            <input type="date" name="date_entry" class="form-control" required />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label text-xs mb-1">Nhà cung cấp</label>
                                            <input type="text" name="supplier" class="form-control" required />
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
                                        <div class="form-group col-md-4">
                                            <label class="form-label text-xs mb-1">Kế hoạch</label>
                                            <select name="id_plan" class="form-control" required>
                                                <option value="">-- Chọn kế hoạch --</option>
                                                <?php foreach (($plans ?? []) as $p): ?>
                                                        <option value="<?= (int)$p->id_plan ?>"><?= htmlspecialchars($p->plan_name) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label text-xs mb-1">Ngày xuất</label>
                                            <input type="date" name="date_out" class="form-control" id="dateOutInput" required />
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label class="form-label text-xs mb-1 font-weight-bold text-info">Ca sản xuất (Bắt buộc)</label>
                                            <select name="id_planshift" class="form-control border border-info" id="shiftSelect" required style="background-color: #f8fdff; font-weight: bold;">
                                                <option value="">-- Chọn ca sản xuất --</option>
                                                <?php foreach (($shifts ?? []) as $s): ?>
                                                        <option value="<?= (int)$s->id_planshift ?>" data-date="<?= $s->shift_date ?? '' ?>"><?= htmlspecialchars($s->ps_name ?? '') ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted" style="font-size: 10px;">Chọn ca để hiển thị danh sách NVL</small>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <label class="form-label text-xs mb-1">Ghi chú</label>
                                            <input type="text" name="note" class="form-control" required />
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
                                        <table id="tableAllMaterials" class="table table-sm table-hover align-items-center d-none">
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
                    <div class="card-header card-header-primary pb-0 d-flex justify-content-between align-items-center" style="padding: 10px 15px;">
                        <div>
                            <h4 class="card-title mb-0" style="margin-bottom: 0; font-size: 16px;">Lịch sử nhập kho</h4>
                            <p class="card-category mb-0" style="font-size: 13px;">Các phiếu nhập gần đây</p>
                        </div>
                        <button id="btnToggleIn" class="btn btn-sm btn-success"><i class="material-icons">arrow_downward</i> Tạo phiếu nhập</button>
                    </div>
                    <div class="card-body table-responsive pt-2" style="padding: 10px;">
                        <div class="row mb-3 px-2 filter-row">
                            <div class="col-md-7">
                                <div class="input-group input-group-static">
                                    <label>Chọn Nguyên Vật Liệu</label>
                                    <select id="searchIn" class="form-control">
                                        <option value="">-- Tất cả NVL --</option>
                                        <?php 
                                        $uniqueMaterials = [];
                                        foreach (($materials ?? []) as $m) {
                                            $name = $m->material_name ?? ($m->name ?? '');
                                            if ($name && !isset($uniqueMaterials[$name])) {
                                                $uniqueMaterials[$name] = true;
                                                echo '<option value="'.strtolower(htmlspecialchars($name)).'">'.htmlspecialchars($name).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group input-group-static">
                                    <label>Lọc theo ngày</label>
                                    <input type="date" id="dateIn" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div id="containerIn" class="history-container">
                            <table id="tableStockIn" class="table table-hover table-sm" style="margin-bottom: 0;">
                                <thead class="text-primary">
                                    <tr style="height: 28px;">
                                        <th style="padding: 4px 8px;">Thời gian</th>
                                        <th style="padding: 4px 8px;">NVL</th>
                                        <th style="padding: 4px 8px;">SL</th>
                                        <th style="padding: 4px 8px;">Nhà Cung Cấp</th>
                                        <th style="padding: 4px 8px;">Tệp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($recent_stock_in ?? []) as $r): ?>
                                    <tr style="height: 24px;" data-name="<?= strtolower(htmlspecialchars($r->material_name ?? '')) ?>" data-date="<?= substr($r->created_at ?? '', 0, 10) ?>">
                                        <td style="padding: 4px 8px; white-space: nowrap;"><?= substr(htmlspecialchars($r->created_at ?? ''), 0, 16) ?></td>
                                        <td style="padding: 4px 8px; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($r->material_name ?? '') ?>"><?= htmlspecialchars(substr($r->material_name ?? ('#'.(int)($r->id_material ?? 0)), 0, 20)) ?></td>
                                        <td style="padding: 4px 8px;"><?= (int)$r->quantity ?></td>
                                        <td style="padding: 4px 8px; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($r->supplier ?? '') ?>"><?= htmlspecialchars(substr($r->supplier ?? '-', 0, 15)) ?></td>
                                        <td style="padding: 4px 8px;">
                                            <?php if (!empty($r->attachment)): ?>
                                                <a class="btn btn-xs btn-outline-primary" target="_blank" href="<?= base_url($r->attachment) ?>" style="padding: 2px 6px; font-size: 12px;">
                                                    <i class="material-icons" style="font-size:14px; vertical-align:middle;">attach_file</i> Xem
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="history-overlay"></div>
                        </div>
                        <div class="text-center mt-2">
                            <button class="btn btn-link btn-sm text-primary p-0 btn-toggle-history" data-target="containerIn">Xem thêm <i class="material-icons" style="font-size: 14px; vertical-align: middle;">expand_more</i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header card-header-info pb-0 d-flex justify-content-between align-items-center" style="padding: 10px 15px;">
                        <div>
                            <h4 class="card-title mb-0" style="margin-bottom: 0; font-size: 16px;">Lịch sử xuất kho</h4>
                            <p class="card-category mb-0" style="font-size: 13px;">Các phiếu xuất gần đây</p>
                        </div>
                        <button id="btnToggleOut" class="btn btn-sm btn-warning"><i class="material-icons">arrow_upward</i> Tạo phiếu xuất</button>
                    </div>
                    <div class="card-body table-responsive pt-2" style="padding: 10px;">
                        <div class="row mb-3 px-2 filter-row">
                            <div class="col-md-4">
                                <div class="input-group input-group-static">
                                    <label>Chọn Nguyên Vật Liệu</label>
                                    <select id="searchOut" class="form-control">
                                        <option value="">-- Tất cả NVL --</option>
                                        <?php 
                                        $uniqueMaterials = [];
                                        foreach (($materials ?? []) as $m) {
                                            $name = $m->material_name ?? ($m->name ?? '');
                                            if ($name && !isset($uniqueMaterials[$name])) {
                                                $uniqueMaterials[$name] = true;
                                                echo '<option value="'.strtolower(htmlspecialchars($name)).'">'.htmlspecialchars($name).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static">
                                    <label>Tìm kế hoạch</label>
                                    <input type="text" id="searchPlanOut" class="form-control" placeholder="Nhập tên kế hoạch...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static">
                                    <label>Lọc theo ngày</label>
                                    <input type="date" id="dateOut" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div id="containerOut" class="history-container">
                            <table id="tableStockOut" class="table table-hover table-sm" style="margin-bottom: 0;">
                                <thead class="text-info">
                                    <tr style="height: 28px;">
                                        <th style="padding: 4px 8px;">Thời gian</th>
                                        <th style="padding: 4px 8px;">NVL</th>
                                        <th style="padding: 4px 8px;">SL</th>
                                        <th style="padding: 4px 8px;">Kế hoạch</th>
                                        <th style="padding: 4px 8px;">Tệp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($recent_stock_out ?? []) as $r): ?>
                                    <tr style="height: 24px;" data-name="<?= strtolower(htmlspecialchars($r->material_name ?? '')) ?>" data-plan="<?= strtolower(htmlspecialchars($r->plan_name ?? '')) ?>" data-date="<?= substr($r->created_at ?? '', 0, 10) ?>">
                                        <td style="padding: 4px 8px; white-space: nowrap;"><?= substr(htmlspecialchars($r->created_at ?? ''), 0, 16) ?></td>
                                        <td style="padding: 4px 8px; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($r->material_name ?? '') ?>"><?= htmlspecialchars(substr($r->material_name ?? ('#'.(int)($r->id_material ?? 0)), 0, 20)) ?></td>
                                        <td style="padding: 4px 8px;"><?= (int)$r->quantity ?></td>
                                        <td style="padding: 4px 8px; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($r->plan_name ?? '') ?>"><?= htmlspecialchars(substr($r->plan_name ?? '-', 0, 15)) ?></td>
                                        <td style="padding: 4px 8px;">
                                            <?php if (!empty($r->attachment)): ?>
                                                <a class="btn btn-xs btn-outline-primary" target="_blank" href="<?= base_url($r->attachment) ?>" style="padding: 2px 6px; font-size: 12px;">
                                                    <i class="material-icons" style="font-size:14px; vertical-align:middle;">attach_file</i> Xem
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="history-overlay"></div>
                        </div>
                        <div class="text-center mt-2">
                            <button class="btn btn-link btn-sm text-info p-0 btn-toggle-history" data-target="containerOut">Xem thêm <i class="material-icons" style="font-size: 14px; vertical-align: middle;">expand_more</i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                <style>
                    /* Subtle spacing for modal forms */
                    .modal .form-row .form-group { margin-bottom: 10px; }
                </style>
                <script>
                    // Wait for DOM to be fully loaded
                    document.addEventListener('DOMContentLoaded', function(){
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
                            // Use a function to get planSelect since it might be in a modal that gets created dynamically
                            function getPlanSelect(){
                                return document.querySelector('#modalStockOut select[name="id_plan"]');
                            }
                            
                            var dateOutInput = document.getElementById('dateOutInput');
                            var shiftSelect = document.getElementById('shiftSelect');
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
                            
                            // Load shifts filtered by selected date
                            async function loadShiftsByDate(date){
                                if(!date){
                                    // Reset shifts to all available
                                    if(shiftSelect){
                                        shiftSelect.innerHTML = '<option value="">-- Chọn ca --</option>';
                                    }
                                    return;
                                }
                                try{
                                    const res = await fetch('<?= site_url('warehouse/get_shifts_by_date'); ?>?date=' + encodeURIComponent(date));
                                    const data = await res.json();
                                    if(!data || !Array.isArray(data.shifts)) throw new Error('Bad response');
                                    
                                    // Build shift options
                                    let html = '<option value="">-- Chọn ca --</option>';
                                    data.shifts.forEach(function(shift){
                                        const shiftId = parseInt(shift.id_planshift||0);
                                        const shiftName = shift.ps_name || '';
                                        const shiftDate = shift.shift_date || '';
                                        html += '<option value="' + shiftId + '" data-date="' + shiftDate + '">' + shiftName + '</option>';
                                    });
                                    
                                    if(shiftSelect){
                                        shiftSelect.innerHTML = html;
                                    }
                                }catch(e){
                                    console.error('Error loading shifts:', e);
                                    if(shiftSelect){
                                        shiftSelect.innerHTML = '<option value="">-- Chọn ca --</option>';
                                    }
                                }
                            }
                            
                            // Listen for date change and load shifts
                            if(dateOutInput){
                                dateOutInput.addEventListener('change', function(){
                                    loadShiftsByDate(this.value);
                                });
                            }
                            
                            async function loadPlanItems(planId, shiftId){
                                console.log('loadPlanItems called with planId:', planId, 'shiftId:', shiftId);
                                if(!planId || !shiftId){
                                    // If no plan or no shift, hide both tables
                                    if(tbodyPlan) tbodyPlan.innerHTML = '';
                                    if(tablePlan) tablePlan.classList.add('d-none');
                                    if(tableAll) tableAll.classList.add('d-none');
                                    return;
                                }
                                try{
                                    let url = '<?= site_url('warehouse/plan_remaining_materials'); ?>?id_plan=' + encodeURIComponent(planId);
                                    if(shiftId) {
                                        url += '&shift_id=' + encodeURIComponent(shiftId);
                                    }
                                    console.log('Fetching from URL:', url);
                                    const res = await fetch(url);
                                    console.log('Response status:', res.status);
                                    const data = await res.json();
                                    console.log('Response data:', data);
                                    if(!data || !Array.isArray(data.items)) throw new Error('Bad response - data or items array missing');
                                    console.log('Number of items:', data.items.length);
                                    // Build rows
                                    let html = '';
                                    data.items.forEach(function(item){
                                        const u = item.uom || '';
                                        const stock = parseInt(item.stock||0);
                                        const planned = parseInt(item.planned||0);
                                        const exported = parseInt(item.exported||0);
                                        const remaining = Math.max(0, parseInt(item.remaining||0));
                                        const shiftRequired = parseInt(item.shift_required||0);
                                        const canExport = item.can_export !== false;
                                        const exportRemaining = parseInt(item.export_remaining||0);
                                        const mid = parseInt(item.id_material||0);
                                        console.log('Processing item:', item.material_name, 'id:', mid, 'can_export:', canExport);
                                        if(mid <= 0) {
                                            console.log('Skipping unmapped item');
                                            return; // skip unmapped
                                        }
                                        // Default export qty suggestion
                                        let suggest = 0;
                                        let maxAllow = 0;
                                        let isDisabled = false;
                                        
                                        if (!canExport) {
                                            // Already exported enough
                                            suggest = 0;
                                            maxAllow = 0;
                                            isDisabled = true;
                                        } else if (shiftRequired > 0) {
                                            // Use shift_required as suggestion
                                            suggest = exportRemaining;
                                            maxAllow = Math.min(stock, exportRemaining);
                                        } else {
                                            // Use remaining as suggestion
                                            suggest = Math.max(0, Math.min(stock, remaining));
                                            maxAllow = Math.max(0, Math.min(stock, remaining));
                                        }
                                        
                                        let inputHTML = '<input type="number" min="0" max="'+maxAllow+'" value="'+suggest+'" class="form-control form-control-sm plan-export-input" name="items['+mid+']"';
                                        if (isDisabled) {
                                            inputHTML += ' disabled title="Đã xuất đủ yêu cầu của ca"';
                                        }
                                        inputHTML += ' />';
                                        
                                        html += '<tr>';
                                        if (isDisabled) {
                                            html += '<td style="opacity: 0.6;">' + (item.material_name||('#'+mid)) + '</td>'+
                                                '<td style="opacity: 0.6;">' + stock + ' ' + u + '</td>'+
                                                '<td style="opacity: 0.6;">' + planned + ' ' + u + '</td>'+
                                                '<td style="opacity: 0.6;">' + exported + ' ' + u + '</td>'+
                                                '<td style="opacity: 0.6;">' + remaining + ' ' + u + '</td>'+
                                                '<td style="max-width:140px; opacity: 0.6;"><span class="badge bg-secondary">Đã đủ</span></td>';
                                        } else {
                                            html += '<td>' + (item.material_name||('#'+mid)) + '</td>'+
                                                '<td>' + stock + ' ' + u + '</td>'+
                                                '<td>' + planned + ' ' + u + '</td>'+
                                                '<td>' + exported + ' ' + u + '</td>'+
                                                '<td>' + remaining + ' ' + u + '</td>'+
                                                '<td style="max-width:140px;">' + inputHTML + '</td>';
                                        }
                                        html += '</tr>';
                                    });
                                    console.log('HTML generated:', html);
                                    if(tbodyPlan) {
                                        tbodyPlan.innerHTML = html;
                                        console.log('tbody updated');
                                    }
                                    setTableMode(true);
                                    console.log('Table mode set to plan mode');
                                }catch(e){
                                    console.error('Error loading plan items:', e);
                                    console.error('Stack:', e.stack);
                                    // On error, show all as fallback
                                    if(tbodyPlan) tbodyPlan.innerHTML = '';
                                    setTableMode(false);
                                }
                            }
                            
                            // Use delegation to handle plan select change event
                            // This ensures the handler works even if the select is created dynamically
                            var planSelect = getPlanSelect();
                            if(planSelect){
                                planSelect.addEventListener('change', function(){ 
                                    loadPlanItems(this.value, shiftSelect ? shiftSelect.value : null); 
                                    if(this.value && (!shiftSelect || !shiftSelect.value)) {
                                        if(shiftSelect) shiftSelect.style.boxShadow = '0 0 10px rgba(23, 193, 232, 0.5)';
                                    }
                                });
                            } else {
                                // Fallback: Try to find and attach to any dynamically added plan select
                                document.addEventListener('change', function(e){
                                    if(e.target && e.target.matches('#modalStockOut select[name="id_plan"]')){
                                        loadPlanItems(e.target.value, shiftSelect ? shiftSelect.value : null);
                                        if(e.target.value && (!shiftSelect || !shiftSelect.value)) {
                                            if(shiftSelect) shiftSelect.style.boxShadow = '0 0 10px rgba(23, 193, 232, 0.5)';
                                        }
                                    }
                                });
                            }
                            
                            // Also reload plan items when shift is changed
                            if(shiftSelect){
                                shiftSelect.addEventListener('change', function(){
                                    this.style.boxShadow = ''; // Remove highlight
                                    
                                    // Update date_out based on selected shift's data-date
                                    var selectedOption = this.options[this.selectedIndex];
                                    if(selectedOption && selectedOption.getAttribute('data-date')) {
                                        var shiftDate = selectedOption.getAttribute('data-date');
                                        if(dateOutInput) dateOutInput.value = shiftDate;
                                    }

                                    var planSelect = getPlanSelect();
                                    if(planSelect && planSelect.value) {
                                        loadPlanItems(planSelect.value, this.value);
                                    }
                                });
                            }
                            
                            // init mode: hide both tables until plan/shift selected
                            if(tablePlan) tablePlan.classList.add('d-none');
                            if(tableAll) tableAll.classList.add('d-none');

                            // Auto-fill quantity when material is selected in stock-in modal
                            var materialSelect = document.querySelector('#modalStockIn select[name="id_material"]');
                            var quantityInput = document.querySelector('#modalStockIn input[name="quantity"]');
                            
                            console.log('Material select found:', !!materialSelect);
                            console.log('Quantity input found:', !!quantityInput);
                            
                            if (materialSelect && quantityInput) {
                                materialSelect.addEventListener('change', function() {
                                    var materialId = this.value;
                                    console.log('Material selected:', materialId);
                                    if (!materialId || materialId === '') {
                                        quantityInput.value = '';
                                        return;
                                    }

                                    // Fetch material info and auto-fill quantity
                                    var url = '<?= site_url('warehouse/get_material_info'); ?>?id_material=' + encodeURIComponent(materialId);
                                    console.log('Fetching from:', url);
                                    
                                    fetch(url)
                                        .then(response => {
                                            console.log('Response status:', response.status);
                                            if (!response.ok) {
                                                throw new Error('HTTP ' + response.status);
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            console.log('Response data:', data);
                                            if (data && data.qty_to_import !== undefined) {
                                                console.log('Setting quantity to:', data.qty_to_import);
                                                quantityInput.value = data.qty_to_import;
                                            } else {
                                                console.log('No qty_to_import in response');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error fetching material info:', error);
                                        });
                                });
                            }
                            
                            // Validate stock-out form: check if all items have enough stock
                            var stockOutForm = document.querySelector('#modalStockOut form');
                            if (stockOutForm) {
                                stockOutForm.addEventListener('submit', function(e) {
                                    var disabledInputs = this.querySelectorAll('input[name^="items["][disabled]');
                                    var enabledInputs = this.querySelectorAll('input[name^="items["]:not([disabled])');
                                    var hasValidExports = enabledInputs.length > 0;
                                    
                                    if (!hasValidExports && disabledInputs.length > 0) {
                                        e.preventDefault();
                                        alert('⚠️ Không có nguyên liệu nào đủ để xuất cho ca này.\n\nCác nguyên liệu đã được xuất đủ yêu cầu của ca.');
                                        return false;
                                    }
                                    
                                    // Check if any enabled input has quantity > 0
                                    var hasQuantity = false;
                                    enabledInputs.forEach(function(inp) {
                                        if (parseInt(inp.value || 0) > 0) {
                                            hasQuantity = true;
                                        }
                                    });
                                    
                                    if (!hasQuantity) {
                                        e.preventDefault();
                                        alert('⚠️ Vui lòng nhập số lượng xuất cho ít nhất một nguyên liệu.');
                                        return false;
                                    }
                                });
                            }

                            // Setup history table filters
                            function setupTableFilter(tableId, searchId, dateId, planSearchId) {
                                var table = document.getElementById(tableId);
                                var searchInput = document.getElementById(searchId);
                                var dateInput = document.getElementById(dateId);
                                var planSearchInput = planSearchId ? document.getElementById(planSearchId) : null;
                                
                                if (!table || !searchInput || !dateInput) return;

                                function filter() {
                                    var searchText = searchInput.value.toLowerCase();
                                    var filterDate = dateInput.value;
                                    var planText = planSearchInput ? planSearchInput.value.toLowerCase() : '';
                                    var rows = table.querySelectorAll('tbody tr');

                                    rows.forEach(function(row) {
                                        var name = row.getAttribute('data-name') || '';
                                        var date = row.getAttribute('data-date') || '';
                                        var plan = row.getAttribute('data-plan') || '';
                                        var show = true;

                                        if (searchText && name.indexOf(searchText) === -1) {
                                            show = false;
                                        }
                                        if (filterDate && date !== filterDate) {
                                            show = false;
                                        }
                                        if (planText && plan.indexOf(planText) === -1) {
                                            show = false;
                                        }

                                        row.style.display = show ? '' : 'none';
                                    });
                                }

                                searchInput.addEventListener('change', filter);
                                searchInput.addEventListener('input', filter);
                                dateInput.addEventListener('change', filter);
                                if (planSearchInput) {
                                    planSearchInput.addEventListener('input', filter);
                                }
                            }

                            setupTableFilter('tableStockIn', 'searchIn', 'dateIn');
                            setupTableFilter('tableStockOut', 'searchOut', 'dateOut', 'searchPlanOut');

                            // Toggle history expansion
                            document.querySelectorAll('.btn-toggle-history').forEach(function(btn) {
                                btn.addEventListener('click', function() {
                                    var targetId = this.getAttribute('data-target');
                                    var container = document.getElementById(targetId);
                                    if (!container) return;

                                    if (container.classList.contains('expanded')) {
                                        container.classList.remove('expanded');
                                        this.innerHTML = 'Xem thêm <i class="material-icons" style="font-size: 14px; vertical-align: middle;">expand_more</i>';
                                    } else {
                                        container.classList.add('expanded');
                                        this.innerHTML = 'Thu gọn <i class="material-icons" style="font-size: 14px; vertical-align: middle;">expand_less</i>';
                                    }
                                });
                            });
                        })();
                    });
                </script>
    </div>
</div>
