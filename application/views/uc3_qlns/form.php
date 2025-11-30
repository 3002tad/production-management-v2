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
    <label>Mã nhân sự</label><br />
    <input type="text" name="code" value="<?php echo isset($old['code']) ? htmlspecialchars($old['code']) : (isset($staff) ? htmlspecialchars($staff->code) : set_value('code')); ?>" <?php echo isset($staff) ? 'readonly' : ''; ?> />
</p>
<p>
    <label>Tên</label><br />
    <input type="text" name="name" value="<?php echo isset($old['name']) ? htmlspecialchars($old['name']) : (isset($staff) ? htmlspecialchars($staff->name) : set_value('name')); ?>" />
</p>
<p>
    <label>Kỹ năng</label><br />
    <input type="text" name="skill" value="<?php echo isset($old['skill']) ? htmlspecialchars($old['skill']) : (isset($staff) ? htmlspecialchars($staff->skill) : set_value('skill')); ?>" />
</p>
<p>
    <label>Phone (bắt đầu bằng 0, đúng 10 chữ số)</label><br />
    <input type="text" name="phone" value="<?php echo isset($old['phone']) ? htmlspecialchars($old['phone']) : (isset($staff) ? htmlspecialchars($staff->phone) : set_value('phone')); ?>" placeholder="0xxxxxxxxx" />
    <?php if (!empty($duplicate_phone)): ?>
        <div style="color:red; font-size:0.95em; margin-top:6px;">Số điện thoại đã tồn tại trong hệ thống.</div>
    <?php endif; ?>
</p>
<p>
    <button type="submit">Lưu</button>
    <a href="<?php echo site_url('UC3_QLNS/Staffs'); ?>">Hủy</a>
 </p>

<?php if (!empty($duplicate_phone) || (!empty($error) && strpos($error, 'điện thoại') !== false)): ?>
    <div style="margin-top:10px;">
        <strong>Hành động:</strong>
        &nbsp;
        <a href="<?php echo isset($staff) ? site_url('UC3_QLNS/Staffs/edit/'.$staff->id) : site_url('UC3_QLNS/Staffs/create'); ?>">Nhập lại</a>
        &nbsp;|&nbsp;
        <a href="<?php echo site_url('UC3_QLNS/Staffs'); ?>">Hủy</a>
    </div>
<?php endif; ?>

<?php echo form_close(); ?>
