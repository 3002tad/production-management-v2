<!-- UC6 - Chi tiết Người dùng -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Chi tiết Người dùng</h4>
            <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/user') ?>">Người dùng</a></li>
                    <li class="breadcrumb-item active"><?= $user->username ?></li>
                </ol>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
<!-- User Info Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-xl mx-auto mb-3">
                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle" 
                         style="font-size: 3rem; width: 120px; height: 120px; line-height: 120px;">
                        <?= strtoupper(substr($user->username, 0, 2)) ?>
                    </div>
                </div>
                
                <h5 class="mb-1"><?= $user->full_name ?: '<em class="text-muted">Chưa có</em>' ?></h5>
                <p class="text-muted mb-0">@<?= $user->username ?></p>

                    <div class="mt-3">
                        <span class="badge bg-info-subtle text-info fs-6">
                        <?= $user->role_display_name ?>
                    </span>
                            </div>
                
                <div class="mt-3">
                        <?php if ($user->is_active == 1): ?>
                            <span class="badge bg-success-subtle text-success fs-6">
                                <i class="mdi mdi-check-circle"></i> Đang hoạt động
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger fs-6">
                            <i class="mdi mdi-lock"></i> Bị khóa
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($user->must_change_password == 1): ?>
                        <span class="badge bg-warning-subtle text-warning fs-6">
                                <i class="mdi mdi-key-change"></i> Phải đổi MK
                            </span>
                        <?php endif; ?>
                    </div>

                <hr class="my-3">

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/user_edit/'.$user->user_id) ?>" 
                       class="btn btn-primary">
                        <i class="mdi mdi-pencil me-1"></i> Sửa thông tin
                    </a>
                    
                    <button type="button" class="btn btn-warning" 
                            onclick="resetPassword(<?= $user->user_id ?>)">
                        <i class="mdi mdi-key-variant me-1"></i> Reset mật khẩu
                    </button>
                    
                    <button type="button" class="btn btn-<?= $user->is_active ? 'danger' : 'success' ?>" 
                            onclick="toggleLock(<?= $user->user_id ?>, <?= $user->is_active ?>)">
                        <i class="mdi mdi-<?= $user->is_active ? 'lock' : 'lock-open' ?> me-1"></i> 
                        <?= $user->is_active ? 'Khóa tài khoản' : 'Mở khóa' ?>
                    </button>
                    
                    <a href="<?= base_url('admin/user') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
                </div>
            </div>
        </div>

<!-- User Details & Audit Log -->
        <div class="col-lg-8">
            <!-- Details Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-account-details me-2"></i> Thông tin chi tiết
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 30%;">Username:</td>
                                <td><?= $user->username ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Họ và tên:</td>
                                <td><?= $user->full_name ?: '<em class="text-muted">Chưa có</em>' ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Email:</td>
                                <td>
                                    <?php if ($user->email): ?>
                                        <a href="mailto:<?= $user->email ?>"><?= $user->email ?></a>
                                    <?php else: ?>
                                        <em class="text-muted">Chưa có</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Số điện thoại:</td>
                                <td>
                                    <?php if ($user->phone): ?>
                                        <a href="tel:<?= $user->phone ?>"><?= $user->phone ?></a>
                                    <?php else: ?>
                                        <em class="text-muted">Chưa có</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Vai trò:</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        <?= $user->role_display_name ?> (Level: <?= $user->role_level ?>)
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Trạng thái:</td>
                                <td>
                                    <?php if ($user->is_active == 1): ?>
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Bị khóa</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Login cuối:</td>
                                <td>
                                    <?= $user->last_login ? date('d/m/Y H:i:s', strtotime($user->last_login)) : '<em class="text-muted">Chưa login</em>' ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tạo lúc:</td>
                                <td><?= date('d/m/Y H:i:s', strtotime($user->created_at)) ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Cập nhật lần cuối:</td>
                                <td>
                                    <?= $user->updated_at ? date('d/m/Y H:i:s', strtotime($user->updated_at)) : '<em class="text-muted">Chưa cập nhật</em>' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
        </div>

        <?php $this->load->view('admin/user/_user_actions_js'); ?>

        <!-- Audit Log Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-history me-2"></i> Lịch sử thay đổi (50 bản ghi gần nhất)
                </h5>
            </div>
            <div class="card-body">
                    <?php if (empty($audit_logs)): ?>
<div class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline" style="font-size: 3rem;"></i>
                        <p class="mt-2">Chưa có lịch sử thay đổi nào.</p>
                    </div>
                <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thời gian</th>
                                                                                <th>Hành động</th>
                                        <th>Chi tiết</th>
<th>Thực hiện bởi</th>
                                    <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($audit_logs as $log): ?>
                                        <tr>
                                            <td style="white-space: nowrap;">
                                        <small><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></small>
</td>
                                            <td>
                                        <?php
                                        $action_badges = [
                                            'create' => ['bg' => 'success', 'icon' => 'plus', 'text' => 'Tạo mới'],
                                            'update' => ['bg' => 'primary', 'icon' => 'pencil', 'text' => 'Cập nhật'],
                                            'lock' => ['bg' => 'danger', 'icon' => 'lock', 'text' => 'Khóa'],
                                            'unlock' => ['bg' => 'success', 'icon' => 'lock-open', 'text' => 'Mở khóa'],
                                            'reset_password' => ['bg' => 'warning', 'icon' => 'key-variant', 'text' => 'Reset MK']
                                        ];
                                        $badge = $action_badges[$log->action] ?? ['bg' => 'secondary', 'icon' => 'circle', 'text' => ucfirst($log->action)];
                                        ?>
                                        <span class="badge bg-<?= $badge['bg'] ?>-subtle text-<?= $badge['bg'] ?>">
                                            <i class="mdi mdi-<?= $badge['icon'] ?>"></i> <?= $badge['text'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($log->old_value || $log->new_value): ?>
                                            <small>
                                                <?php
                                                $old = json_decode($log->old_value, true);
                                                $new = json_decode($log->new_value, true);
                                                
                                                if ($old && $new) {
                                                    foreach ($new as $key => $val) {
                                                        if (isset($old[$key]) && $old[$key] != $val) {
                                                            echo "<strong>$key:</strong> " . htmlspecialchars($old[$key]) . " → " . htmlspecialchars($val) . "<br>";
                                                        }
                                                    }
                                                } elseif ($new) {
                                                    echo "Dữ liệu mới: " . htmlspecialchars(json_encode($new, JSON_UNESCAPED_UNICODE));
                                                } else {
                                                    echo '<em class="text-muted">Không có chi tiết</em>';
                                                }
                                                ?>
                                            </small>
                                        <?php else: ?>
                                            <em class="text-muted">-</em>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $log->username ?></td>
                                    <td><small><?= $log->ip_address ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                                        <?php endif; ?>
                            </div>
        </div>
    </div>
</div>