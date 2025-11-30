<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC1: Customer Management - ADD FORM                                         ║
║  Material Design 3.0 với Poppins font & Material Icons Round                ║
║  Validation: telp INT 8-15 digits, email max 25, address max 50             ║
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
                            <i class="material-icons-round text-white opacity-10 me-2" style="font-size: 24px;">person_add</i>
                            <h6 class="text-white mb-0" style="font-family: 'Poppins', sans-serif;">Thêm Khách hàng mới</h6>
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
                <form action="<?= site_url('BOD/storeCustomer'); ?>" method="POST" id="addCustomerForm">
                    <div class="row mt-4">
                        <!-- Tên khách hàng (Required) -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Tên khách hàng <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="cust_name" 
                                       class="form-control" 
                                       required
                                       maxlength="100"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Tối đa 100 ký tự
                            </small>
                        </div>

                        <!-- Email (Optional, max 25 chars) -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control"
                                       maxlength="25"
                                       required
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Tối đa 25 ký tự (theo database)
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Điện thoại (INT 8-15 digits) -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="telp" 
                                       id="telpInput"
                                       class="form-control"
                                       required
                                       pattern="[0-9]{8,15}"
                                       maxlength="15"
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-muted" id="telpHint" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Chỉ số, 8-15 chữ số (lưu dạng INT trong DB)
                            </small>
                            <small class="text-danger" id="telpError" style="font-family: 'Poppins', sans-serif; display: none;">
                                <i class="material-icons-round" style="font-size: 12px;">error</i>
                                <span id="telpErrorMessage"></span>
                            </small>
                        </div>

                        <!-- Địa chỉ (max 50 chars) -->
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Địa chỉ <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="address" 
                                       class="form-control"
                                       maxlength="50"
                                       required
                                       style="font-family: 'Poppins', sans-serif;">
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Tối đa 50 ký tự (theo database)
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ghi chú (Optional) -->
                        <div class="col-12 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label" style="font-family: 'Poppins', sans-serif;">
                                    Ghi chú
                                </label>
                                <textarea name="notes" 
                                          class="form-control" 
                                          rows="3"
                                          style="font-family: 'Poppins', sans-serif;"></textarea>
                            </div>
                            <small class="text-muted" style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round" style="font-size: 12px;">info</i>
                                Thông tin bổ sung về khách hàng (không bắt buộc)
                            </small>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="alert alert-info" role="alert" style="font-family: 'Poppins', sans-serif;">
                        <strong><i class="material-icons-round" style="font-size: 16px; vertical-align: middle;">info</i> Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Khách hàng mới sẽ tự động có trạng thái <strong>Hoạt động</strong></li>
                            <li>Email phải là duy nhất trong hệ thống</li>
                            <li>Số điện thoại chỉ nhập số, không có dấu cách hoặc ký tự đặc biệt</li>
                            <li>Các trường đánh dấu <span class="text-danger">*</span> là bắt buộc</li>
                        </ul>
                    </div>

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
                                    class="btn bg-gradient-success mb-0"
                                    style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round opacity-10" style="font-size: 18px;">save</i>
                                Lưu khách hàng
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

<!-- Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addCustomerForm');
    const telpInput = document.querySelector('input[name="telp"]');
    const emailInput = document.querySelector('input[name="email"]');

    // Material Design input focus
    document.querySelectorAll('.input-group-outline input, .input-group-outline textarea').forEach(input => {
        if (input.value) {
            input.parentElement.classList.add('is-filled');
        }
        
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

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const telp = telpInput.value;
        const email = emailInput.value;

        // Validate telp length
        if (telp.length < 8 || telp.length > 15) {
            e.preventDefault();
            alert('Số điện thoại phải từ 8-15 chữ số!');
            telpInput.focus();
            return false;
        }

        // Validate email length
        if (email && email.length > 25) {
            e.preventDefault();
            alert('Email không được vượt quá 25 ký tự (giới hạn database)!');
            emailInput.focus();
            return false;
        }

        return true;
    });
});
</script>
