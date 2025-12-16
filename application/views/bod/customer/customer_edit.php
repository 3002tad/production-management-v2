<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC1: Customer Management - EDIT FORM                                        ║
║  Material Design 3.0 với is_active toggle                                   ║
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
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Chỉnh sửa Khách hàng</h6>
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
                <!-- Customer Info Card -->
                <div class="alert alert-secondary" role="alert" style="font-family: 'Poppins', sans-serif;">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">badge</i> Mã KH:</strong> 
                            <?= $customer->id_cust; ?>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">schedule</i> Tạo lúc:</strong> 
                            <?= date('d/m/Y H:i', strtotime($customer->created_at)); ?>
                        </div>
                    </div>
                </div>

                <form action="<?= site_url('BOD/updateCustomer'); ?>" method="POST" id="editCustomerForm">
                    <input type="hidden" name="id_cust" value="<?= $customer->id_cust; ?>">
                    
                    <div class="row mt-4">
                        <!-- Tên khách hàng -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Tên khách hàng <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="cust_name" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($customer->cust_name); ?>"
                                       required
                                       maxlength="50"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline <?= !empty($customer->email) ? 'is-filled' : ''; ?>">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($customer->email ?? ''); ?>"
                                       maxlength="25"
                                       required
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-warning" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">warning</i>
                                Max 25 ký tự (giới hạn DB)
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Điện thoại -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline <?= !empty($customer->telp) ? 'is-filled' : ''; ?>">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="telp" 
                                       id="telpInput"
                                       class="form-control"
                                       value="<?= htmlspecialchars($customer->telp ?? ''); ?>"
                                       required
                                       pattern="[0-9]{8,15}"
                                       maxlength="15"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-muted" id="telpHint" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Chỉ số, 8-15 chữ số (lưu dạng chuỗi số VARCHAR(20) trong DB)
                            </small>
                            <small class="text-danger" id="telpError" style="font-family: 'Poppins', sans-serif; display: none;">
                                <i class="material-icons-round" style="font-size: 12px;">error</i>
                                <span id="telpErrorMessage"></span>
                            </small>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline <?= !empty($customer->address) ? 'is-filled' : ''; ?>">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Địa chỉ <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="address" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($customer->address ?? ''); ?>"
                                       maxlength="50"
                                       required
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-warning" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">warning</i>
                                Max 50 ký tự (giới hạn DB)
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ghi chú -->
                        <div class="col-md-8 mb-3">
                            <div class="input-group input-group-outline <?= !empty($customer->notes) ? 'is-filled' : ''; ?>">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Ghi chú
                                </label>
                                <textarea name="notes" 
                                          class="form-control" 
                                          rows="3"
                                          style="font-family: 'Poppins', sans-serif;"><?= htmlspecialchars($customer->notes ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Trạng thái (is_active toggle) -->
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
                                               <?= ($customer->is_active == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="isActiveSwitch" style="font-family: 'Poppins', sans-serif;">
                                            <span id="statusText">
                                                <?= ($customer->is_active == 1) ? 'Hoạt động' : 'Ngừng hoạt động'; ?>
                                            </span>
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2" style="font-family: 'Poppins', sans-serif;">
                                        Khách hàng ngừng hoạt động sẽ không thể tạo đơn hàng mới
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <?php if (isset($customer->total_orders) && $customer->total_orders > 0): ?>
                        <div class="alert alert-info mt-3" role="alert" style="font-family: 'Poppins', sans-serif;">
                            <strong><i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">analytics</i> Thống kê:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Tổng đơn hàng: <strong><?= $customer->total_orders; ?></strong> đơn</li>
                                <li>Tổng số lượng: <strong><?= number_format($customer->total_quantity ?? 0); ?></strong> chiếc</li>
                                <?php if (isset($customer->last_order_date)): ?>
                                    <li>Đơn hàng gần nhất: <strong><?= date('d/m/Y', strtotime($customer->last_order_date)); ?></strong></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <a href="<?= site_url('BOD/customer'); ?>" 
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

<!-- Input validation styles -->
<style>
.form-control.is-invalid {
    border-color: #f44336 !important;
    box-shadow: 0 0 0 2px rgba(244, 67, 54, 0.2) !important;
}

.text-danger {
    color: #f44336 !important;
    font-weight: 500;
}

.text-danger i {
    vertical-align: middle;
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editCustomerForm');
    const telpInput = document.querySelector('input[name="telp"]');
    const emailInput = document.querySelector('input[name="email"]');
    const isActiveSwitch = document.getElementById('isActiveSwitch');
    const statusText = document.getElementById('statusText');

    // Material Design input handling
    document.querySelectorAll('.input-group-outline input, .input-group-outline textarea').forEach(input => {
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

    // Toggle status text
    isActiveSwitch.addEventListener('change', function() {
        statusText.textContent = this.checked ? 'Hoạt động' : 'Ngừng hoạt động';
    });

    // Validate telp (only numbers) + Real-time validation
    const telpHint = document.getElementById('telpHint');
    const telpError = document.getElementById('telpError');
    const telpErrorMessage = document.getElementById('telpErrorMessage');
    
    telpInput.addEventListener('input', function(e) {
        // Chỉ cho phép số
        this.value = this.value.replace(/[^0-9]/g, '');
        
        const length = this.value.length;
        
        // Hiển thị cảnh báo real-time
        if (length > 15) {
            // Cắt bỏ phần thừa
            this.value = this.value.substring(0, 15);
            telpHint.style.display = 'none';
            telpError.style.display = 'block';
            telpErrorMessage.textContent = 'Tối đa 15 chữ số! Đã tự động cắt bỏ phần thừa.';
            this.classList.add('is-invalid');
            
            // Xóa cảnh báo sau 3 giây
            setTimeout(() => {
                telpError.style.display = 'none';
                telpHint.style.display = 'block';
                this.classList.remove('is-invalid');
            }, 3000);
        } else if (length > 0 && length < 8) {
            telpHint.style.display = 'none';
            telpError.style.display = 'block';
            telpErrorMessage.textContent = `Hiện tại: ${length} chữ số (tối thiểu 8)`;
            this.classList.add('is-invalid');
        } else if (length >= 8 && length <= 15) {
            telpHint.style.display = 'block';
            telpError.style.display = 'none';
            this.classList.remove('is-invalid');
        } else {
            telpHint.style.display = 'block';
            telpError.style.display = 'none';
            this.classList.remove('is-invalid');
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const telp = telpInput.value;
        const email = emailInput.value;

        if (telp.length < 8 || telp.length > 15) {
            e.preventDefault();
            alert('Số điện thoại phải từ 8-15 chữ số!');
            telpInput.focus();
            return false;
        }

        if (email && email.length > 25) {
            e.preventDefault();
            alert('Email không được vượt quá 25 ký tự!');
            emailInput.focus();
            return false;
        }
    });
});
</script>
