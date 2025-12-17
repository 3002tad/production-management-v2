<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC2: Product Management - EDIT FORM with BOM EDITOR                         ║
║  Material Design 3.0 với pre-filled BOM data                                ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <!-- Card Header -->
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-6 d-flex align-items-center">
                            <i class="material-icons-round text-white opacity-10 me-2" style="font-size: 24px;">edit</i>
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Chỉnh sửa Sản phẩm & BOM</h6>
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
                <!-- Product Info Card -->
                <div class="alert alert-secondary" role="alert" style="font-family: 'Poppins', sans-serif;">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">badge</i> Mã SP:</strong> 
                            <?= $product->id_product; ?>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">schedule</i> Tạo lúc:</strong> 
                            <?= date('d/m/Y H:i', strtotime($product->created_at)); ?>
                        </div>
                    </div>
                </div>

                <form action="<?= site_url('BOD/updateProduct'); ?>" method="POST" id="editProductForm">
                    <input type="hidden" name="id_product" value="<?= $product->id_product; ?>">
                    
                    <!-- Product Information Section -->
                    <h6 class="mt-4" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">info</i>
                        Thông tin Sản phẩm
                    </h6>
                    <hr class="horizontal dark mt-2 mb-3">

                    <div class="row">
                        <!-- Tên sản phẩm -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                Tên sản phẩm <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-outline is-filled">
                                <input type="text" 
                                       name="product_name" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($product->product_name); ?>"
                                       required
                                       maxlength="50"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                        </div>

                        <!-- Đường kính -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                Đường kính (mm) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-outline is-filled">
                                <input type="number" 
                                       name="diameter" 
                                       class="form-control" 
                                       list="diameterList"
                                       value="<?= htmlspecialchars($product->diameter); ?>"
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
                        <!-- Ứng dụng -->
                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Ứng dụng (Màu mực) <span class="text-danger">*</span>
                                </label>
<div class="input-group input-group-outline is-filled">
                                <input type="text" 
                                       name="application" 
                                       class="form-control"
list="applicationList"
                                       value="<?= htmlspecialchars($product->application ?? ''); ?>"
                                       required
                                       maxlength="100"
