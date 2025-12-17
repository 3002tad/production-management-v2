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
                            <a href="<?= site_url('uc15_bcsc/uc15_bcsc/edit/' . $incident->id); ?>" class="btn btn-sm bg-gradient-warning mb-0">
                                <i class="material-icons text-white me-1">edit</i>Sửa
                            </a>
                        <?php endif; ?>
                        <?php if ($can_delete): ?>
                            <a href="<?= site_url('uc15_bcsc/uc15_bcsc/delete/' . $incident->id); ?>" class="btn btn-sm bg-gradient-danger mb-0" onclick="return confirm('Bạn chắc chắn muốn xóa?');">
                                <i class="material-icons text-white me-1">delete</i>Xóa
                            </a>
                        <?php endif; ?>
                        <a href="<?= site_url('uc15_bcsc/uc15_bcsc'); ?>" class="btn btn-sm btn-secondary mb-0">
                            <i class="material-icons text-white me-1">arrow_back</i>Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body px-5 py-4">
                <!-- Thông tin cơ bản -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">
                                <i class="material-icons text-xs">person</i> Người báo cáo
                            </h6>
                            <p class="text-base font-weight-bold"><?= $incident->user_name ?? 'N/A'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">
                                <i class="material-icons text-xs">category</i> Loại sự cố
                            </h6>
                            <p class="text-base">
                                <?php 
                                $category_icons = [
                                    'equipment' => '🔧 Thiết bị',
                                    'quality' => '✓ Chất lượng',
                                    'safety' => '⚠ An toàn',
                                    'other' => '• Khác'
                                ];
                                echo $category_icons[$incident->category] ?? $incident->category;
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">
                                <i class="material-icons text-xs">priority_high</i> Mức độ
                            </h6>
                            <p class="text-base">
                                <?php 
                                $severity_colors = [1 => 'info', 2 => 'warning', 3 => 'danger', 4 => 'dark'];
                                $severity_labels = [1 => 'Thấp', 2 => 'Trung bình', 3 => 'Cao', 4 => 'Nghiêm trọng'];
                                $color = $severity_colors[$incident->severity_level] ?? 'secondary';
                                $label = $severity_labels[$incident->severity_level] ?? 'N/A';
                                ?>
                                <span class="badge bg-<?= $color; ?>"><?= $label; ?></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Vị trí sự cố -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-3">
                            <i class="material-icons text-xs">location_on</i> Vị trí & Thời gian sự cố
                        </h6>
                        <div class="row">
                            <?php if (!empty($incident->shift_code)): ?>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong class="text-xs text-secondary">Ca làm việc:</strong>
                                    <p class="text-sm mb-0">
                                        <span class="badge bg-info"><?= $incident->shift_code; ?></span>
                                        <?= $incident->shift_name; ?>
                                    </p>
                                    <?php if (!empty($incident->shift_date)): ?>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($incident->shift_date)); ?> 
                                            (<?= date('H:i', strtotime($incident->start_time)); ?>-<?= date('H:i', strtotime($incident->end_time)); ?>)
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong class="text-xs text-secondary">Khu vực:</strong>
                                    <p class="text-sm mb-0">
                                        <i class="material-icons text-xs">factory</i> 
                                        <?= $incident->zone_name ?? 'N/A'; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong class="text-xs text-secondary">Dây chuyền:</strong>
                                    <p class="text-sm mb-0">
                                        <span class="badge bg-primary"><?= $incident->line_code ?? 'N/A'; ?></span>
                                        <?= $incident->line_name ?? ''; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong class="text-xs text-secondary">Máy móc:</strong>
                                    <p class="text-sm mb-0">
                                        <?php if (!empty($incident->machine_code)): ?>
                                            <span class="badge bg-secondary"><?= $incident->machine_code; ?></span>
                                            <?= $incident->machine_name; ?>
                                        <?php else: ?>
                                            <em class="text-muted">Sự cố cả dây chuyền</em>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">
                                <i class="material-icons text-xs">flag</i> Trạng thái xử lý
                            </h6>
                            <?php if ($incident->status == 1): ?>
                                <span class="badge bg-success">
                                    <i class="material-icons text-xs me-1">check_circle</i>Đã hoàn thành
                                </span>
                            <?php elseif ($incident->status == 2): ?>
                                <span class="badge bg-warning">
                                    <i class="material-icons text-xs me-1">hourglass_empty</i>Đang xử lý
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="material-icons text-xs me-1">pending</i>Chờ xử lý
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-2">
                                <i class="material-icons text-xs">person_outline</i> Người xử lý
                            </h6>
                            <p class="text-base">
                                <?= !empty($incident->assignee_name) ? $incident->assignee_name : '<em class="text-muted">Chưa gán</em>'; ?>
                            </p>
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

                    <!-- Tiến độ xử lý (Progress history) -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Tiến Độ Xử Lý</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($coordination)): ?>
                                <?php foreach ($coordination as $c): ?>
                                    <div class="mb-3 border p-3" style="background-color:#f8f9fa;border-radius:4px;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong><?= htmlspecialchars($c->action_type); ?></strong>
                                                <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($c->created_at)); ?></div>
                                            </div>
                                            <div>
                                                <span class="badge bg-<?= (in_array($c->status, ['in_progress','submitted','repair_done_by_technical'])) ? 'warning' : 'secondary'; ?>"><?= htmlspecialchars($c->status ?? ''); ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <?php if (!empty($c->shift_info)): ?><strong>Thời gian/Info:</strong> <?= nl2br(htmlspecialchars($c->shift_info)); ?><br><?php endif; ?>
                                            <?php if (!empty($c->notes)): ?><strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($c->notes)); ?><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">Chưa có tiến độ xử lý nào.</div>
                            <?php endif; ?>

                            <?php if (isset($user_role) && in_array($user_role, ['technical','technical_staff'])): ?>
                                <hr>
                                <h6 class="mb-3">Cập Nhật Tiến Độ (Dành cho Technical)</h6>
                                <form method="post" action="<?= site_url('uc17_xlsc/update_progress'); ?>" class="row g-2 align-items-end">
                                    <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
                                    <div class="col-md-3">
                                        <label class="form-label">Tiến độ (%)</label>
                                        <input type="number" name="percent" min="0" max="100" class="form-control" required>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label">Ghi chú</label>
                                        <input type="text" name="notes" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-warning w-100">Cập Nhật</button>
                                    </div>
                                </form>

                                <hr>
                                <form method="post" action="<?= site_url('uc17_xlsc/mark_repair_done'); ?>">
                                    <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <input type="text" name="notes" class="form-control" placeholder="Ghi chú hoàn tất (tùy chọn)">
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-success w-100">Hoàn Tất (Kỹ thuật)</button>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
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
                <?php if ($can_update_status && !(isset($user_role) && in_array($user_role, ['technical', 'technical_staff']))): ?>
                    <div class="card border-1 my-4">
                        <div class="card-header bg-light">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 mb-0">
                                <i class="material-icons text-xs me-2">update</i>Cập nhật trạng thái
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?= site_url('uc15_bcsc/uc15_bcsc/update_status/' . $incident->id); ?>" class="row align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Trạng thái mới</label>
                                    <select name="status" class="form-control" required>
                                        <option value="0" <?= ($incident->status == 0) ? 'selected' : ''; ?>>⏸ Chờ xử lý - Pending</option>
                                        <option value="2" <?= ($incident->status == 2) ? 'selected' : ''; ?>>⏳ Đang xử lý - In Progress</option>
                                        <option value="1" <?= ($incident->status == 1) ? 'selected' : ''; ?>>✓ Đã hoàn thành - Completed</option>
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
