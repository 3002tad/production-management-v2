<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC2: Product Management - ADD FORM with BOM BUILDER                         ║
║  Material Design 3.0 với dynamic BOM rows                                   ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <!-- Card Header -->
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-6 d-flex align-items-center">
                            <i class="material-icons-round text-white opacity-10 me-2" style="font-size: 24px;">add_box</i>
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Thêm Sản phẩm mới</h6>
                        </div>
                        <div class="col-6 text-end">
                            <a href="<?= site_url('BOD/product'); ?>" 
                               class="btn bg-gradient-light mb-0"
                               style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">arrow_back</i>
                                Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body px-4 pb-4">
                <form action="<?= site_url('BOD/storeProduct'); ?>" method="POST" id="addProductForm">
                    <!-- Product Information Section -->
                    <h6 class="mt-4" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">info</i>
                        Thông tin Sản phẩm
                    </h6>
                    <hr class="horizontal dark mt-2 mb-3">

                    <div class="row">
                        <!-- Tên sản phẩm (Required, max 50) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                Tên sản phẩm <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-outline is-focused">
                                <input type="text" 
                                       name="product_name" 
                                       class="form-control" 
                                       required
                                       maxlength="50"
                                       placeholder="VD: Bút bi BP-01, Bút chì BC-05"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Tối đa 50 ký tự
                            </small>
                        </div>

                        <!-- Đường kính (Required) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                Đường kính (mm) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-outline is-focused">
                                <input type="number" 
                                       name="diameter" 
                                       class="form-control" 
                                       list="diameterList"
                                       required
                                       placeholder="Chọn hoặc nhập (VD: 0.5, 0.7, 1.0)"
                                       step="0.01"
                                       min="0.01"
                                       max="10"
                                       style="font-family: 'Poppins', sans-serif;">
                                <datalist id="diameterList">
                                    <?php foreach ($diameters as $value => $label): ?>
                                        <option value="<?= $value; ?>"><?= $label; ?></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Chọn từ danh sách hoặc nhập giá trị tùy chỉnh (0.01 - 10mm)
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ứng dụng/Màu (max 100) -->
                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Ứng dụng (Màu mực) <span class="text-danger">*</span>
                                </label>
<div class="input-group input-group-outline is-focused">
                                <input type="text" 
                                       name="application" 
                                       class="form-control"
list="applicationList"
                                       required
                                       maxlength="100"
placeholder="Chọn hoặc nhập (VD: đỏ, xanh, nhiều màu)"
                                       style="font-family: 'Poppins', sans-serif;">
<datalist id="applicationList">
                                    <option value="xanh">Xanh</option>
                                    <option value="trắng">Trắng</option>
                                    <option value="tím">Tím</option>
                                    <option value="nhiều màu">Nhiều màu</option>
                                    <option value="xanh lá">Xanh lá</option>
                                    <option value="bạc">Bạc</option>
                                </datalist>
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">VD: Mực xanh, Mực đen, Mực đỏ</small>
                        </div>

                        <!-- Tóm tắt -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Tóm tắt
                                </label>
                                <input type="text" 
                                       name="summary" 
                                       class="form-control"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">VD: Sản phẩm cao cấp, Chất lượng tốt</small>
                        </div>
                    </div>

                    <!-- BOM Builder Section -->
                    <h6 class="mt-4" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">precision_manufacturing</i>
                        Bill of Materials (BOM) - Danh sách Nguyên liệu <span class="text-danger">*</span>
                    </h6>
                    <hr class="horizontal dark mt-2 mb-3">

                    <div class="alert alert-warning" role="alert" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">warning</i>
                        <strong>Bắt buộc:</strong> Chọn ít nhất 1 nguyên liệu và nhập định mức > 0. Bạn có thể bỏ chọn NVL nào không dùng.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-items-center mb-0" id="bomTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">Dùng</th>
                                    <th class="text-left" style="width: 40%;">Nguyên liệu</th>
                                    <th class="text-left" style="width: 20%;">Tồn kho hiện tại</th>
                                    <th class="text-left" style="width: 20%;">Số lượng/SP <span class="text-danger">*</span></th>
                                    <th class="text-left" style="width: 15%;">ĐVT</th>
                                </tr>
                            </thead>
                            <tbody id="bomContainer">
                        <?php foreach ($materials as $material): ?>
                                <tr class="bom-row" data-material-id="<?= $material->id_material; ?>">
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input bom-checkbox" 
                                                   type="checkbox" 
                                                   name="bom_enabled[]" 
                                                   value="<?= $material->id_material; ?>"
                                                   id="bom_check_<?= $material->id_material; ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-bold"><?= htmlspecialchars($material->material_name); ?></span>
                                        <input type="hidden" name="bom_materials[]" value="<?= $material->id_material; ?>">
                                        <input type="hidden" name="bom_material_names[]" value="<?= htmlspecialchars($material->material_name); ?>">
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-info"><?= number_format($material->stock ?? 0, 2); ?> <?= htmlspecialchars($material->uom); ?></span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-outline" style="max-width: 150px;">
                                            <input type="number" 
                                                   name="bom_quantities[]" 
                                                   class="form-control form-control-sm bom-quantity" 
                                                   step="0.01"
                                                   min="0"
                                                   value="0"
                                                   placeholder="0.00"
                                                   disabled
                                                   style="font-family: 'Poppins', sans-serif;">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="bom_units[]" value="<?= htmlspecialchars($material->uom); ?>">
                                        <span class="text-sm"><?= htmlspecialchars($material->uom); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" 
                            id="addCustomMaterial" 
                            class="btn btn-outline-primary btn-sm mt-2"
                            style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">add_circle_outline</i>
                        Thêm NVL mới (không có trong danh sách)
                    </button>

                    <!-- Action Buttons -->
                    <div class="row mt-5">
                        <div class="col-12 text-end">
                            <a href="<?= site_url('BOD/product'); ?>" 
                               class="btn btn-outline-secondary mb-0 me-2"
                               style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">close</i>
                                Hủy bỏ
                            </a>
                            <button type="submit" 
                                    class="btn bg-gradient-success mb-0"
                                    style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">save</i>
                                Lưu sản phẩm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- BOM Row Template (Hidden) -->
