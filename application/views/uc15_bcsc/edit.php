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
                <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">edit</i>
                        Chỉnh sửa báo cáo sự cố
                    </h6>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <?php $controller_segment = $this->uri->segment(2) ?: 'uc15_bcsc'; ?>
                <form action="<?= site_url('uc15_bcsc/' . $controller_segment . '/update/' . $incident->id); ?>" method="post" enctype="multipart/form-data" class="px-4 py-3">
                    <!-- Dây chuyền & Máy -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Mã dây chuyền (tùy chọn)</label>
                            <select name="id_planshift" class="form-control">
                                <option value="">-- Chọn dây chuyền --</option>
                                <?php foreach ($plan_shifts as $ps): ?>
                                    <option value="<?= $ps->id_planshift; ?>" <?= ($ps->id_planshift == $incident->id_planshift) ? 'selected' : ''; ?>>
                                        <?= $ps->id_planshift; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tên máy / Mã máy <span class="text-danger">*</span></label>
                            <select name="id_machine" class="form-control" required id="machine_select">
                                <?php foreach ($machines as $m): ?>
                                    <option value="<?= $m->id_machine; ?>" <?= ($m->id_machine == $incident->id_machine) ? 'selected' : ''; ?>>
                                        <?= $m->machine_name; ?> (<?= $m->id_machine; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Loại & Mức độ sự cố -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại sự cố <span class="text-danger">*</span></label>
                            <select name="category" class="form-control" required>
                                <option value="">-- Chọn loại sự cố --</option>
                                <option value="equipment" <?= ($incident->category == 'equipment') ? 'selected' : ''; ?>>Thiết bị/Máy móc</option>
                                <option value="quality" <?= ($incident->category == 'quality') ? 'selected' : ''; ?>>Chất lượng sản phẩm</option>
                                <option value="safety" <?= ($incident->category == 'safety') ? 'selected' : ''; ?>>An toàn lao động</option>
                                <option value="other" <?= ($incident->category == 'other') ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mức độ nghiêm trọng <span class="text-danger">*</span></label>
                            <select name="severity_level" class="form-control" required>
                                <option value="">-- Chọn mức độ --</option>
                                <option value="1" <?= ($incident->severity_level == 1) ? 'selected' : ''; ?>>🟢 Thấp - Low</option>
                                <option value="2" <?= ($incident->severity_level == 2) ? 'selected' : ''; ?>>🟡 Trung bình - Medium</option>
                                <option value="3" <?= ($incident->severity_level == 3) ? 'selected' : ''; ?>>🔴 Cao - High</option>
                                <option value="4" <?= ($incident->severity_level == 4) ? 'selected' : ''; ?>>⚫ Nghiêm trọng - Critical</option>
                            </select>
                        </div>
                    </div>

                    <!-- Mô tả sự cố -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Mô tả chi tiết sự cố <span class="text-danger">*</span></label>
                            <textarea name="incident_description" class="form-control" rows="4" required placeholder="Ghi rõ chi tiết sự cố, vị trí, thời gian..."><?= $incident->incident_description; ?></textarea>
                            <small class="text-muted d-block mt-1">Mô tả chi tiết giúp kỹ sư nhanh chóng xác định và xử lý sự cố</small>
                        </div>
                    </div>

                    <!-- Ảnh/Video -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Ảnh/Video (tùy chọn)</label>
                            <?php if (!empty($incident->media_path) && file_exists($incident->media_path)): ?>
                                <div class="alert alert-info mb-3">
                                    <i class="material-icons text-sm me-2">info</i>
                                    <span>Tệp hiện tại: <a href="<?= site_url($incident->media_path); ?>" target="_blank" class="text-dark font-weight-bold"><?= basename($incident->media_path); ?></a></span>
                                </div>
                            <?php endif; ?>
                            <div class="input-group">
                                <input type="file" name="media" class="form-control" accept="image/*,video/*" id="media_file">
                                <span class="input-group-text">
                                    <i class="material-icons">image</i>
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1">Nếu muốn thay thế, chọn tệp mới. Để trống nếu giữ nguyên.</small>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="0" <?= ($incident->status == 0) ? 'selected' : ''; ?>>⏸ Chờ xử lý - Pending</option>
                                <option value="2" <?= ($incident->status == 2) ? 'selected' : ''; ?>>⏳ Đang xử lý - In Progress</option>
                                <option value="1" <?= ($incident->status == 1) ? 'selected' : ''; ?>>✓ Đã hoàn thành - Completed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="<?= site_url('uc15_bcsc/' . $controller_segment); ?>" class="btn btn-secondary">
                                    <i class="material-icons text-sm me-2">arrow_back</i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="material-icons text-sm me-2">update</i>Cập nhật
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
