<!-- UC6 - Danh sách Người dùng -->
    <div class="container-fluid py-4">
<!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                <li class="breadcrumb-item text-sm">
<a class="opacity-5 text-dark" href="<?= base_url('admin/') ?>">
                    <i class="material-icons-round text-sm">home</i>
</a>
</li>
                <li class="breadcrumb-item text-sm active" aria-current="page">Quản lý Người dùng</li>
            </ol>
            <h6 class="font-weight-bolder mb-3">Quản lý Người dùng</h6>
        </nav>
    
    <!-- Statistics Cards with Material Design -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons-round opacity-10">group</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Tổng Người dùng</p>
                        <h4 class="mb-0"><?= $statistics['total_users'] ?></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
        <p class="mb-0"><span class="text-secondary text-sm font-weight-bolder">Tất cả user</span> trong hệ thống</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons-round opacity-10">check_circle</i>
    </div>
<div class="text-end pt-1">
    <p class="text-sm mb-0 text-capitalize">Đang hoạt động</p>
        <h4 class="mb-0 text-success"><?= $statistics['active_users'] ?></h4>
            </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Có thể đăng nhập</span></p>
                </div>
            </div>
            </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons-round opacity-10">lock</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Bị khóa</p>
                        <h4 class="mb-0 text-danger"><?= $statistics['locked_users'] ?></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">Không thể truy cập</span></p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons-round opacity-10">key</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Cần đổi mật khẩu</p>
                        <h4 class="mb-0 text-warning"><?= $statistics['must_change_password'] ?></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-warning text-sm font-weight-bolder">Đổi MK lần đầu</span></p>
            </div>
        </div>
    </div>
</div>

<!-- User List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="d-flex align-items-center justify-content-between px-3">
                            <h6 class="text-white text-capitalize ps-3">Danh sách Người dùng</h6>
                            <a href="<?= base_url('admin/user_add') ?>" class="btn btn-sm bg-white text-primary">
                                <i class="material-icons-round text-sm">add</i> Thêm User
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                        <!-- Filters -->
                <form method="GET" action="<?= base_url('admin/user') ?>" class="row g-3 mb-3 px-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." 
                               value="<?= $this->input->get('search') ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="role_id">
                            <option value="">-- Tất cả vai trò --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role->role_id ?>" <?= $this->input->get('role_id') == $role->role_id ? 'selected' : '' ?>>
<?= $role->role_display_name ?>
</option>
                            <?php endforeach; ?>
                        </select>
</div>
                    <div class="col-md-2">
                        <select class="form-select" name="is_active">
                            <option value="">-- Trạng thái --</option>
                            <option value="1" <?= $this->input->get('is_active') === '1' ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= $this->input->get('is_active') === '0' ? 'selected' : '' ?>>Bị khóa</option>
                        </select>
</div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-magnify me-1"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?= base_url('admin/user') ?>" class="btn btn-secondary w-100">
                            <i class="mdi mdi-refresh me-1"></i> Reset
                        </a>
                    </div>
                    </form>

<!-- DataTable with Material Design -->
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="userTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Username</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Họ tên</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vai trò</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Login cuối</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                        <div class="avatar avatar-sm me-3 bg-gradient-<?= $user->is_active ? 'success' : 'secondary' ?> shadow">
                                        <i class="material-icons-round opacity-10" style="font-size: 1.2rem; line-height: 2.2rem;">person</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm"><?= $user->username ?></h6>
                                            <?php if ($user->must_change_password == 1): ?>
                                                <p class="text-xs text-warning mb-0">
                                                <i class="material-icons-round text-xs">key</i> Đổi MK
                                            </p>
                                            <?php endif; ?>
</div>
                                    </div>
                                        </td>
                                        <td>
                                    <p class="text-xs font-weight-bold mb-0"><?= $user->full_name ?: '<em class="text-secondary">Chưa có</em>' ?></p>
</td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info"><?= $user->role_display_name ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold"><?= $user->email ?: '<em>Chưa có</em>' ?></span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                            <?php if ($user->is_active == 1): ?>
                                                <span class="badge badge-sm bg-gradient-success">
                                        <i class="material-icons-round text-xs">check_circle</i> Hoạt động
                                    </span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-danger">
                                        <i class="material-icons-round text-xs">lock</i> Bị khóa
                                    </span>
                                            <?php endif; ?>
                                        </td>
                                    <td class="align-middle text-center">
                                <?php if ($user->last_login): ?>
                                        <span class="text-secondary text-xs font-weight-bold"><?= date('d/m/Y H:i', strtotime($user->last_login)) ?></span>
                                    <?php else: ?>
                                    <span class="text-secondary text-xs"><em>Chưa login</em></span>
                                <?php endif; ?>
</td>
                                <td class="align-middle">
                                    <a href="<?= base_url('admin/user_detail/'.$user->user_id) ?>" 
                                       class="btn btn-link text-info text-gradient px-2 mb-0" title="Xem chi tiết">
                                        <i class="material-icons-round text-sm me-1">visibility</i> Chi tiết
                                    </a>
                                    <a href="<?= base_url('admin/user_edit/'.$user->user_id) ?>" 
                                       class="btn btn-link text-dark px-2 mb-0" title="Sửa">
                                        <i class="material-icons-round text-sm me-1">edit</i> Sửa
                                    </a>
                                    <button type="button" class="btn btn-link text-warning text-gradient px-2 mb-0" 
                                            onclick="resetPassword(<?= $user->user_id ?>)" title="Reset mật khẩu">
                                        <i class="material-icons-round text-sm me-1">vpn_key</i> Reset
                                    </button>
                                    <button type="button" class="btn btn-link text-<?= $user->is_active ? 'danger' : 'success' ?> text-gradient px-2 mb-0" 
                                            onclick="toggleLock(<?= $user->user_id ?>, <?= $user->is_active ?>)" 
                                            title="<?= $user->is_active ? 'Khóa' : 'Mở khóa' ?>">
                                        <i class="material-icons-round text-sm me-1"><?= $user->is_active ? 'lock' : 'lock_open' ?></i>
                                        <?= $user->is_active ? 'Khóa' : 'Mở' ?>
                                    </button>
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
<?php $this->load->view('admin/user/_user_actions_js'); ?>