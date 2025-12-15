<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_staff'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('breadcrumb_staff'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
            </div>
        </div>
    </nav>
</br>
<div class="container-fluid py-4 pt-0">
    <div class="card-header p-0 w-75 position-fixed mt-n4 mx-2 z-index-2">
        <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
            <div class="col-8 d-flex align-items-center">
            <i class="material-icons pr-3">task</i>
                <h6 class="mb-0"><?= lang('label_data_staff'); ?></h6>
            </div>
            <div class="col-4 text-end">
                <span class="badge bg-warning text-dark">View Only</span>
            </div>
        </div>
    </div>
</div>
<div class="container py-4 pl-5 pr-5">
    <div class="row">
        <div class="col-12 mb-4">
            <!-- Filters -->
            <div class="card">
                <div class="card-body">
                    <form class="row" method="GET" action="<?= site_url('leader/staff'); ?>">
                        <div class="col-md-4">
                            <label class="form-label">Bộ phận:</label>
                            <select name="department" class="form-control">
                                <option value="">Chọn bộ phận</option>
                                <option value="Sản Xuất" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Sản Xuất') ? 'selected' : ''; ?>>Sản Xuất</option>
                                <option value="IT" <?php echo (isset($_GET['department']) && $_GET['department'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                                <option value="Kho" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Kho') ? 'selected' : ''; ?>>Kho</option>
                                <option value="QC" <?php echo (isset($_GET['department']) && $_GET['department'] == 'QC') ? 'selected' : ''; ?>>QC</option>
                                <option value="Kỹ Thuật" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Kỹ Thuật') ? 'selected' : ''; ?>>Kỹ Thuật</option>
                                <option value="Ban Giám Đốc" <?php echo (isset($_GET['department']) && $_GET['department'] == 'Ban Giám Đốc') ? 'selected' : ''; ?>>Ban Giám Đốc</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chức vụ:</label>
                            <select name="position" class="form-control">
                                <option value="">Chọn chức vụ</option>
                                <option value="Giám Đốc" <?php echo (isset($_GET['position']) && $_GET['position'] == 'Giám Đốc') ? 'selected' : ''; ?>>Giám Đốc</option>
                                <option value="Trưởng Phòng" <?php echo (isset($_GET['position']) && $_GET['position'] == 'Trưởng Phòng') ? 'selected' : ''; ?>>Trưởng Phòng</option>
                                <option value="Trưởng Dây Chuyền" <?php echo (isset($_GET['position']) && $_GET['position'] == 'Trưởng Dây Chuyền') ? 'selected' : ''; ?>>Trưởng Dây Chuyền</option>
                                <option value="Nhân Viên Kho" <?php echo (isset($_GET['position']) && $_GET['position'] == 'Nhân Viên Kho') ? 'selected' : ''; ?>>Nhân Viên Kho</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status:</label>
                            <select name="status" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                                <option value="2" <?php echo (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Tìm theo mã NV:</label>
                            <input type="text" name="search_code" class="form-control" placeholder="Nhập mã nhân viên" value="<?php echo isset($_GET['search_code']) ? $_GET['search_code'] : ''; ?>">
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                            <a href="<?= site_url('leader/staff'); ?>" class="btn btn-secondary">Xóa lọc</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card">
        <div class="card-body pt-4 p-3">
            <div class="table-responsive p-0">
                <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_staff_name'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Bộ phận</th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Chức vụ</th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_phone'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_email'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_status'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($staff)) : $i = 1; foreach ($staff as $value) : ?>
                        <tr>
                        <td>
                        <div class="d-flex pl-3">
                            <div class="my-auto">
                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->staff_name; ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->department_name ?: $value->department; ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->position; ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->phone; ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->email; ?></span>
                        </td>
                        <td class="pl-4">
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
                        <span class="text-sm font-weight-bold"><?= $status_text; ?></span>
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
