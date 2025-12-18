<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC2: Product Management - DELETE CONFIRMATION with BOM display             ║
║  Material Design 3.0 với FK constraint warning                              ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <!-- Card Header -->
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-danger shadow-danger border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-6 d-flex align-items-center">
                            <i class="material-icons-round text-white opacity-10 me-2" style="font-size: 24px;">delete_forever</i>
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Xác nhận xóa Sản phẩm</h6>
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
                <!-- Warning Alert -->
                <div class="alert alert-danger text-white" role="alert" style="font-family: 'Poppins', sans-serif;">
                    <strong>
                        <i class="material-icons-round" style="font-size: 20px; vertical-align: middle;">warning</i>
                        CẢNH BÁO XÓA DỮ LIỆU
                    </strong>
                    <p class="mb-0 mt-2">
                        Bạn đang yêu cầu xóa sản phẩm khỏi hệ thống. Hành động này <strong>KHÔNG THỂ HOÀN TÁC</strong>!
                    </p>
                </div>

                <!-- Product Information Card -->
                <div class="card border mt-4">
                    <div class="card-header bg-light">
                        <h6 style="font-family: 'Poppins', sans-serif;">
                            <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">inventory_2</i>
                            Thông tin Sản phẩm cần xóa
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Mã sản phẩm:</label>
                                <p class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= $product->id_product; ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Tên sản phẩm:</label>
                                <p class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= htmlspecialchars($product->product_name); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Đường kính:</label>
                                <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                    <span class="badge badge-sm bg-gradient-secondary"><?= $product->diameter; ?>mm</span>
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Ứng dụng:</label>
                                <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($product->application) ? htmlspecialchars($product->application) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Tóm tắt:</label>
                                <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($product->summary) ? htmlspecialchars($product->summary) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOM Information -->
                <?php if (isset($product->bom_data['materials']) && !empty($product->bom_data['materials'])): ?>
                    <div class="card border mt-4">
                        <div class="card-header bg-light">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">precision_manufacturing</i>
                                Bill of Materials (BOM)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif;">STT</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif;">Nguyên liệu</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif;">Số lượng</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="font-family: 'Poppins', sans-serif;">Đơn vị</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($product->bom_data['materials'] as $material): ?>
                                            <tr>
                                                <td class="text-sm" style="font-family: 'Poppins', sans-serif;"><?= $no++; ?></td>
                                                <td class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                                    <?= htmlspecialchars($material['material_name']); ?>
                                                </td>
                                                <td class="text-center text-sm" style="font-family: 'Poppins', sans-serif;">
                                                    <?= $material['quantity_per_unit'] ?? $material['quantity'] ?? 0; ?>
                                                </td>
                                                <td class="text-center text-sm" style="font-family: 'Poppins', sans-serif;">
                                                    <?= !empty($material['unit']) ? htmlspecialchars($material['unit']) : '<em class="text-muted">—</em>'; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs text-muted mt-2 mb-0" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                BOM này sẽ bị xóa cùng với sản phẩm
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-4" role="alert" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">info</i>
                        Sản phẩm này chưa có BOM
                    </div>
                <?php endif; ?>

                <!-- Order Statistics & FK Constraint -->
                <?php if ((isset($product->total_orders) && $product->total_orders > 0) || (!empty($product->references))): ?>
                    <div class="alert alert-warning mt-4" role="alert" style="font-family: 'Poppins', sans-serif;">
                        <strong>
                            <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">error</i>
                            RÀNG BUỘC KHÓA NGOẠI (FK CONSTRAINT)
                        </strong>
                        <?php if (isset($product->total_orders) && $product->total_orders > 0): ?>
                            <p class="mb-2 mt-2">
                                Sản phẩm này có <strong class="text-danger"><?= $product->total_orders; ?> đơn hàng</strong> trong hệ thống!
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($product->references)): ?>
                            <p class="mb-2 mt-2">
                                Ngoài ra, sản phẩm còn được tham chiếu bởi các bảng khác:
                            </p>
                            <ul class="mb-2">
                                <?php foreach ($product->references as $tbl => $cnt): ?>
                                    <li><strong><?= htmlspecialchars($tbl); ?></strong>: <?= (int)$cnt; ?> bản ghi</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <hr>
                        <p class="mb-0 text-danger">
                            <strong>⚠️ KHÔNG THỂ XÓA</strong> - Vui lòng xóa hoặc cập nhật các bản ghi liên quan trước khi xóa sản phẩm.
                        </p>
                    </div>

                    <!-- Disabled Delete Button -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="<?= site_url('BOD/product'); ?>" 
                               class="btn bg-gradient-secondary mb-0"
                               style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">arrow_back</i>
                                Quay lại danh sách
                            </a>
                            <button type="button" 
                                    class="btn bg-gradient-danger mb-0"
                                    disabled
                                    style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">delete_forever</i>
                                Không thể xóa (Có dữ liệu liên quan)
                            </button>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- No Orders - Can Delete -->
                    <div class="alert alert-success mt-4" role="alert" style="font-family: 'Poppins', sans-serif;">
                        <strong>
                            <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">check_circle</i>
                            KIỂM TRA RÀNG BUỘC
                        </strong>
                        <p class="mb-0 mt-2">
                            Sản phẩm này <strong>chưa có đơn hàng nào</strong>. Có thể xóa an toàn.
                        </p>
                    </div>

                    <!-- Confirmation Form -->
                    <form action="<?= site_url('BOD/destroyProduct'); ?>" 
                          method="POST" 
                          id="deleteForm"
                          class="mt-4">
                        
                        <input type="hidden" name="id_product" value="<?= $product->id_product; ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product->product_name); ?>">
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="confirmDelete"
                                   required>
                            <label class="form-check-label" for="confirmDelete" style="font-family: 'Poppins', sans-serif;">
                                Tôi hiểu rằng hành động này không thể hoàn tác và muốn xóa sản phẩm <strong><?= htmlspecialchars($product->product_name); ?></strong>
                                <?php if (isset($product->bom_data['materials']) && !empty($product->bom_data['materials'])): ?>
                                    (bao gồm BOM với <?= count($product->bom_data['materials']); ?> nguyên liệu)
                                <?php endif; ?>
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="<?= site_url('BOD/product'); ?>" 
                                   class="btn bg-gradient-secondary mb-0 me-2"
                                   style="font-family: 'Poppins', sans-serif;">
                                    <i class="material-icons-round opacity-10" style="font-size: 18px;">close</i>
                                    Hủy bỏ
                                </a>
                                <button type="submit" 
                                        class="btn bg-gradient-danger mb-0"
                                        id="deleteButton"
                                        disabled
                                        style="font-family: 'Poppins', sans-serif;">
                                    <i class="material-icons-round opacity-10" style="font-size: 18px;">delete_forever</i>
                                    Xác nhận xóa
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirmDelete');
    const deleteButton = document.getElementById('deleteButton');
    const deleteForm = document.getElementById('deleteForm');

    if (confirmCheckbox && deleteButton) {
        confirmCheckbox.addEventListener('change', function() {
            deleteButton.disabled = !this.checked;
        });

        deleteForm.addEventListener('submit', function(e) {
            if (!confirm('BẠN CHẮC CHẮN MUỐN XÓA SẢN PHẨM NÀY?\n\nHành động này KHÔNG THỂ HOÀN TÁC!')) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
