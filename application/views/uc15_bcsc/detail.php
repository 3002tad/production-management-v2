<div class="row pr-2">
    <div class="col-12">
        <div class="card my-2">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-5">
                    <h6 class="text-white text-capitalize mb-0">
                        <i class="material-icons opacity-10">info</i>
                        Chi tiết báo cáo sự cố
                    </h6>
                    <div class="d-flex gap-2">
                        <?php if ($can_edit): ?>
                            <a href="<?= site_url('uc15_qlns/uc15_bcsc/edit/' . $incident->id); ?>" class="btn btn-sm bg-gradient-warning mb-0">
                                <i class="material-icons text-white me-1">edit</i>Sửa
                            </a>
                        <?php endif; ?>
                        <?php if ($can_delete): ?>
                            <a href="<?= site_url('uc15_qlns/uc15_bcsc/delete/' . $incident->id); ?>" class="btn btn-sm bg-gradient-danger mb-0" onclick="return confirm('Bạn chắc chắn muốn xóa?');">
                                <i class="material-icons text-white me-1">delete</i>Xóa
                            </a>
                        <?php endif; ?>
                        <a href="<?= site_url('uc15_qlns/uc15_bcsc'); ?>" class="btn btn-sm btn-secondary mb-0">
                            <i class="material-icons text-white me-1">arrow_back</i>Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body px-5 py-4">
                <!-- Thông tin chung -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">Người báo cáo</h6>
                            <p class="text-base font-weight-bold"><?= $incident->user_name ?? 'N/A'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">Tên máy</h6>
                            <p class="text-base font-weight-bold"><?= $incident->machine_name ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">Mã dây chuyền</h6>
                            <p class="text-base font-weight-bold"><?= $incident->id_planshift; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">Trạng thái</h6>
                            <?php if ($incident->status == 1): ?>
                                <span class="badge bg-success">
                                    <i class="material-icons text-xs me-1">check_circle</i>Đã hoàn thành
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning">
                                    <i class="material-icons text-xs me-1">pending</i>Chưa hoàn thành
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Mô tả sự cố -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-3">Mô tả chi tiết sự cố</h6>
                        <p class="text-base" style="white-space: pre-wrap; line-height: 1.6;"><?= nl2br(htmlspecialchars($incident->incident_description)); ?></p>
                    </div>
                </div>

                <!-- Ảnh/Video -->
                <?php if (!empty($incident->media_path) && file_exists($incident->media_path)): ?>
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-3">Ảnh/Video</h6>
                            <?php
                            $file_ext = strtolower(pathinfo($incident->media_path, PATHINFO_EXTENSION));
                            $video_ext = ['mp4', 'avi', 'mov', 'mkv'];
                            ?>
                            <div class="text-center">
                                <?php if (in_array($file_ext, $video_ext)): ?>
                                    <video width="500" controls class="border-radius-lg">
                                        <source src="<?= site_url($incident->media_path); ?>" type="video/<?= $file_ext; ?>">
                                        Trình duyệt của bạn không hỗ trợ video.
                                    </video>
                                <?php else: ?>
                                    <img src="<?= site_url($incident->media_path); ?>" alt="Ảnh sự cố" class="img-fluid border-radius-lg" style="max-width: 500px;">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Thời gian -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">Ngày tạo</h6>
                            <p class="text-base">
                                <i class="material-icons text-xs me-1">schedule</i>
                                <?= date('d/m/Y H:i', strtotime($incident->created_at)); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">Cập nhật lần cuối</h6>
                            <p class="text-base">
                                <i class="material-icons text-xs me-1">update</i>
                                <?= date('d/m/Y H:i', strtotime($incident->updated_at)); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cập nhật trạng thái cho technical staff -->
                <?php if ($can_update_status): ?>
                    <div class="card border-1 my-4">
                        <div class="card-header bg-light">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-0">
                                <i class="material-icons text-xs me-2">update</i>Cập nhật trạng thái
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?= site_url('uc15_qlns/uc15_bcsc/update_status/' . $incident->id); ?>" class="row align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Trạng thái mới</label>
                                    <select name="status" class="form-control" required>
                                        <option value="0" <?= ($incident->status == 0) ? 'selected' : ''; ?>>Chưa hoàn thành</option>
                                        <option value="1" <?= ($incident->status == 1) ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="material-icons text-sm me-2">check</i>Cập nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
