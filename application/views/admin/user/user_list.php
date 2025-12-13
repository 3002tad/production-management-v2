<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản Lý Người Dùng</h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/user/add'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Thêm Người Dùng
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $statistics['total']; ?></h3>
                                    <p>Tổng Người Dùng</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
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
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo $statistics['inactive']; ?></h3>
                                    <p>Không Hoạt Động</p>
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
                            <div class="col-md-3">
                                <select name="role" class="form-control">
                                    <option value="">Tất cả Vai Trò</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role->role_id; ?>" <?php echo ($this->input->get('role') == $role->role_id) ? 'selected' : ''; ?>>
                                            <?php echo $role->role_name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Tất cả Trạng Thái</option>
                                    <option value="1" <?php echo ($this->input->get('status') == '1') ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($this->input->get('status') == '0') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm username, email, tên nhân viên..." value="<?php echo $this->input->get('search'); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">Tìm Kiếm</button>
                            </div>
                        </div>
                    </form>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Tên Nhân Viên</th>
                                    <th>Vai Trò</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user->user_id; ?></td>
                                        <td><?php echo $user->username; ?></td>
                                        <td><?php echo $user->email; ?></td>
                                        <td><?php echo $user->staff_name ?: 'N/A'; ?></td>
                                        <td><?php echo $user->role_name; ?></td>
                                        <td>
                                            <span class="badge <?php echo ($user->status == 1) ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo ($user->status == 1) ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($user->created_at)); ?></td>
                                        <td>
                                            <a href="<?php echo site_url('admin/user/edit/'.$user->user_id); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="<?php echo site_url('admin/user/toggle_status/'.$user->user_id); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-toggle-on"></i> Toggle
                                            </a>
                                            <a href="<?php echo site_url('admin/user/delete/'.$user->user_id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa user này?')">
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