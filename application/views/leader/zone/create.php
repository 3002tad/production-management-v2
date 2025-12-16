<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/zone/'); ?>">Khu vực</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Thêm mới</li>
            </ol>
            <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
        </nav>
    </div>
</nav>

<div class="container-fluid py-4">
    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="material-icons">error</i></span>
        <span class="alert-text"><?= $this->session->flashdata('error') ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Create Zone Form -->
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <i class="material-icons text-primary me-2">domain</i>
                        <p class="mb-0">Điền thông tin khu vực mới</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('leader/zone/store'); ?>" id="createZoneForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Mã khu *</label>
                                    <input type="text" class="form-control" name="zone_code" required maxlength="50" 
                                           placeholder="VD: ZONE_A, ZONE_B">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Tên khu vực *</label>
                                    <input type="text" class="form-control" name="zone_name" required maxlength="100"
                                           placeholder="VD: Khu A, Khu sản xuất chính">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Tầng</label>
                                    <input type="text" class="form-control" name="floor" maxlength="50"
                                           placeholder="VD: Tầng 1, Tầng 2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Tòa nhà</label>
                                    <input type="text" class="form-control" name="building" maxlength="50"
                                           placeholder="VD: Nhà máy A, Nhà máy B">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group input-group-static mb-4">
                                    <label>Mô tả</label>
                                    <textarea class="form-control" name="description" rows="3"
                                              placeholder="Mô tả chi tiết về khu vực..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="status">
                                        <option value="1" selected>Hoạt động</option>
                                        <option value="0">Ngừng hoạt động</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= site_url('leader/zone/'); ?>" class="btn btn-light m-0 me-2">
                                <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary m-0">
                                <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Lưu Khu vực
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
document.getElementById('createZoneForm').addEventListener('submit', function(e) {
    const zoneCode = this.zone_code.value.trim();
    const zoneName = this.zone_name.value.trim();

    if (!zoneCode || !zoneName) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin bắt buộc (*)');
        return false;
    }

    // Validate zone code format (letters, numbers, underscore, dash)
    if (!/^[A-Z0-9_-]+$/i.test(zoneCode)) {
        e.preventDefault();
        alert('Mã khu chỉ được chứa chữ cái, số, dấu gạch dưới và dấu gạch ngang');
        return false;
    }
});
</script>
