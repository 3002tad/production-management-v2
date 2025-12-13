<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm Người Dùng Mới</h3>
                </div>
                <form action="" method="POST">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo set_value('username'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="role_id">Vai Trò <span class="text-danger">*</span></label>
                            <select class="form-control" id="role_id" name="role_id" required onchange="suggestStaff()">
                                <option value="">Chọn vai trò</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role->role_id; ?>" data-role-name="<?php echo $role->role_name; ?>">
                                        <?php echo $role->role_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="staff_id">Liên Kết Nhân Viên</label>
                            <select class="form-control" id="staff_id" name="staff_id">
                                <option value="">Không liên kết</option>
                                <?php foreach ($staff_without_user as $staff): ?>
                                    <option value="<?php echo $staff->id_staff; ?>" data-position="<?php echo $staff->position; ?>" data-group="<?php echo $staff->staff_group; ?>">
                                        <?php echo $staff->staff_name; ?> (<?php echo $staff->position; ?> - <?php echo $staff->staff_group; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Chọn nhân viên để liên kết với user này (tùy chọn)</small>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Tạo User</button>
                        <a href="<?php echo site_url('admin/user'); ?>" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function suggestStaff() {
    const roleSelect = document.getElementById('role_id');
    const staffSelect = document.getElementById('staff_id');
    const selectedRole = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedRole.getAttribute('data-role-name');

    // Auto-suggest staff based on role
    const positionToRoleMap = {
        'Giám Đốc': 'bod',
        'Trưởng Dây Chuyền': 'line_manager',
        'Nhân Viên Kho': 'warehouse_staff',
        'Nhân Viên QC': 'qc_staff',
        'Kỹ Thuật Viên': 'technical_staff',
        'Công Nhân': 'worker',
        'Administrator': 'system_admin',
        'Leader': 'line_manager'
    };

    // Reset selection
    staffSelect.selectedIndex = 0;

    // Find matching staff
    for (let i = 1; i < staffSelect.options.length; i++) {
        const option = staffSelect.options[i];
        const position = option.getAttribute('data-position');
        const group = option.getAttribute('data-group');

        if (positionToRoleMap[position] === roleName.toLowerCase() || group === roleName.toLowerCase()) {
            staffSelect.selectedIndex = i;
            break;
        }
    }
}
</script>