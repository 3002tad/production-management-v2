<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?php echo $title; ?></h1>
    
    <div class="container-fluid pt-0 ">
        <div class="card-header p-0 w-75 position-fixed mt-n4 mx-2 z-index-2">
            <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
                <div class="col-8 d-flex align-items-center">
                <i class="material-icons pr-3">people</i>
                    <h6 class="mb-0 pr-4 ">Sửa thông tin nhân viên</h6>
                </div>
                <div class="col-4 text-end">
                    <a href="<?php echo base_url('leader/staff'); ?>" class="btn btn-outline-dark btn-sm mb-0">Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <div class="row">
                        <div class="col-7 align-items-center pl-4">
                            <h4 class="mb-0">Sửa nhân viên</h4>
                            <span class="text-sm mb-0 text-end">Edit staff information</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex pt-4">
                    <div class="col-8">
                        <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
                            <?php if (function_exists('validation_errors') && validation_errors()) { echo validation_errors('<div class="alert alert-danger">', '</div>'); } ?>
                            <?php if (!empty(isset($validation_errors) ? $validation_errors : '')) { echo '<div class="alert alert-danger">'.(isset($validation_errors) ? $validation_errors : '').'</div>'; } ?>
                            <form class="pt-4" action="<?php echo base_url('leader/updateStaff'); ?>" method="post">
                                <input type="hidden" name="id_staff" value="<?php echo isset($staff->id_staff) ? $staff->id_staff : ''; ?>">
                                <span>Tên nhân viên</span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <input type="text" class="form-control" name="staff_name" value="<?php echo isset($staff->staff_name) ? $staff->staff_name : set_value('staff_name'); ?>" required>
                                </div>
                                <span>Số điện thoại</span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <input type="tel" class="form-control" name="phone" value="<?php echo isset($staff->phone) ? $staff->phone : set_value('phone'); ?>">
                                </div>
                                <span>Email</span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <input type="email" class="form-control" name="email" value="<?php echo isset($staff->email) ? $staff->email : set_value('email'); ?>">
                                </div>
                                <span>Trạng thái</span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <select class="form-control" name="st_status">
                                        <option value="1" <?php echo (isset($staff->st_status) && $staff->st_status == 1) ? 'selected' : ''; ?>>Hoạt động</option>
                                        <option value="0" <?php echo (isset($staff->st_status) && $staff->st_status == 0) ? 'selected' : ''; ?>>Ngừng hoạt động</option>
                                    </select>
                                </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="pr-2">
                            <span>Update and save?</span></br>
                        </div>
                        <div class="d-flex">
                            <div class="pt-2 pl-2">
                                <a class="btn btn-outline-dark btn-sm mb-0" href="<?php echo base_url('leader/staff'); ?>">Back</a>
                            </div>
                            <div class="pt-2 pl-2">
                                <button class="btn btn-dark btn-sm mb-0" type="submit">Save</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
