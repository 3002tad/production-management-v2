<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="<?= base_url('admin/') ?>">
                            <i class="material-icons text-sm">home</i>
                        </a>
                    </li>
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="<?= base_url('admin/user') ?>">Người dùng</a>
                    </li>
                    <li class="breadcrumb-item text-sm active" aria-current="page">Sửa quyền: <?= $user->username ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Sửa Quyền Người dùng</h6>
            </nav>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header p-3 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="material-icons opacity-10" style="font-size: 24px; line-height: 40px;">edit</i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Chỉnh sửa quyền: <?= $user->username ?></h6>
                            <p class="text-sm mb-0">Thay đổi vai trò và quyền truy cập</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form action="<?= base_url('admin/user_edit_process') ?>" method="POST" id="userEditForm" class="multisteps-form__form">
                        <input type="hidden" name="user_id" value="<?= $user->user_id ?>">

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label class="ms-0">Username <span class="text-muted">(Không thể thay đổi)</span></label>
                                    <input type="text" class="form-control" value="<?= $user->username ?>" readonly disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                </div>
<small class="text-muted d-block mt-n2 mb-3">
                                    <i class="material-icons text-xs">info</i> Username không thể thay đổi sau khi tạo
                                </small>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="role_id" class="ms-0">Vai trò <span class="text-danger">*</span></label>
                                    <select class="form-control" id="role_id" name="role_id" required>
                                        <option value="">-- Chọn vai trò --</option>
                                        <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role->role_id ?>" <?= $role->role_id == $user->role_id ? 'selected' : '' ?>>
                                            <?= $role->role_display_name ?> (Level: <?= $role->level ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-gradient-light p-3 mb-3">
                                    <h6 class="text-sm font-weight-bold mb-2">Thông tin nhân viên</h6>
                                    <p class="text-xs mb-1"><strong>Họ tên:</strong> <?= htmlspecialchars($user->staff_name ?? $user->full_name) ?></p>
                                    <p class="text-xs mb-1"><strong>Email:</strong> <?= htmlspecialchars($user->staff_email ?? $user->email) ?></p>
                                    <p class="text-xs mb-0"><strong>SĐT:</strong> <?= htmlspecialchars($user->staff_phone ?? $user->phone) ?></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-gradient-light p-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-sm icon-shape bg-white shadow text-center border-radius-md me-2">
                                            <i class="material-icons text-dark opacity-10" style="font-size: 18px;">info</i>
                                        </div>
                                        <div>
                                            <p class="text-xs mb-0"><strong>Trạng thái:</strong> 
                                                <?php if ($user->is_active == 1): ?>
                                                    <span class="badge badge-sm bg-gradient-success">Hoạt động</span>
                                        <?php else: ?>
                                                    <span class="badge badge-sm bg-gradient-danger">Bị khóa</span>
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-xs mb-0"><strong>Login cuối:</strong> <?= $user->last_login ? date('d/m/Y H:i', strtotime($user->last_login)) : 'Chưa login' ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Boxes - Không hiển thị flashdata error từ trang khác -->

                        <div class="alert alert-info alert-dismissible text-white fade show" role="alert">
                            <span class="alert-icon"><i class="material-icons">info</i></span>
                            <span class="alert-text">
                                <strong>Lưu ý:</strong> 
                                <ul class="mb-0 mt-2">
                                    <li>Chỉ có thể thay đổi vai trò của user</li>
                                    <li>Thông tin nhân viên được quản lý riêng trong module HR</li>
                                    <li>Mật khẩu chỉ được reset bằng nút riêng trong danh sách user</li>
                                    <li>Thay đổi vai trò sẽ ảnh hưởng đến quyền truy cập ngay lập tức</li>
                                </ul>
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <a href="<?= base_url('admin/user') ?>" class="btn btn-outline-secondary mb-0 me-2">
                                    <i class="material-icons text-sm">arrow_back</i> Quay lại
</a>
                                <button type="submit" class="btn bg-gradient-primary mb-0">
                                    <i class="material-icons text-sm">save</i> Lưu thay đổi
</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>