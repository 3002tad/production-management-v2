<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h2><?php echo isset($staff) ? 'Cập nhật nhân sự' : 'Tạo nhân sự mới'; ?></h2>

<?php if (!empty($error)): ?>
    <div style="color:red; padding:8px; border:1px solid #eaa; background:#fee; margin-bottom:10px;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php echo form_open(); ?>
<?php $old = isset($old) ? $old : array(); ?>

<p>
    <label>Tên nhân sự *</label><br />
    <input type="text" name="staff_name" required value="<?php echo isset($old['staff_name']) ? htmlspecialchars($old['staff_name']) : (isset($staff) ? htmlspecialchars($staff->staff_name) : ''); ?>" />
</p>

<p>
    <label>Email *</label><br />
    <input type="email" name="email" required value="<?php echo isset($old['email']) ? htmlspecialchars($old['email']) : (isset($staff) ? htmlspecialchars($staff->email) : ''); ?>" />
    <?php if (!empty($duplicate_email)): ?>
        <div style="color:red; font-size:0.95em; margin-top:6px;">Email đã tồn tại trong hệ thống.</div>
    <?php endif; ?>
</p>

<p>
    <label>Số điện thoại * (bắt đầu bằng 0, đúng 10 chữ số)</label><br />
    <input type="text" name="phone" required value="<?php echo isset($old['phone']) ? htmlspecialchars($old['phone']) : (isset($staff) ? htmlspecialchars($staff->phone) : ''); ?>" placeholder="0xxxxxxxxx" />
    <?php if (!empty($duplicate_phone)): ?>
        <div style="color:red; font-size:0.95em; margin-top:6px;">Số điện thoại đã tồn tại trong hệ thống.</div>
    <?php endif; ?>
</p>

<p>
    <label>Bộ phận</label><br />
    <select name="department">
        <option value="">-- Chọn bộ phận --</option>
        <?php if (!empty($departments)): ?>
            <?php foreach ($departments as $dept): ?>
                <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo (isset($old['department']) && $old['department'] == $dept) || (isset($staff) && $staff->department == $dept) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($dept); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</p>

<p>
    <label>Vị trí</label><br />
    <select name="position">
        <option value="">-- Chọn vị trí --</option>
        <?php if (!empty($positions)): ?>
            <?php foreach ($positions as $pos): ?>
                <option value="<?php echo htmlspecialchars($pos); ?>" <?php echo (isset($old['position']) && $old['position'] == $pos) || (isset($staff) && $staff->position == $pos) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($pos); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</p>

<p>
    <label>Trạng thái</label><br />
    <select name="st_status">
        <option value="1" <?php echo (isset($old['st_status']) && $old['st_status'] == 1) || (isset($staff) && $staff->st_status == 1) || !isset($staff) ? 'selected' : ''; ?>>Hoạt động</option>
        <option value="0" <?php echo (isset($old['st_status']) && $old['st_status'] == 0) || (isset($staff) && $staff->st_status == 0) ? 'selected' : ''; ?>>Không hoạt động</option>
    </select>
</p>

<p>
    <button type="submit">Lưu</button>
    <a href="<?php echo site_url('UC3_QLNS/Staffs'); ?>">Hủy</a>
</p>

<?php echo form_close(); ?>