placeholder="Chọn hoặc nhập (VD: xanh, đen, đỏ)"
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
                        </div>

                        <!-- Tóm tắt -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline <?= !empty($product->summary) ? 'is-filled' : ''; ?>">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Tóm tắt
                                </label>
                                <input type="text" 
                                       name="summary" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($product->summary ?? ''); ?>"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <h6 style="font-family: 'Poppins', sans-serif;">
                                        <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">toggle_on</i>
                                        Trạng thái
                                    </h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="is_active" 
                                               id="isActiveSwitch"
                                               value="1"
                                               <?= ($product->is_active == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="isActiveSwitch" style="font-family: 'Poppins', sans-serif;">
                                            <span id="statusText">
                                                <?= ($product->is_active == 1) ? 'Hoạt động' : 'Ngừng hoạt động'; ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOM Editor Section -->
                    <h6 class="mt-4" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">precision_manufacturing</i>
                        Bill of Materials (BOM) - Danh sách Nguyên liệu <span class="text-danger">*</span>
                    </h6>
                    <hr class="horizontal dark mt-2 mb-3">

                    <div class="alert alert-warning" role="alert" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">warning</i>
                        <strong>Bắt buộc:</strong> Chọn ít nhất 1 nguyên liệu và nhập định mức > 0
                    </div>

                    <?php
                    // Build array of existing BOM materials for easier lookup
                    $existingBOM = [];
                    $customBOM = [];
                    if (!empty($product->bom_items)) {
foreach ($product->bom_items as $mat) {
                            if (!empty($mat->id_material)) {
                                $existingBOM[$mat->id_material] = $mat; // Truy cập thuộc tính đối tượng
                            } else {
                                // Custom NVL (không có trong danh sách material)
                                $customBOM[] = $mat;
                            }
                        }
                    }
?>

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
                                                    <?php 
                                    $isUsed = isset($existingBOM[$material->id_material]);
                                    $quantity = $isUsed ? ($existingBOM[$material->id_material]->quantity_per_unit ?? $existingBOM[$material->id_material]->quantity ?? 0) : 0;
                                    ?>
                                <tr class="bom-row <?= $isUsed ? 'table-active' : ''; ?>" data-material-id="<?= $material->id_material; ?>">
                                    <td class="text-center">
                                                <div class="form-check">
                                            <input class="form-check-input bom-checkbox" 
type="checkbox" 
                                                   name="bom_enabled[]" 
                                                   value="<?= $material->id_material; ?>"
                                                   id="bom_check_<?= $material->id_material; ?>"
                                                   <?= $isUsed ? 'checked' : ''; ?>>
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
                                        <div class="input-group input-group-outline <?= $isUsed ? 'is-filled' : ''; ?>" style="max-width: 150px;">
                                            <input type="number" 
                                                   name="bom_quantities[]" 
                                                   class="form-control form-control-sm bom-quantity"
                                                   step="0.01"
                                                   min="0"
                                                   value="<?= $quantity; ?>"
                                                   placeholder="0.00"
                                                   <?= $isUsed ? '' : 'disabled'; ?>
                                                   style="font-family: 'Poppins', sans-serif;">
                                        </div>
</td>
                                    <td>
                                        <input type="hidden" name="bom_uoms[]" value="<?= htmlspecialchars($material->uom); ?>">
                                        <span class="text-sm"><?= htmlspecialchars($material->uom); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (!empty($customBOM)): ?>
                                    <?php foreach ($customBOM as $c): ?>
                                    <tr class="bom-row table-warning" data-material-id="custom">
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input bom-checkbox" 
                                                       type="checkbox" 
                                                       name="bom_enabled[]" 
                                                       value="custom" 
                                                       checked>
                                    </div>
                                    </td>
                                        <td>
                                        <div class="input-group input-group-outline is-filled" style="max-width: 300px;">
                                            <input type="text" 
                                                   name="bom_material_names[]" 
                                                   class="form-control form-control-sm" 
                                                       placeholder="Nhập tên NVL mới"
                                                       required
                                                       value="<?= htmlspecialchars($c->material_name ?? ''); ?>"
                                                   style="font-family: 'Poppins', sans-serif;">
                                                                                    </div>
<input type="hidden" name="bom_materials[]" value="">
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-secondary"><?= isset($c->stock) ? number_format($c->stock, 2) : 'Chưa có'; ?></span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-outline is-filled" style="max-width: 150px;">
                                                <input type="number" 
                                                       name="bom_quantities[]" 
                                                       class="form-control form-control-sm bom-quantity" 
                                                       step="0.01"
                                                       min="0.01"
                                                       placeholder="0.00"
                                                       required
                                                       value="<?= htmlspecialchars($c->quantity_per_unit ?? ''); ?>"
                                                       style="font-family: 'Poppins', sans-serif;">
                                    </div>
                                    </td>
                                        <td>
                                            <div class="d-flex align-items-center" style="max-width: 160px; gap:8px;">
                                                <div class="input-group input-group-outline is-filled" style="flex:1;">
                                                    <input type="text" 
                                                           name="bom_uoms[]" 
                                                           class="form-control form-control-sm uom-input"
                                                           list="unitList"
                                                           placeholder="g, kg, pcs"
                                                           required
                                                           value="<?= htmlspecialchars($c->uom ?? ''); ?>"
                                                           style="font-family: 'Poppins', sans-serif;">
                                                </div>
                                        <button type="button" class="btn btn-danger btn-sm remove-bom-row" style="font-family: 'Poppins', sans-serif;">
                                            <i class="material-icons-round" style="font-size: 16px;">delete</i>
                                                                                    </button>
                                    </div>
                                </td>
                                    </tr>
                            <?php endforeach; ?>
                                                <?php endif; ?>
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
                                    class="btn bg-gradient-warning mb-0"
                                    style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">save</i>
                                Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- BOM Row Template -->
<template id="bomRowTemplate">
    <tr class="bom-row custom-material" data-material-id="custom">
        <td class="text-center">
        <div class="form-check">
            <input class="form-check-input bom-checkbox" type="checkbox" name="bom_enabled[]" value="custom" checked>
            </div>
        </td>
        <td>
            <div class="input-group input-group-outline is-focused" style="max-width: 300px;">
                <input type="text" name="bom_material_names[]" class="form-control form-control-sm material-input" list="materialList" placeholder="Chọn từ danh sách hoặc nhập mới" style="font-family: 'Poppins', sans-serif;">
                <datalist id="materialList">
                    <?php foreach ($materials as $material): ?>
                        <option value="<?= htmlspecialchars($material->material_name); ?>" data-id="<?= $material->id_material; ?>" data-uom="<?= htmlspecialchars($material->uom); ?>"><?= htmlspecialchars($material->material_name); ?></option>
                    <?php endforeach; ?>
                </datalist>
                <input type="hidden" name="bom_materials[]" class="material-id-input" value="">
            </div>
        </td>
        <td>
            <span class="badge badge-sm bg-gradient-secondary">Chưa có</span>
        </td>
        <td>
            <div class="input-group input-group-outline" style="max-width: 150px;">
                <input type="number" name="bom_quantities[]" class="form-control form-control-sm" step="0.01" min="0.01" placeholder="VD: 10" required style="font-family: 'Poppins', sans-serif;">
                            </div>
</td>
        <td>
            <div class="d-flex align-items-center" style="max-width: 160px; gap:8px;">
                <div class="input-group input-group-outline" style="flex:1;">
                    <input type="text" name="bom_uoms[]" class="form-control form-control-sm uom-input" list="unitList" placeholder="g, kg, pcs" required style="font-family: 'Poppins', sans-serif;">
        </div>
                    <button type="button" class="btn btn-danger btn-sm remove-bom-row" style="font-family: 'Poppins', sans-serif;">
                <i class="material-icons-round" style="font-size: 16px;">delete</i>
                            </button>
        </div>
    </td>
    </tr>
</template>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProductForm');
const bomTable = document.getElementById('bomTable');
    const bomContainer = document.getElementById('bomContainer');
    const addCustomMaterialBtn = document.getElementById('addCustomMaterial');
    const isActiveSwitch = document.getElementById('isActiveSwitch');
    const statusText = document.getElementById('statusText');

    // Material Design input handling
    function setupInputHandlers(container) {
        container.querySelectorAll('.input-group-outline input, .input-group-outline select').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('is-focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('is-focused');
                if (this.value) {
                    this.parentElement.classList.add('is-filled');
                } else {
                    this.parentElement.classList.remove('is-filled');
                }
            });
        });
    }

    setupInputHandlers(document);

    // Checkbox handling - Enable/disable quantity input (for edit view)
    function setupCheckboxHandlers(container) {
        const checkboxes = container.querySelectorAll('.bom-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const row = this.closest('.bom-row');
                const quantityInput = row.querySelector('.bom-quantity') || row.querySelector('input[name="bom_quantities[]"]');
                if (this.checked) {
                    if (quantityInput) {
                        quantityInput.disabled = false;
                        quantityInput.removeAttribute('aria-disabled');
                        quantityInput.required = true;
                        if (quantityInput.value === '' || quantityInput.value === '0') quantityInput.value = '';
                        quantityInput.focus();
                    }
                    row.classList.add('table-active');
                } else {
                    if (quantityInput) {
                        quantityInput.disabled = true;
                        quantityInput.required = false;
                        quantityInput.value = '0';
                    }
                    row.classList.remove('table-active');
                }
            });
        });
    }

    // Khởi tạo bộ xử lý checkbox cho các dòng đã có
    setupCheckboxHandlers(document);

    // Setup existing remove buttons
    document.querySelectorAll('.remove-bom-row').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.bom-row').remove();
        });
    });

    // Toggle status text
    isActiveSwitch.addEventListener('change', function() {
        statusText.textContent = this.checked ? 'Hoạt động' : 'Ngừng hoạt động';
    });

    // Material data mapping
    const materialData = {
        <?php foreach ($materials as $material): ?>
        "<?= htmlspecialchars($material->material_name); ?>": {
            id: "<?= $material->id_material; ?>",
            uom: "<?= htmlspecialchars($material->uom); ?>"
        },
        <?php endforeach; ?>
    };

    // Auto-fill uom when material is selected from datalist
    function setupMaterialAutofill(row) {
        const materialInput = row.querySelector('.material-input');
        const uomInput = row.querySelector('.uom-input');
        const materialIdInput = row.querySelector('.material-id-input');
        
        if (!materialInput) return;
        
        let inputTimeout;
        let lastValue = '';
        
        materialInput.addEventListener('input', function(e) {
            clearTimeout(inputTimeout);
            
            const currentValue = this.value.trim();
            
            // Chờ 100ms để phân biệt giữa gõ từ từ và chọn từ datalist
            inputTimeout = setTimeout(() => {
                if (materialData[currentValue] && currentValue !== lastValue) {
                    // Chọn từ danh sách - tự động điền ngay lập tức
                    uomInput.value = materialData[currentValue].uom;
                    materialIdInput.value = materialData[currentValue].id;
                    uomInput.parentElement.classList.add('is-filled');
                } else if (!materialData[currentValue]) {
                    // Nhập nguyên liệu mới - xóa ID
                    materialIdInput.value = '';
                }
                lastValue = currentValue;
            }, 100);
        });
    }

    // Add BOM Row
    addCustomMaterialBtn.addEventListener('click', function() {
        const newRowFragment = bomRowTemplate.content.cloneNode(true);
        // Append as a table row (template contains a <tr>)
        bomContainer.appendChild(newRowFragment);
        
        const lastRow = bomContainer.lastElementChild; // this should be the new <tr>
        setupInputHandlers(lastRow);
        setupMaterialAutofill(lastRow);
setupBomRowValidation(lastRow);
        setupCheckboxHandlers(lastRow);
        
// Setup remove button
        const removeBtn = lastRow.querySelector('.remove-bom-row');
if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            lastRow.remove();
        });
}
        
        // Focus on material input
        const matInput = lastRow.querySelector('input[name="bom_material_names[]"]');
        if (matInput) matInput.focus();
    });
    
    // Dynamic validation: nếu nhập nguyên liệu thì bắt buộc nhập số lượng và đơn vị
    function setupBomRowValidation(row) {
        const materialInput = row.querySelector('input[name="bom_material_names[]"]');
        const quantityInput = row.querySelector('input[name="bom_quantities[]"]');
        const uomInput = row.querySelector('input[name="bom_uoms[]"]');
        
        if (!materialInput || !quantityInput || !uomInput) return;
        
        materialInput.addEventListener('input', function() {
            if (this.value.trim()) {
                // Có nguyên liệu → bắt buộc số lượng và đơn vị
                quantityInput.setAttribute('required', 'required');
                uomInput.setAttribute('required', 'required');
            } else {
                // Không có nguyên liệu → không bắt buộc
                quantityInput.removeAttribute('required');
                uomInput.removeAttribute('required');
            }
        });
        
        // Kích hoạt lần đầu để check giá trị có sẵn
        materialInput.dispatchEvent(new Event('input'));
    }
    
    // Setup validation cho tất cả BOM rows có sẵn (bao gồm rows được load từ DB)
    document.querySelectorAll('.bom-row').forEach(row => {
        setupBomRowValidation(row);
    });

    // Form validation (follow Add form logic: validate only checked rows and disable unchecked ones before submit)
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

        // Validate BOM - require at least one selected material
        const checkedBoxes = document.querySelectorAll('.bom-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('⚠️ Bắt buộc chọn ít nhất 1 nguyên liệu cho sản phẩm!');
            return false;
        }

        let hasError = false;

        // Validate each checked row (quantity > 0, and for custom rows name & unit present)
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.bom-row');
            const quantityInput = row.querySelector('.bom-quantity') || row.querySelector('input[name="bom_quantities[]"]');
            const quantity = quantityInput ? parseFloat(quantityInput.value) : 0;

            if (!quantity || quantity <= 0) {
                e.preventDefault();
                alert('Vui lòng nhập số lượng > 0 cho tất cả NVL đã chọn!');
                if (quantityInput) quantityInput.focus();
                hasError = true;
                return false;
            }

            if (row.classList.contains('custom-material')) {
                const materialNameInput = row.querySelector('input[name="bom_material_names[]"]');
                const unitInput = row.querySelector('input[name="bom_uoms[]"]');

                if (materialNameInput && !materialNameInput.value.trim()) {
                    e.preventDefault();
                    alert('Vui lòng nhập tên nguyên liệu!');
                    materialNameInput.focus();
                    hasError = true;
                    return false;
                }

                if (unitInput && !unitInput.value.trim()) {
                    e.preventDefault();
                    alert('Vui lòng nhập đơn vị!');
                    unitInput.focus();
                    hasError = true;
                    return false;
                }
            }
        });

        if (hasError) return false;

        // Disable unchecked rows so they are not submitted (keeps arrays aligned like Add form)
        document.querySelectorAll('.bom-row').forEach(row => {
            const checkbox = row.querySelector('.bom-checkbox');
            if (checkbox && !checkbox.checked) {
                row.querySelectorAll('input, select, textarea').forEach(input => input.disabled = true);
            }
        });

        return true;
    });
});
</script>
