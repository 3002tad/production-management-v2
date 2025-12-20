<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Quản lý nguyên vật liệu</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Quản lý nguyên vật liệu</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
            </div>
        </div>
    </div>
</nav>

<br/>

<!-- Card tiêu đề: Nguyên liệu đã sử dụng -->
<div class="container-fluid pt-0 px-4">
    <div class="card-header p-0 mt-n4 mx-2 z-index-2">
        <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
            <div class="col-8 d-flex align-items-center">
                <i class="material-icons pr-3">view_in_ar</i>
                <h6 class="mb-0 pr-4"><?= lang('label_material_used'); ?></h6>
            </div>
            <div class="col-4 text-end">
                <span class="badge bg-info text-white">Chế độ Xem</span>
            </div>
        </div>
    </div>
</div>

<!-- Hai card dưới -->
<div class="container-fluid py-4 pt-2 px-4">
    <div class="row g-4 mx-1">
        <!-- Cảnh báo Tồn kho thấp (40%) -->
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header card-header-danger py-3 px-4">
                    <h6 class="mb-0">⚠️ Cảnh báo Tồn kho</h6>
                    <span class="text-sm mb-0">Nguyên liệu dưới mức tối thiểu</span>
                </div>
                <div class="card-body pt-3 px-4 pb-3">
                    <div class="table-responsive p-0">
                        <table id="table-input" class="table align-items-center justify-content-center mb-0 table-center">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_material'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tồn hiện tại</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tối thiểu</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Thiếu</th>
                                </tr>
                            </thead>
                            <tbody class="pl-3">
                                <?php if (!empty($material_input)) : $i = 1; foreach ($material_input as $value) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex pl-3">
                                            <div class="my-auto">
                                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pl-4">
                                        <span class="text-sm font-weight-bold"><?= $value->material_name ?></span>
                                    </td>
                                    <td class="pl-4">
                                        <?php 
                                            $uh = isset($value->uom) ? trim(strtolower($value->uom)) : 'g';
                                            switch ($uh) {
                                                case 'kg': $uh_label = lang('unit_kilogram'); break;
                                                case 'g': $uh_label = lang('unit_gram'); break;
                                                case 'pcs': $uh_label = lang('unit_pieces'); break;
                                                case 'm': $uh_label = lang('unit_meter'); break;
                                                case 'cm': $uh_label = lang('unit_centimeter'); break;
                                                case 'mm': $uh_label = lang('unit_mm'); break;
                                                case 'ml': $uh_label = lang('unit_milliliter'); break;
                                                case 'l': $uh_label = lang('unit_liter'); break;
                                                case 'box': $uh_label = lang('unit_box'); break;
                                                default: $uh_label = $uh; break;
                                            }
                                        ?>
                                        <span class="text-sm font-weight-bold text-danger"><?= $value->stock ?> <?= $uh_label; ?></span>
                                    </td>
                                    <td class="pl-4">
                                        <span class="text-sm font-weight-bold"><?= $value->min_stock ?> <?= $uh_label; ?></span>
                                    </td>
                                    <td class="pl-4">
                                        <span class="text-sm font-weight-bold badge badge-danger"><?= $value->thiếu_bao_nhiêu ?> <?= $uh_label; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <span class="text-sm text-success">✓ Tất cả nguyên liệu đều đủ tồn kho</span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trạng thái nguyên liệu (60%) - READ ONLY MODE -->
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header card-header-success py-3 px-4">
                    <div class="row">
                        <div class="col-7 align-items-center">
                            <h6 class="mb-0"><?= lang('label_material_status'); ?></h6>
                            <span class="text-sm mb-0"><?= lang('label_material_for_production'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3 px-4 pb-3">
                    <div class="table-responsive p-0">
                        <table id="table-data" class="table align-items-center justify-content-center mb-0 table-center">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0"><?= lang('table_no'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_code'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_material'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_stock'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tồn kho tối thiểu</th>
                                </tr>
                            </thead>
                            <tbody class="pl-3">
                                <?php if (!empty($materials)) : $i = 1; foreach ($materials as $value) : ?>
                                <tr>
                                    <td><h6 class="mb-0 text-sm"><?= $i++; ?></h6></td>
                                    <td class="pl-4"><span class="text-sm font-weight-bold"><?= $value->id_material ?></span></td>
                                    <td class="pl-4"><span class="text-sm font-weight-bold"><?= $value->material_name ?></span></td>
                                    <td class="pl-4">
                                        <?php 
                                            $u = isset($value->uom) ? trim(strtolower($value->uom)) : 'g';
                                            switch ($u) {
                                                case 'kg': $u_label = lang('unit_kilogram'); break;
                                                case 'g': $u_label = lang('unit_gram'); break;
                                                case 'pcs': $u_label = lang('unit_pieces'); break;
                                                case 'm': $u_label = lang('unit_meter'); break;
                                                case 'cm': $u_label = lang('unit_centimeter'); break;
                                                case 'mm': $u_label = lang('unit_mm'); break;
                                                case 'ml': $u_label = lang('unit_milliliter'); break;
                                                case 'l': $u_label = lang('unit_liter'); break;
                                                case 'box': $u_label = lang('unit_box'); break;
                                                default: $u_label = $u; break;
                                            }
                                        ?>
                                        <span class="text-sm font-weight-bold"><?= $value->stock ?> <?= $u_label; ?></span>
                                    </td>
                                    <td class="pl-4">
                                        <?php 
                                            $u2 = isset($value->uom) ? trim(strtolower($value->uom)) : 'g';
                                            switch ($u2) {
                                                case 'kg': $u2_label = lang('unit_kilogram'); break;
                                                case 'g': $u2_label = lang('unit_gram'); break;
                                                case 'pcs': $u2_label = lang('unit_pieces'); break;
                                                case 'm': $u2_label = lang('unit_meter'); break;
                                                case 'cm': $u2_label = lang('unit_centimeter'); break;
                                                case 'mm': $u2_label = lang('unit_mm'); break;
                                                case 'ml': $u2_label = lang('unit_milliliter'); break;
                                                case 'l': $u2_label = lang('unit_liter'); break;
                                                case 'box': $u2_label = lang('unit_box'); break;
                                                default: $u2_label = $u2; break;
                                            }
                                        ?>
                                        <span class="text-sm font-weight-bold">
                                            <?= isset($value->min_stock) ? $value->min_stock : 0 ?> <?= $u2_label; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <span class="text-sm text-muted">Không có dữ liệu</span>
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
</div>