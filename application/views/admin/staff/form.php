<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= isset($staff) ? 'Chỉnh sửa thông tin' : 'Thêm nhân viên mới' ?>
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= current_url() ?>" method="post">
                <div class="form-group">
                    <label for="username">Tên đăng nhập *</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?= isset($staff) ? $staff->username : '' ?>" 
                           <?= isset($staff) ? 'readonly' : 'required' ?>>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu <?= isset($staff) ? '(để trống nếu không thay đổi)' : '*' ?></label>
                    <input type="password" class="form-control" id="password" name="password" 
                           <?= isset($staff) ? '' : 'required' ?>>
                </div>

                <div class="form-group">
                    <label for="role_id">Chức vụ *</label>
                    <select class="form-control" id="role_id" name="role_id" required>
                        <option value="">Chọn chức vụ</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->role_id ?>" 
                                    <?= (isset($staff) && $staff->role_id == $role->role_id) ? 'selected' : '' ?>>
                                <?= $role->role_display_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="full_name">Họ và Tên *</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" 
                           value="<?= isset($staff) ? $staff->full_name : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= isset($staff) ? $staff->email : '' ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="<?= isset($staff) ? $staff->phone : '' ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <a href="<?= base_url('hr') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>