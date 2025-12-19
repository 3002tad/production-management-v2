<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản Lý Nhân Viên</h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/staff/add'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Thêm Nhân Viên
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $statistics['total']; ?></h3>
                                    <p>Tổng nhân viên</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo $statistics['active']; ?></h3>
                                    <p>Đang Hoạt Động</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo $statistics['with_user']; ?></h3>
                                    <p>Có tài khoản</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3><?php echo $statistics['without_user']; ?></h3>
                                    <p>Chưa có tài khoản</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-times"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Bộ phận:</label>
                                <select name="department" class="form-control">
                                    <option value="">Chọn bộ phận</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept; ?>" <?php echo ($this->input->get('department') == $dept) ? 'selected' : ''; ?>>
                                            <?php echo $dept; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Chức vụ:</label>
                                <select name="position" class="form-control">
                                    <option value="">Chọn chức vụ</option>
                                    <?php foreach ($positions as $pos): ?>
                                        <option value="<?php echo $pos; ?>" <?php echo ($this->input->get('position') == $pos) ? 'selected' : ''; ?>>
                                            <?php echo $pos; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Status:</label>
                                <select name="status" class="form-control">
                                    <option value="">Tất cả</option>
                                    <option value="1" <?php echo ($this->input->get('status') == '1') ? 'selected' : ''; ?>>Đã xếp lịch</option>
                                    <option value="2" <?php echo ($this->input->get('status') == '2') ? 'selected' : ''; ?>>Sẵn sàng</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Lọc</button>
                                <a href="<?php echo site_url('admin/staff'); ?>" class="btn btn-secondary">Xóa lọc</a>
                            </div>
                        </div>
                    </form>

                    <!-- Staff Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Bộ phận</th>
                                    <th>Chức vụ</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($staff as $s): ?>
                                    <tr>
                                        <td><?php echo $s->id_staff; ?></td>
                                        <td><?php echo $s->staff_name; ?></td>
                                        <td><?php echo $s->email; ?></td>
                                        <td><?php echo $s->phone; ?></td>
                                        <td><?php echo $s->department ?: 'Chưa Phân Loại'; ?></td>
                                        <td><?php echo $s->position ?: 'Chưa Phân Loại'; ?></td>
                                        <td>
                                            <span class="badge <?php echo ($s->st_status == 1) ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo ($s->st_status == 1) ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/staff/edit/'.$s->id_staff); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="<?php echo site_url('admin/staff/delete/'.$s->id_staff); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>