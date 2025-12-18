<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?php echo $title; ?></h1>
    
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <div class="row">
                        <div class="col-8 align-items-center pl-4">
                            <h4 class="mb-0">Danh sách nhân viên</h4>
                            <span class="text-sm mb-0 text-end">Quản lý thông tin nhân viên</span>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?php echo base_url('leader/staff/add'); ?>" class="btn btn-dark btn-sm mb-0">+ Thêm nhân viên</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên nhân viên</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($staffs as $index => $staff): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo isset($staff->staff_name) ? $staff->staff_name : (isset($staff->name) ? $staff->name : ''); ?></td>
                            <td><?php echo isset($staff->phone) ? $staff->phone : ''; ?></td>
                            <td><?php echo isset($staff->email) ? $staff->email : ''; ?></td>
                            <td>
                                <?php if (isset($staff->st_status) && $staff->st_status == 1): ?>
                                    <span class="badge badge-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Ngừng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo base_url('leader/staff/'.$staff->id_staff.'/edit'); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (isset($staff->st_status) && $staff->st_status == 1): ?>
                                <a href="<?php echo base_url('leader/deactivateStaff/'.$staff->id_staff); ?>" class="btn btn-warning btn-sm" onclick="return confirm('Bạn có chắc chắn muốn ngừng hoạt động nhân viên này?')">
                                    <i class="fas fa-user-slash"></i>
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo base_url('leader/deleteStaff/'.$staff->id_staff); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
