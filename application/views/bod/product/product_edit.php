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
                            <div class="input-group input-group-outline <?= !empty($product->application) ? 'is-filled' : ''; ?>">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Ứng dụng (Màu mực) <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="application" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($product->application ?? ''); ?>"
                                       required
                                       maxlength="100"
                                       style="font-family: 'Poppins', sans-serif;">
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
                        Bill of Materials (BOM) - Danh sách Nguyên liệu
                    </h6>
                    <hr class="horizontal dark mt-2 mb-3">

                    <div id="bomContainer">
                        <?php if (isset($product->bom_data['materials']) && !empty($product->bom_data['materials'])): ?>
                            <?php foreach ($product->bom_data['materials'] as $material): ?>
                                <div class="row bom-row mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label" style="font-family: 'Poppins', sans-serif; margin-bottom: 5px;">Nguyên liệu</label>
                                        <div class="input-group input-group-outline is-filled">
                                            <input type="text" 
                                                   name="bom_material_names[]" 
                                                   class="form-control material-input" 
                                                   list="materialList"
                                                   value="<?= htmlspecialchars($material['material_name']); ?>"
                                                   style="font-family: 'Poppins', sans-serif;">
                                            <datalist id="materialList">
                                                <?php foreach ($materials as $mat): ?>
                                                    <option 
                                                        value="<?= htmlspecialchars($mat->material_name); ?>" 
                                                        data-id="<?= $mat->id_material; ?>"
                                                        data-unit="<?= htmlspecialchars($mat->unit); ?>">
                                                        <?= htmlspecialchars($mat->material_display); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </datalist>
                                            <input type="hidden" 
                                                   name="bom_materials[]" 
                                                   class="material-id-input"
                                                   value="<?= $material['id_material'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" style="font-family: 'Poppins', sans-serif; margin-bottom: 5px;">Số lượng</label>
                                        <div class="input-group input-group-outline is-filled">
                                            <input type="number" 
                                                   name="bom_quantities[]" 
                                                   class="form-control"
                                                   value="<?= $material['quantity']; ?>"
                                                   step="0.01"
                                                   min="0.01"
                                                   style="font-family: 'Poppins', sans-serif;">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" style="font-family: 'Poppins', sans-serif; margin-bottom: 5px;">Đơn vị</label>
                                        <div class="input-group input-group-outline is-filled">
                                            <input type="text" 
                                                   name="bom_units[]" 
                                                   class="form-control unit-input"
                                                   list="unitList"
                                                   value="<?= htmlspecialchars($material['unit']); ?>"
                                                   style="font-family: 'Poppins', sans-serif;">
                                            <datalist id="unitList">
                                                <option value="g">g (gam)</option>
                                                <option value="kg">kg (kilogram)</option>
                                                <option value="pcs">pcs (cái)</option>
                                                <option value="l">l (lít)</option>
                                                <option value="ml">ml (mililít)</option>
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
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">info</i>
                                Sản phẩm chưa có BOM. Nhấn "Thêm nguyên liệu" để tạo BOM.
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" 
                            id="addBomRow" 
                            class="btn btn-outline-success btn-sm mt-2"
                            style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">add_circle_outline</i>
                        Thêm nguyên liệu
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
                            data-unit="<?= htmlspecialchars($material->unit); ?>">
                            <?= htmlspecialchars($material->material_display); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
                <input type="hidden" name="bom_materials[]" class="material-id-input">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-family: 'Poppins', sans-serif; margin-bottom: 5px;">Số lượng</label>
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
    const form = document.getElementById('editProductForm');
    const bomContainer = document.getElementById('bomContainer');
    const addBomBtn = document.getElementById('addBomRow');
    const bomRowTemplate = document.getElementById('bomRowTemplate');
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
            unit: "<?= htmlspecialchars($material->unit); ?>"
        },
        <?php endforeach; ?>
    };

    // Auto-fill unit when material is selected from datalist
    function setupMaterialAutofill(row) {
        const materialInput = row.querySelector('.material-input');
        const unitInput = row.querySelector('.unit-input');
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
                    unitInput.value = materialData[currentValue].unit;
                    materialIdInput.value = materialData[currentValue].id;
                    unitInput.parentElement.classList.add('is-filled');
                } else if (!materialData[currentValue]) {
                    // Nhập nguyên liệu mới - xóa ID
                    materialIdInput.value = '';
                }
                lastValue = currentValue;
            }, 100);
        });
    }

    // Add BOM Row
    addBomBtn.addEventListener('click', function() {
        const newRow = bomRowTemplate.content.cloneNode(true);
        bomContainer.appendChild(newRow);
        setupInputHandlers(bomContainer);
        
        const lastRow = bomContainer.lastElementChild;
        setupMaterialAutofill(lastRow);
        
        const removeBtn = lastRow.querySelector('.remove-bom-row');
        removeBtn.addEventListener('click', function() {
            lastRow.remove();
        });
        
        // Setup dynamic validation for new row
        setupBomRowValidation(lastRow);
    });
    
    // Dynamic validation: nếu nhập nguyên liệu thì bắt buộc nhập số lượng và đơn vị
    function setupBomRowValidation(row) {
        const materialInput = row.querySelector('input[name="bom_material_names[]"]');
        const quantityInput = row.querySelector('input[name="bom_quantities[]"]');
        const unitInput = row.querySelector('input[name="bom_units[]"]');
        
        if (!materialInput || !quantityInput || !unitInput) return;
        
        materialInput.addEventListener('input', function() {
            if (this.value.trim()) {
                // Có nguyên liệu → bắt buộc số lượng và đơn vị
                quantityInput.setAttribute('required', 'required');
                unitInput.setAttribute('required', 'required');
            } else {
                // Không có nguyên liệu → không bắt buộc
                quantityInput.removeAttribute('required');
                unitInput.removeAttribute('required');
            }
        });
        
        // Kích hoạt lần đầu để check giá trị có sẵn
        materialInput.dispatchEvent(new Event('input'));
    }
    
    // Setup validation cho tất cả BOM rows có sẵn (bao gồm rows được load từ DB)
    document.querySelectorAll('.bom-row').forEach(row => {
        setupBomRowValidation(row);
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const materials = document.querySelectorAll('select[name="bom_materials[]"]');
        const quantities = document.querySelectorAll('input[name="bom_quantities[]"]');
        const units = document.querySelectorAll('input[name="bom_units[]"]');

        for (let i = 0; i < materials.length; i++) {
            if (materials[i].value) {
                if (!quantities[i].value || parseFloat(quantities[i].value) <= 0) {
                    e.preventDefault();
                    alert('Vui lòng nhập số lượng hợp lệ!');
                    quantities[i].focus();
                    return false;
                }
                if (!units[i].value.trim()) {
                    e.preventDefault();
                    alert('Vui lòng nhập đơn vị!');
                    units[i].focus();
                    return false;
                }
            }
        }
    });
});
</script>
