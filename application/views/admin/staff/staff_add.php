<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm Nhân Viên Mới</h3>
                </div>
                <form action="<?php echo site_url('admin/staff/add_process'); ?>" method="POST">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="staff_name">Tên Nhân Viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="staff_name" name="staff_name" value="<?php echo set_value('staff_name'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Số Điện Thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo set_value('phone'); ?>" placeholder="0xxxxxxxxx">
                        </div>

                        <div class="form-group">
                            <label for="department">Bộ Phận</label>
                            <select class="form-control" id="department" name="department">
                                <option value="">Chưa Phân Loại</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept; ?>" <?php echo set_select('department', $dept); ?>><?php echo $dept; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="position">Chức Vụ</label>
                            <select class="form-control" id="position" name="position">
                                <option value="">Chưa Phân Loại</option>
                                <?php foreach ($positions as $pos): ?>
                                    <option value="<?php echo $pos; ?>" <?php echo set_select('position', $pos); ?>><?php echo $pos; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="st_status">Trạng Thái</label>
                            <select class="form-control" id="st_status" name="st_status">
                                <option value="1" <?php echo set_select('st_status', '1', TRUE); ?>>Active</option>
                                <option value="2" <?php echo set_select('st_status', '2'); ?>>Inactive</option>
                            </select>
                        </div>

                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="<?php echo site_url('admin/staff'); ?>" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>