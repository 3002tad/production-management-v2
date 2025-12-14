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
                <button type="button" id="btn-open-add" class="btn badge-sm bg-gradient-secondary mb-0">
                    <?= lang('btn_add_material'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hai card dưới -->
<div class="container-fluid py-4 pt-2 px-4">
    <div class="row g-4 mx-1">
        <!-- Lịch sử nguyên liệu (40%) -->
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header card-header-rose py-3 px-4">
                    <h6 class="mb-0"><?= lang('label_material_history'); ?></h6>
                    <span class="text-sm mb-0"><?= lang('label_material_history_desc'); ?></span>
                </div>
                <div class="card-body pt-3 px-4 pb-3">
                    <div class="table-responsive p-0">
                        <table id="table-data" class="table align-items-center justify-content-center mb-0 table-center">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_material'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_out'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_production'); ?></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_date'); ?></th>
                                </tr>
                            </thead>
                            <tbody class="pl-3">
                                <?php if (!empty($material)) : $i = 1; foreach ($material as $value) : ?>
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
                                        <span class="text-sm font-weight-bold"><?= $value->used_stock ?> <?= $uh_label; ?></span>
                                    </td>
                                    <td class="pl-4">
                                        <span class="text-sm font-weight-bold"><?= $value->staff_name ?></span>
                                    </td>
                                    <td class="pl-4">
                                        <span class="text-sm font-weight-bold"><?= $value->start_date ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trạng thái nguyên liệu (60%) -->
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
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_action'); ?></th>
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
                                    <td class="pl-4">
                                        <button type="button"
                                            class="btn btn-info btn-link btn-sm btn-edit-material"
                                            data-id="<?= $value->id_material ?>"
                                            data-name="<?= htmlspecialchars($value->material_name, ENT_QUOTES, 'UTF-8') ?>"
                                            data-type="<?= htmlspecialchars(isset($value->material_type) ? $value->material_type : '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-stock="<?= (int)($value->stock ?? 0) ?>"
                                            data-min="<?= (int)($value->min_stock ?? 0) ?>"
                                            data-uom="<?= htmlspecialchars(isset($value->uom) ? $value->uom : 'g', ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                            <i class="material-icons">edit</i>
                                        </button>
                                        <a href="<?= site_url('warehouse/deleteMaterialMaster/'.$value->id_material); ?>"
                                           onclick="return confirm('<?= lang('msg_confirm_delete_data');?>');"
                                           class="btn btn-danger btn-link btn-sm">
                                            <i class="material-icons">close</i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Thêm nguyên liệu -->
<div class="modal fade" id="modalAddMaterial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0"><?= lang('btn_add_material'); ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('warehouse/addNewMaterial'); ?>" method="post">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-xs mb-1">Mã NVL (tùy chọn)</label>
                            <input type="text" name="id_material" class="form-control" placeholder="Để trống để tự tạo">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-xs mb-1">Tên nguyên liệu<span class="text-danger">*</span></label>
                            <input type="text" name="material_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs mb-1">Loại nguyên liệu</label>
                            <input type="text" name="material_type" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs mb-1">Đơn vị tính<span class="text-danger">*</span></label>
                            <select class="form-select" name="uom" required>
                                <option value="g">Gram (g)</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="m">Meter (m)</option>
                                <option value="cm">Centimeter (cm)</option>
                                <option value="mm">Millimeter (mm)</option>
                                <option value="ml">Milliliter (ml)</option>
                                <option value="l">Liter (l)</option>
                                <option value="box">Box</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs mb-1">Tồn ban đầu</label>
                            <input type="number" name="stock" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs mb-1">Tồn tối thiểu</label>
                            <input type="number" name="min_stock" class="form-control" value="0" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn bg-gradient-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Sửa nguyên liệu -->
<div class="modal fade" id="modalEditMaterial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0">Sửa nguyên liệu</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('warehouse/updateMaterial'); ?>" method="post">
                <input type="hidden" name="old_id_material" id="edit-old-id">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-xs mb-1">Mã NVL</label>
                            <input type="text" name="id_material" id="edit-id" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-xs mb-1">Tên nguyên liệu<span class="text-danger">*</span></label>
                            <input type="text" name="material_name" id="edit-name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs mb-1">Loại nguyên liệu</label>
                            <input type="text" name="material_type" id="edit-type" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs mb-1">Đơn vị tính<span class="text-danger">*</span></label>
                            <select class="form-select" name="uom" id="edit-uom" required>
                                <option value="g">Gram (g)</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="m">Meter (m)</option>
                                <option value="cm">Centimeter (cm)</option>
                                <option value="mm">Millimeter (mm)</option>
                                <option value="ml">Milliliter (ml)</option>
                                <option value="l">Liter (l)</option>
                                <option value="box">Box</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs mb-1">Tồn</label>
                            <input type="number" name="stock" id="edit-stock" class="form-control" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs mb-1">Tồn tối thiểu</label>
                            <input type="number" name="min_stock" id="edit-min" class="form-control" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn bg-gradient-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function(){
        var addBtn = document.getElementById('btn-open-add');
        if (addBtn) {
            addBtn.addEventListener('click', function(){
                if (window.bootstrap && bootstrap.Modal) {
                    var modal = new bootstrap.Modal(document.getElementById('modalAddMaterial'));
                    modal.show();
                } else {
                    document.getElementById('modalAddMaterial').classList.add('show');
                    document.getElementById('modalAddMaterial').style.display = 'block';
                }
            });
        }

        function setSelectValue(sel, val){
            if(!sel) return;
            val = (val || 'g').trim().toLowerCase();
            sel.value = val;
        }
        var editButtons = document.querySelectorAll('.btn-edit-material');
        editButtons.forEach(function(btn){
            btn.addEventListener('click', function(){
                var id = this.getAttribute('data-id');
                var name = this.getAttribute('data-name') || '';
                var type = this.getAttribute('data-type') || '';
                var stock = this.getAttribute('data-stock') || '0';
                var min = this.getAttribute('data-min') || '0';
                var uom = this.getAttribute('data-uom') || 'g';

                document.getElementById('edit-old-id').value = id;
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-type').value = type;
                document.getElementById('edit-stock').value = stock;
                document.getElementById('edit-min').value = min;
                setSelectValue(document.getElementById('edit-uom'), uom);

                if (window.bootstrap && bootstrap.Modal) {
                    var modal = new bootstrap.Modal(document.getElementById('modalEditMaterial'));
                    modal.show();
                } else {
                    document.getElementById('modalEditMaterial').classList.add('show');
                    document.getElementById('modalEditMaterial').style.display = 'block';
                }
            });
        });
    })();
</script>