<template id="bomRowTemplate">
    <div class="row bom-row mb-3">
        <div class="col-md-5">
            <label class="form-label" style="font-family: 'Poppins', sans-serif; margin-bottom: 5px;">Nguyên liệu</label>
            <div class="input-group input-group-outline is-focused">
                <input type="text" 
                       name="bom_material_names[]" 
                       class="form-control material-input" 
                       list="materialList"
                       placeholder="Chọn từ danh sách hoặc nhập mới"
                       style="font-family: 'Poppins', sans-serif;">
                <datalist id="materialList">
                    <?php foreach ($materials as $material): ?>
                        <option 
                            value="<?= htmlspecialchars($material->material_name); ?>" 
                            data-id="<?= $material->id_material; ?>"
                            data-unit="<?= htmlspecialchars($material->uom); ?>">
                            <?= htmlspecialchars($material->material_display); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
                <input type="hidden" name="bom_materials[]" class="material-id-input">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-family: 'Poppins', sans-serif; margin-bottom: 5px;">Định mức/SP</label>
            <div class="input-group input-group-outline">
                <input type="number" 
                       name="bom_quantities[]" 
                       class="form-control" 
                       step="0.01"
                       min="0.01"
                       placeholder="VD: 10, 25.5"
                       style="font-family: 'Poppins', sans-serif;">
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-family: 'Poppins', sans-serif; margin-bottom: 5px;">Đơn vị</label>
            <div class="input-group input-group-outline">
                <input type="text" 
                       name="bom_units[]" 
                       class="form-control unit-input"
                       list="unitList"
                       placeholder="Chọn: g, kg, pcs"
                       style="font-family: 'Poppins', sans-serif;">
                <datalist id="unitList">
                    <option value="g">Gram</option>
                    <option value="kg">Kilogram</option>
                    <option value="pcs">Chiếc</option>
                    <option value="l">Lít</option>
                    <option value="ml">Mililit</option>
                </datalist>
            </div>
        </div>
        <div class="col-md-2">
            <button type="button" 
                    class="btn btn-danger btn-sm remove-bom-row w-100"
                    style="font-family: 'Poppins', sans-serif; margin-top: 23px;">
                <i class="material-icons-round" style="font-size: 16px;">delete</i>
                Xóa
            </button>
        </div>
    </div>
