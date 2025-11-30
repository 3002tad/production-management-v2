<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC1: Customer Management - DELETE CONFIRMATION                              ║
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
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Xác nhận xóa Khách hàng</h6>
                        </div>
                        <div class="col-6 text-end">
                            <a href="<?= site_url('BOD/customer'); ?>" 
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
                        Bạn đang yêu cầu xóa khách hàng khỏi hệ thống. Hành động này <strong>KHÔNG THỂ HOÀN TÁC</strong>!
                    </p>
                </div>

                <!-- Customer Information Card -->
                <div class="card border mt-4">
                    <div class="card-header bg-light">
                        <h6 style="font-family: 'Poppins', sans-serif;">
                            <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">person</i>
                            Thông tin Khách hàng cần xóa
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Mã khách hàng:</label>
                                <p class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= $customer->id_cust; ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Tên khách hàng:</label>
                                <p class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= htmlspecialchars($customer->cust_name); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Email:</label>
                                <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($customer->email) ? htmlspecialchars($customer->email) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Điện thoại:</label>
                                <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($customer->telp) ? htmlspecialchars($customer->telp) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Địa chỉ:</label>
                                <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($customer->address) ? htmlspecialchars($customer->address) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($customer->notes)): ?>
                            <div class="row">
                                <div class="col-12">
                                    <label class="text-muted text-xs" style="font-family: 'Poppins', sans-serif;">Ghi chú:</label>
                                    <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                        <?= htmlspecialchars($customer->notes); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Statistics -->
                <?php if (isset($customer->total_orders) && $customer->total_orders > 0): ?>
                    <div class="alert alert-warning mt-4" role="alert" style="font-family: 'Poppins', sans-serif;">
                        <strong>
                            <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">error</i>
                            RÀNG BUỘC KHÓA NGOẠI (FK CONSTRAINT)
                        </strong>
                        <p class="mb-2 mt-2">
                            Khách hàng này có <strong class="text-danger"><?= $customer->total_orders; ?> đơn hàng</strong> trong hệ thống!
                        </p>
                        <ul class="mb-0">
                            <li>Tổng số lượng đã đặt: <strong><?= number_format($customer->total_quantity ?? 0); ?></strong> chiếc</li>
                            <?php if (isset($customer->last_order_date)): ?>
                                <li>Đơn hàng gần nhất: <strong><?= date('d/m/Y', strtotime($customer->last_order_date)); ?></strong></li>
                            <?php endif; ?>
                        </ul>
                        <hr>
                        <p class="mb-0 text-danger">
                            <strong>⚠️ KHÔNG THỂ XÓA</strong> - Vui lòng xóa tất cả đơn hàng của khách hàng trước khi xóa khách hàng này.
                        </p>
                    </div>

                    <!-- Disabled Delete Button -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="<?= site_url('BOD/customer'); ?>" 
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
                                Không thể xóa (Có đơn hàng)
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
                            Khách hàng này <strong>chưa có đơn hàng nào</strong>. Có thể xóa an toàn.
                        </p>
                    </div>

                    <!-- Confirmation Form -->
                    <form action="<?= site_url('BOD/destroyCustomer'); ?>" 
                          method="POST" 
                          id="deleteForm"
                          class="mt-4">
                        
                        <input type="hidden" name="id_cust" value="<?= $customer->id_cust; ?>">
                        <input type="hidden" name="cust_name" value="<?= htmlspecialchars($customer->cust_name); ?>">
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="confirmDelete"
                                   required>
                            <label class="form-check-label" for="confirmDelete" style="font-family: 'Poppins', sans-serif;">
                                Tôi hiểu rằng hành động này không thể hoàn tác và muốn xóa khách hàng <strong><?= htmlspecialchars($customer->cust_name); ?></strong>
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="<?= site_url('BOD/customer'); ?>" 
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
        // Enable/disable delete button based on checkbox
        confirmCheckbox.addEventListener('change', function() {
            deleteButton.disabled = !this.checked;
        });

        // Final confirmation before submit
        deleteForm.addEventListener('submit', function(e) {
            if (!confirm('BẠN CHẮC CHẮN MUỐN XÓA KHÁCH HÀNG NÀY?\n\nHành động này KHÔNG THỂ HOÀN TÁC!')) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
