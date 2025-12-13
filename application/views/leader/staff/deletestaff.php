<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_staff'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Xóa nhân sự</h6>
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
        <div class="card-header card-header-danger">
            <div class="row">
                <div class="col-7 align-items-center pl-4">
                    <h4 class="mb-0">Xác nhận xóa nhân sự</h4>
                    <span class="text-sm mb-0 text-end">Hành động này không thể hoàn tác</span>
                </div>
            </div>
            <div class="d-flex pt-4" method="post">
                <div class="col-8">
                    <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
                        <div class="alert alert-danger" role="alert">
                            <strong>Cảnh báo!</strong> Bạn sắp xóa nhân sự sau đây:
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Mã nhân sự:</label>
                            <p class="text-danger"><?= $detail['id_staff']; ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Tên nhân sự:</label>
                            <p class="text-danger"><?= $detail['staff_name']; ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Số điện thoại:</label>
                            <p><?= $detail['phone']; ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Email:</label>
                            <p><?= $detail['email']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="pr-2">
                        <span>Nhấn "Xóa" để xác nhận xóa</span></br>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a class="btn btn-outline-dark btn-sm mb-2" href="<?= site_url('leader/staff'); ?>"><?= lang('btn_back'); ?></a>
                        <form action="<?= site_url('leader/deleteStaff/'.$detail['id_staff']); ?>" method="POST" style="display:inline;">
                            <button class="btn btn-danger btn-sm mb-0 w-100" type="submit" onclick="return confirm('Xác nhận xóa nhân sự này?')">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
<div>
