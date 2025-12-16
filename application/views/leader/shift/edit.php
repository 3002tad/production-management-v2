<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/shift/'); ?>">Ca làm việc</a></li>
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/shift/detail/' . $shift->shift_id); ?>"><?= $shift->shift_name ?></a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Chỉnh sửa</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Chỉnh sửa Ca làm việc</h6>
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

    <!-- Edit Shift Form -->
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <i class="material-icons text-warning me-2">edit</i>
                        <p class="mb-0">Chỉnh sửa thông tin ca làm việc</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('leader/shift/update/' . $shift->shift_id); ?>" id="editShiftForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Tên ca *</label>
                                    <input type="text" class="form-control" name="shift_name" required 
                                           value="<?= $shift->shift_name ?>" placeholder="VD: Ca sáng, Ca chiều">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Dây chuyền *</label>
                                    <select class="form-control" name="line_id" required>
                                        <option value="">-- Chọn dây chuyền --</option>
                                        <?php foreach ($lines as $line): ?>
                                            <option value="<?= $line->id ?>" <?= ($shift->line_id == $line->id) ? 'selected' : '' ?>>
                                                <?= $line->line_code ?> - <?= $line->line_name ?>
                                                <?php if (!empty($line->zone_name)): ?>
                                                    (<?= $line->zone_name ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Ngày làm việc *</label>
                                    <input type="date" class="form-control" name="shift_date" required 
                                           value="<?= $shift->shift_date ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Giờ bắt đầu *</label>
                                    <input type="time" class="form-control" name="start_time" required
                                           value="<?= $shift->start_time ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Giờ kết thúc *</label>
                                    <input type="time" class="form-control" name="end_time" required
                                           value="<?= $shift->end_time ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Sản lượng mục tiêu *</label>
                                    <input type="number" class="form-control" name="target_quantity" required 
                                           min="0" value="<?= $shift->target_quantity ?>" placeholder="VD: 1000">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-group input-group-static mb-4">
                                    <label>Ghi chú</label>
                                    <textarea class="form-control" name="notes" rows="3" 
                                              placeholder="Ghi chú thêm về ca làm việc..."><?= $shift->notes ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= site_url('leader/shift/detail/' . $shift->shift_id); ?>" class="btn btn-light m-0">
                                <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary m-0">
                                <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Cập nhật Ca
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
document.getElementById('editShiftForm').addEventListener('submit', function(e) {
    const startTime = document.querySelector('[name="start_time"]').value;
    const endTime = document.querySelector('[name="end_time"]').value;

    if (startTime && endTime && startTime >= endTime) {
        e.preventDefault();
        alert('Giờ kết thúc phải sau giờ bắt đầu');
        return false;
    }
});
</script>
