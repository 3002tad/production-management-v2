<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân sự</h6>
            <a href="<?= base_url('personnel/add') ?>" class="btn btn-primary float-right">Thêm nhân sự mới</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Chức vụ</th>
                            <th>Phòng ban</th>
                            <th>Điện thoại</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($personnel_list as $person): ?>
                        <tr>
                            <td><?= $person->id ?></td>
                            <td><?= $person->name ?></td>
                            <td><?= $person->position ?></td>
                            <td><?= $person->department ?></td>
                            <td><?= $person->phone ?></td>
                            <td><?= $person->email ?></td>
                            <td><?= $person->status ?></td>
                            <td>
                                <a href="<?= base_url('personnel/edit/'.$person->id) ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="<?= base_url('personnel/delete/'.$person->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>