<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h2>Danh sách nhân sự</h2>

<?php if (!empty($this->session->flashdata('message'))): ?>
    <div style="color:green"><?php echo $this->session->flashdata('message'); ?></div>
<?php endif; ?>
<?php if (!empty($this->session->flashdata('error'))): ?>
    <div style="color:red"><?php echo $this->session->flashdata('error'); ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div style="color:red"><?php echo $error; ?></div>
<?php endif; ?>

<form method="get">
    Mã NV: <input type="text" name="code" value="<?php echo set_value('code'); ?>" />
    Kỹ năng: <input type="text" name="skill" value="<?php echo set_value('skill'); ?>" />
    <button type="submit">Tìm</button>
    <a href="<?php echo site_url('UC3_QLNS/Staffs'); ?>">Reset</a>
</form>

<?php if (!isset($readonly) || !$readonly): ?>
    <p><a href="<?php echo site_url('UC3_QLNS/Staffs/create'); ?>">Thêm nhân sự mới</a></p>
<?php endif; ?>

<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Mã</th><th>Tên</th><th>Kỹ năng</th><th>Phone</th><th>Trạng thái</th><th>Hành động</th></tr>
    <?php foreach ($staffs as $s): ?>
        <tr>
            <td><?php echo $s->id; ?></td>
            <td><?php echo htmlspecialchars($s->code); ?></td>
            <td><?php echo htmlspecialchars($s->name); ?></td>
            <td><?php echo htmlspecialchars($s->skill); ?></td>
            <td><?php echo htmlspecialchars($s->phone); ?></td>
            <td><?php echo ($s->active ? 'Hoạt động' : 'Ngừng hoạt động'); ?></td>
            <td>
                <?php if (!isset($readonly) || !$readonly): ?>
                    <a href="<?php echo site_url('UC3_QLNS/Staffs/edit/'.$s->id); ?>">Sửa</a> |
                    <a href="<?php echo site_url('UC3_QLNS/Staffs/deactivate/'.$s->id); ?>">Ngừng hoạt động</a>
                <?php else: ?>
                    (Chỉ xem)
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
