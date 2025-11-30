<div class="row pr-2">
    <div class="col-12">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-support-16"></i></span>
                <span class="alert-text"><?= $this->session->flashdata('error'); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (validation_errors()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-support-16"></i></span>
                <span class="alert-text"><?= validation_errors(); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card my-2">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-danger shadow-danger border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">add_circle</i>
                        Tạo báo cáo sự cố mới
                    </h6>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <form action="<?= site_url('uc15_qlns/uc15_bcsc/store'); ?>" method="post" enctype="multipart/form-data" class="px-4 py-3">
                    <!-- Dây chuyền & Máy -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Mã dây chuyền <span class="text-danger">*</span></label>
                            <select name="id_planshift" class="form-control" required>
                                <option value="">-- Chọn dây chuyền --</option>
                                <?php foreach ($plan_shifts as $ps): ?>
                                    <option value="<?= $ps->id_planshift; ?>"><?= $ps->id_planshift; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tên máy / Mã máy <span class="text-danger">*</span></label>
                            <select name="id_machine" class="form-control" required id="machine_select">
                                <option value="">-- Chọn máy --</option>
                                <?php foreach ($machines as $m): ?>
                                    <option value="<?= $m->id_machine; ?>"><?= $m->machine_name; ?> (<?= $m->id_machine; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Mô tả sự cố -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Mô tả chi tiết sự cố <span class="text-danger">*</span></label>
                            <textarea name="incident_description" class="form-control" rows="4" required placeholder="Ghi rõ chi tiết sự cố, vị trí, thời gian..."></textarea>
                            <small class="text-muted d-block mt-1">Mô tả chi tiết giúp kỹ sư nhanh chóng xác định và xử lý sự cố</small>
                        </div>
                    </div>

                    <!-- Ảnh/Video -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Ảnh/Video (tùy chọn)</label>
                            <div class="input-group">
                                <input type="file" name="media" class="form-control" accept="image/*,video/*" id="media_file">
                                <span class="input-group-text">
                                    <i class="material-icons">image</i>
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1">Hỗ trợ: jpg, jpeg, png, gif, mp4, avi, mov, mkv (Tối đa 51MB)</small>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="0">Chưa hoàn thành</option>
                                <option value="1">Đã hoàn thành</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="<?= site_url('uc15_qlns/uc15_bcsc'); ?>" class="btn btn-secondary">
                                    <i class="material-icons text-sm me-2">arrow_back</i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="material-icons text-sm me-2">save</i>Tạo báo cáo
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
