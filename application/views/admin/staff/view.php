<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Thông tin chi tiết nhân viên: <?= $staff->full_name ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Mã nhân viên</th>
                        <td><?= $staff->staff_id ?></td>
                    </tr>
                    <tr>
                        <th>Họ và Tên</th>
                        <td><?= $staff->full_name ?></td>
                    </tr>
                    <tr>
                        <th>Tên đăng nhập</th>
                        <td><?= $staff->username ?></td>
                    </tr>
                    <tr>
                        <th>Chức vụ</th>
                        <td><?= $role->role_display_name ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $staff->email ?></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại</th>
                        <td><?= $staff->phone ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <span class="badge badge-<?= $staff->is_active ? 'success' : 'secondary' ?>">
                                <?= $staff->is_active ? 'Đang hoạt động' : 'Đã khóa' ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= date('d/m/Y H:i:s', strtotime($staff->created_at)) ?></td>
                    </tr>
                    <tr>
                        <th>Cập nhật lần cuối</th>
                        <td><?= $staff->updated_at ? date('d/m/Y H:i:s', strtotime($staff->updated_at)) : 'Chưa cập nhật' ?></td>
                    </tr>
                    <tr>
                        <th>Đăng nhập lần cuối</th>
                        <td><?= $staff->last_login ? date('d/m/Y H:i:s', strtotime($staff->last_login)) : 'Chưa đăng nhập' ?></td>
                    </tr>
                </table>
            </div>

            <div class="mt-3">
                <a href="<?= base_url('hr/edit/'.$staff->id) ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <a href="<?= base_url('hr') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>