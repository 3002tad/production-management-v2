<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_staff'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('label_update_staff'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
            </div>
        </div>
    </nav>
</br>
<div class="d-flex justify-content-center">
    <div class="col-lg-10 col-md-12">
        <div class="card">
        <div class="card-header card-header-primary">
            <div class="row">
                <div class="col-7 align-items-center pl-4">
                    <h4 class="mb-0"><?= lang('title_update_staff_data'); ?></h4>
                    <span class="text-sm mb-0 text-end"><?= lang('subtitle_update_data'); ?></span>
                </div>
            </div>
            <div class="d-flex pt-4" method="post">
                <div class="col-8">
                    <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
                    <?php endif; ?>
                    <form class="pt-4" action="<?= site_url('admin/updatestaff'); ?>" method="post">
                        <span><?= lang('table_staff_name'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="hidden" name="id_staff" value="<?= $detail['id_staff']; ?>">
                            <input type="text" name="staff_name" value="<?= $detail['staff_name']; ?>" class="form-control">                  
                        </div>

                        <span>Bộ phận:</span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <select name="department" class="form-control">
                                <option value="">Chọn bộ phận</option>
                                <option value="Sản Xuất" <?= ($detail['department'] == 'Sản Xuất') ? 'selected' : ''; ?>>Sản Xuất</option>
                                <option value="IT" <?= ($detail['department'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                                <option value="Kho" <?= ($detail['department'] == 'Kho') ? 'selected' : ''; ?>>Kho</option>
                                <option value="QC" <?= ($detail['department'] == 'QC') ? 'selected' : ''; ?>>QC</option>
                                <option value="Kỹ Thuật" <?= ($detail['department'] == 'Kỹ Thuật') ? 'selected' : ''; ?>>Kỹ Thuật</option>
                                <option value="Ban Giám Đốc" <?= ($detail['department'] == 'Ban Giám Đốc') ? 'selected' : ''; ?>>Ban Giám Đốc</option>
                            </select>
                        </div>

                        <span>Chức vụ:</span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <select name="position" class="form-control">
                                <option value="">Chọn chức vụ</option>
                                <option value="Giám Đốc" <?= ($detail['position'] == 'Giám Đốc') ? 'selected' : ''; ?>>Giám Đốc</option>
                                <option value="Trưởng Phòng" <?= ($detail['position'] == 'Trưởng Phòng') ? 'selected' : ''; ?>>Trưởng Phòng</option>
                                <option value="Trưởng Dây Chuyền" <?= ($detail['position'] == 'Trưởng Dây Chuyền') ? 'selected' : ''; ?>>Trưởng Dây Chuyền</option>
                                <option value="Nhân Viên Kho" <?= ($detail['position'] == 'Nhân Viên Kho') ? 'selected' : ''; ?>>Nhân Viên Kho</option>
                            </select>
                        </div>

                        <div class="row d-flex">
                            <div class="col-4">
                                <span><?= lang('label_phone_number'); ?></span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <label class="form-label"></label>
                                    <input type="tel" name="phone" value="<?= $detail['phone']; ?>" class="form-control" required pattern="^0\d{9}$" minlength="10" maxlength="10" title="Số điện thoại gồm 10 chữ số và bắt đầu bằng 0">
                                </div>
                            </div>
                            <div class="col-1">
                            </div>
                            <div class="col-7">
                            <span><?= lang('table_email'); ?></span></br>
                            <div class="input-group input-group-dynamic mb-4">
                                <label class="form-label"></label>
                                <input type="email" name="email" value="<?= $detail['email']; ?>" class="form-control" required pattern="^[^@\s]+@mail\.com$" title="Email phải kết thúc bằng @mail.com">
                            </div>
                            </div>
                        </div>
                        <?php if (!isset($is_read_only) || !$is_read_only): ?>
                            <div class="row d-flex">
                                <div class="col-6">
                                    <span><?= lang('table_status'); ?></span></br>
                                    <div class="input-group input-group-dynamic mb-4">
                                        <label class="form-label"></label>
                                        <select name="st_status" class="form-control" required>
                                            <option value="1" <?= ($detail['st_status'] == 1) ? 'selected' : ''; ?>>Sẵn sàng</option>
                                            <option value="2" <?= ($detail['st_status'] == 2) ? 'selected' : ''; ?>>Đã xếp lịch</option>
                                            <option value="3" <?= ($detail['st_status'] == 3) ? 'selected' : ''; ?>>Ngừng hoạt động</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        

                        

                    </div>
                </div>
                <div class="col-4">
                    <div class="pr-2">
                        <span><?= lang('msg_confirm_update_data'); ?></span></br>
                    </div>
                    <div class="d-flex">
                        <div class="pt-2 pl-2">
                            <a class="btn btn-outline-dark btn-sm mb-0" href="<?= site_url('admin/staff'); ?>"><?= lang('btn_back'); ?></a>
                        </div>
                        <div class="pt-2 pl-2">
                            <button class="btn btn-dark btn-sm mb-0" type="submit"><?= lang('btn_save'); ?></button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
        </div>
    </div>
<div>