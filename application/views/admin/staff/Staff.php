<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="<?= site_url('admin/'); ?>">
                            <i class="material-icons-round text-sm">home</i>
                        </a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_staff'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Quản lý Nhân Viên</h6>
            </nav>
        </div>
    </nav>

<div class="container-fluid py-4">
    <!-- Statistics Cards with Material Design -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons-round opacity-10">groups</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Tổng Nhân Viên</p>
                        <h4 class="mb-0"><?php echo isset($statistics['total']) ? $statistics['total'] : 0; ?></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-secondary text-sm font-weight-bolder">Tất cả nhân viên</span> trong hệ thống</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons-round opacity-10">verified</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Đang hoạt động</p>
                        <h4 class="mb-0 text-success"><?php echo isset($statistics['active']) ? $statistics['active'] : 0; ?></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Có thể làm việc</span></p>
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
                        <p class="text-sm mb-0 text-capitalize">Không tài khoản</p>
                        <h4 class="mb-0 text-danger"><?php echo isset($statistics['without_user']) ? $statistics['without_user'] : 0; ?></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">Chưa có user</span></p>
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
                        <p class="text-sm mb-0 text-capitalize">Có tài khoản</p>
                        <h4 class="mb-0 text-warning"><?php echo isset($statistics['with_user']) ? $statistics['with_user'] : 0; ?></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-warning text-sm font-weight-bolder">Có user</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between px-3">
                            <h6 class="text-white text-capitalize ps-3">Danh sách Nhân viên</h6>
                            <a href="<?= site_url('admin/staff/addstaff'); ?>" class="btn btn-sm bg-white text-primary">
                                <i class="material-icons-round text-sm">add</i> Thêm Nhân Viên
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <!-- Filters -->
                    <form method="GET" action="<?= site_url('admin/staff'); ?>" class="row g-3 mb-3 px-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search_code" placeholder="Tìm kiếm..." 
                                   value="<?php echo isset($_GET['search_code']) ? $_GET['search_code'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="department">
                                <option value="">-- Chọn bộ phận --</option>
                                <option value="Sản Xuất" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Sản Xuất') ? 'selected' : ''; ?>>Sản Xuất</option>
                                <option value="IT" <?php echo (isset($_GET['department']) && $_GET['department'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                                <option value="Kho" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Kho') ? 'selected' : ''; ?>>Kho</option>
                                <option value="QC" <?php echo (isset($_GET['department']) && $_GET['department'] == 'QC') ? 'selected' : ''; ?>>QC</option>
                                <option value="Kỹ Thuật" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Kỹ Thuật') ? 'selected' : ''; ?>>Kỹ Thuật</option>
                                <option value="Ban Giám Đốc" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Ban Giám Đốc') ? 'selected' : ''; ?>>Ban Giám Đốc</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">-- Trạng thái --</option>
                                <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                                <option value="2" <?php echo (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="material-icons-round text-sm">search</i> Lọc
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?= site_url('admin/staff'); ?>" class="btn btn-secondary w-100">
                                <i class="material-icons-round text-sm">refresh</i> Reset
                            </a>
                        </div>
                    </form>

                    <?php if ($this->session->flashdata('flash')): ?>
                        <div class="alert alert-success mx-3">
                            <?= $this->session->flashdata('flash'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger mx-3">
                            <?= $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive p-0">
                        <table id="table-data" class="table align-items-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-center">STT</th>
                        <th class="text-center">Thao tác</th>
                        <th>Tên nhân viên</th>
                        <th>Bộ phận</th>
                        <th>Chức vụ</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($staff)) : $i = 1; foreach ($staff as $value) : ?>
                        <tr>
                        <td class="text-center">
                            <?= $i++; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= site_url('admin/staff/'.$value->id_staff.'/update'); ?>" class="btn btn-sm btn-warning" title="Sửa">
                                <span class="material-icons-round" style="font-size:16px;">edit</span>
                            </a>
                            <a href="<?= site_url('admin/deleteStaff/'.$value->id_staff); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?');" title="Xóa">
                                <span class="material-icons-round" style="font-size:16px;">delete</span>
                            </a>
                        </td>
                        <td>
                            <?= $value->staff_name; ?>
                        </td>
                        <td>
                            <?= !empty($value->department_name) ? $value->department_name : (!empty($value->department) ? $value->department : 'Chưa Phân Loại'); ?>
                        </td>
                        <td>
                            <?= !empty($value->position) ? $value->position : 'Chưa Phân Loại'; ?>
                        </td>
                        <td>
                            <?= $value->phone; ?>
                        </td>
                        <td>
                            <?= $value->email; ?>
                        </td>
                        <td>
                        <?php
                            if ($value->st_status == 1) {
                                $status_text = 'Sẵn sàng';
                            } elseif ($value->st_status == 2) {
                                $status_text = 'Đã xếp lịch';
                            } elseif ($value->st_status == 3) {
                                $status_text = 'Ngừng hoạt động';
                            } else {
                                $status_text = 'Không rõ';
                            }
                        ?>
                        <?= $status_text; ?>
                        </td>
                        </tr>
                        <?php endforeach; endif; ?>

                    </tbody>
                    </table>
            </div>
        </div>
        </div>
    </div>
</div>