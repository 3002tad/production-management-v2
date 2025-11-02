<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách Nhân viên</h6>
                <a href="<?= base_url('hr/add') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm Nhân viên
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã nhân viên</th>
                            <th>Họ và Tên</th>
                            <th>Tên đăng nhập</th>
                            <th>Chức vụ</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staff_list as $staff): ?>
                            <tr>
                                <td><?= $staff->staff_id ?></td>
                                <td><?= $staff->full_name ?></td>
                                <td><?= $staff->username ?></td>
                                <td>
                                    <?php 
                                    foreach ($roles as $role) {
                                        if ($role->role_id == $staff->role_id) {
                                            echo $role->role_display_name;
                                            break;
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?= $staff->email ?></td>
                                <td><?= $staff->phone ?></td>
                                <td>
                                    <a href="<?= base_url('hr/toggle_status/'.$staff->id) ?>" 
                                       class="btn btn-sm <?= $staff->is_active ? 'btn-success' : 'btn-secondary' ?>">
                                        <?= $staff->is_active ? 'Đang hoạt động' : 'Đã khóa' ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= base_url('hr/view/'.$staff->staff_id) ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('hr/edit/'.$staff->staff_id) ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('hr/delete/'.$staff->staff_id) ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?')">
                                        <i class="fas fa-trash"></i>
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