</template>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addProductForm');
const bomTable = document.getElementById('bomTable');
    const bomContainer = document.getElementById('bomContainer');
    const addCustomMaterialBtn = document.getElementById('addCustomMaterial');

    // Checkbox handling - Enable/disable quantity input
    const bomCheckboxes = document.querySelectorAll('.bom-checkbox');
    const bomQuantities = document.querySelectorAll('.bom-quantity');
    
    bomCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('.bom-row');
            const quantityInput = row.querySelector('.bom-quantity');
            
            if (this.checked) {
                quantityInput.disabled = false;
                quantityInput.value = '';
                quantityInput.focus();
                row.classList.add('table-active');
                } else {
                    quantityInput.disabled = true;
                quantityInput.value = '0';
                row.classList.remove('table-active');
                }
            });
        });
    
    // Add custom material (not in list)
    let customMaterialCounter = 0;
    addCustomMaterialBtn.addEventListener('click', function() {
        customMaterialCounter++;
        const newRow = document.createElement('tr');
        newRow.className = 'bom-row custom-material table-warning';
        newRow.innerHTML = `
            <td class="text-center">
                <div class="form-check">
                    <input class="form-check-input bom-checkbox" 
                           type="checkbox" 
                           name="bom_enabled[]" 
                           value="custom_${customMaterialCounter}"
                           checked>
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline is-focused" style="max-width: 300px;">
                    <input type="text" 
                           name="bom_material_names[]" 
                           class="form-control form-control-sm" 
                           placeholder="Nhập tên NVL mới"
                           required
                           style="font-family: 'Poppins', sans-serif;">
                </div>
                <input type="hidden" name="bom_materials[]" value="">
            </td>
            <td>
                <span class="badge badge-sm bg-gradient-secondary">Chưa có</span>
            </td>
            <td>
                <div class="input-group input-group-outline is-focused" style="max-width: 150px;">
                    <input type="number" 
                           name="bom_quantities[]" 
                           class="form-control form-control-sm bom-quantity" 
                           step="0.01"
                           min="0.01"
                           placeholder="0.00"
                           required
                           style="font-family: 'Poppins', sans-serif;">
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline is-focused" style="max-width: 100px;">
                    <input type="text" 
                           name="bom_units[]" 
                           class="form-control form-control-sm"
                           list="unitList"
                           placeholder="g, kg, pcs"
                           required
                           style="font-family: 'Poppins', sans-serif;">
                </div>
            </td>
        </tr>
        `;
        
        bomContainer.appendChild(newRow);
        
        // Focus on material name input
        newRow.querySelector('input[name="bom_material_names[]"]').focus();
        
        // Setup checkbox handler for new row
        const checkbox = newRow.querySelector('.bom-checkbox');
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                if (confirm('Xóa NVL tùy chỉnh này?')) {
                    newRow.remove();
            } else {
                this.checked = true;
    }
    }
        });
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const productName = document.querySelector('input[name="product_name"]').value.trim();
        const diameter = document.querySelector('input[name="diameter"]').value.trim();

        if (!productName) {
            e.preventDefault();
            alert('Vui lòng nhập tên sản phẩm!');
            return false;
        }

        if (!diameter) {
            e.preventDefault();
            alert('Vui lòng chọn hoặc nhập đường kính!');
            return false;
        }

        // Validate BOM - Bắt buộc ít nhất 1 NVL
        const checkedBoxes = document.querySelectorAll('.bom-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('⚠️ Bắt buộc chọn ít nhất 1 nguyên liệu cho sản phẩm!');
            return false;
        }
        
        // Validate each checked material
        let hasError = false;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.bom-row');
        const quantityInput = row.querySelector('.bom-quantity');
        const quantity = parseFloat(quantityInput.value);
            
            if (!quantity || quantity <= 0) {
                e.preventDefault();
                alert('Vui lòng nhập số lượng > 0 cho tất cả NVL đã chọn!');
                quantityInput.focus();
                hasError = true;
                return false;
            }
            
            // Validate custom materials
            if (row.classList.contains('custom-material')) {
                const materialNameInput = row.querySelector('input[name="bom_material_names[]"]');
                const unitInput = row.querySelector('input[name="bom_units[]"]');
                
                if (!materialNameInput.value.trim()) {
                    e.preventDefault();
                    alert('Vui lòng nhập tên nguyên liệu!');
                    materialNameInput.focus();
hasError = true;
                    return false;
                }

                if (!unitInput.value.trim()) {
                    e.preventDefault();
                    alert('Vui lòng nhập đơn vị!');
                    unitInput.focus();
hasError = true;
                    return false;
                }
            }
        });
        
        if (hasError) {
            return false;
        }
        
        // Remove unchecked materials from submission (avoid using :has for wider browser support)
document.querySelectorAll('.bom-row').forEach(row => {
            const checkbox = row.querySelector('.bom-checkbox');
            if (checkbox && !checkbox.checked) {
            row.querySelectorAll('input').forEach(input => input.disabled = true);
            }
        });

        return true;
    });
});
</script>
