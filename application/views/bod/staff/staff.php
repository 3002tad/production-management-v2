<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="<?= site_url('BOD/'); ?>">
                        <i class="material-icons-round text-sm">home</i>
                    </a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Nhân viên</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Danh sách Nhân viên</h6>
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
                            <span class="badge bg-warning text-dark">View Only</span>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <!-- Filters -->
                    <form method="GET" action="<?= site_url('BOD/staff'); ?>" class="row g-3 mb-3 px-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search_code" placeholder="Tìm kiếm..." 
                                   value="<?php echo isset($_GET['search_code']) ? $_GET['search_code'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="department">
                                <option value="">-- Chọn bộ phận --</option>
                                <?php foreach($departments as $id => $label): ?>
                                    <option value="<?= $id ?>" <?= (isset($_GET['department']) && $_GET['department'] == $id) ? 'selected' : ''; ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">-- Trạng thái --</option>
                                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Sẵn sàng</option>
                                <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : ''; ?>>Đã xếp lịch</option>
                                <option value="3" <?= (isset($_GET['status']) && $_GET['status'] == '3') ? 'selected' : ''; ?>>Ngừng hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="material-icons-round text-sm">search</i> Lọc
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?= site_url('BOD/staff'); ?>" class="btn btn-secondary w-100">
                                <i class="material-icons-round text-sm">refresh</i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive p-0">
                        <table id="table-data" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">STT</th>
                                    <th>Tên nhân viên</th>
                                    <th>Bộ phận</th>
                                    <th>Chức vụ</th>
                                    <th>Số điện thoại</th>
                                    <th>Email</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($staff)) : $i = 1; foreach ($staff as $value) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?= $i++; ?>
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
</div>