<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="material-icons">error</i></span>
                    <span class="alert-text"><?= $this->session->flashdata('error'); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (validation_errors()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="material-icons">error</i></span>
                    <span class="alert-text"><?= validation_errors(); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <i class="material-icons text-warning me-2">edit</i>
                        <h5 class="mb-0">Chỉnh Sửa Báo Cáo Sự Cố</h5>
                    </div>
                    <p class="text-sm mb-0 mt-2">ID: <?= $incident->id; ?> | Ngày tạo: <?= date('d/m/Y H:i', strtotime($incident->created_at)); ?></p>
                </div>

                <div class="card-body">
                    <?php $controller_segment = $this->uri->segment(2) ?: 'uc15_bcsc'; ?>
                    <form action="<?= site_url('uc15_bcsc/' . $controller_segment . '/update/' . $incident->id); ?>" method="post" enctype="multipart/form-data">
                        <!-- Incident Location -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="text-sm text-uppercase text-secondary">Vị trí & Thời gian sự cố</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Ca làm việc (nếu có)</label>
                                    <select name="shift_id" class="form-control" id="shift_select">
                                        <option value="">-- Không chọn ca --</option>
                                        <?php if (!empty($shifts)): ?>
                                            <?php foreach ($shifts as $shift): ?>
                                                <option value="<?= $shift->shift_id; ?>" <?= ($shift->shift_id == $incident->shift_id) ? 'selected' : ''; ?>>
                                                    <?= $shift->shift_code; ?> - <?= $shift->shift_name; ?> 
                                                    (<?= date('d/m', strtotime($shift->shift_date)); ?> 
                                                    <?= date('H:i', strtotime($shift->start_time)); ?>-<?= date('H:i', strtotime($shift->end_time)); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Dây chuyền *</label>
                                    <select name="line_id" class="form-control" id="line_select" required>
                                        <option value="">-- Chọn dây chuyền --</option>
                                        <?php 
                                        $current_zone = '';
                                        foreach ($lines as $line): 
                                            if ($current_zone != $line->zone_name):
                                                if ($current_zone != '') echo '</optgroup>';
                                                $current_zone = $line->zone_name;
                                                echo '<optgroup label="' . ($line->zone_name ?: 'Không có khu') . '">';
                                            endif;
                                        ?>
                                            <option value="<?= $line->id; ?>" data-zone="<?= $line->zone_name; ?>" data-line-code="<?= $line->line_code; ?>" <?= ($line->id == $incident->line_id) ? 'selected' : ''; ?>>
                                                <?= $line->line_code; ?> - <?= $line->line_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php if ($current_zone != '') echo '</optgroup>'; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Máy móc (nếu liên quan máy cụ thể)</label>
                                    <select name="id_machine" class="form-control" id="machine_select">
                                        <option value="">-- Chọn máy (hoặc để trống) --</option>
                                        <?php foreach ($machines as $m): ?>
                                            <option value="<?= $m->id; ?>" data-line="<?= $m->line_code; ?>" 
                                                    <?= ($m->id == $incident->id_machine) ? 'selected' : ''; ?>>
                                                <?= $m->machine_code; ?> - <?= $m->machine_name; ?> (<?= $m->stage_type; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Incident Classification -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="text-sm text-uppercase text-secondary">Phân loại sự cố</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Loại sự cố *</label>
                                    <select name="category" class="form-control" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="equipment" <?= ($incident->category == 'equipment') ? 'selected' : ''; ?>>🔧 Thiết bị/Máy móc</option>
                                        <option value="quality" <?= ($incident->category == 'quality') ? 'selected' : ''; ?>>✓ Chất lượng sản phẩm</option>
                                        <option value="safety" <?= ($incident->category == 'safety') ? 'selected' : ''; ?>>⚠ An toàn lao động</option>
                                        <option value="other" <?= ($incident->category == 'other') ? 'selected' : ''; ?>>• Khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Mức độ nghiêm trọng *</label>
                                    <select name="severity_level" class="form-control" required>
                                        <option value="">-- Chọn mức độ --</option>
                                        <option value="1" <?= ($incident->severity_level == 1) ? 'selected' : ''; ?>>1 - Thấp (Low)</option>
                                        <option value="2" <?= ($incident->severity_level == 2) ? 'selected' : ''; ?>>2 - Trung bình (Medium)</option>
                                        <option value="3" <?= ($incident->severity_level == 3) ? 'selected' : ''; ?>>3 - Cao (High)</option>
                                        <option value="4" <?= ($incident->severity_level == 4) ? 'selected' : ''; ?>>4 - Nghiêm trọng (Critical)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Incident Description -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="text-sm text-uppercase text-secondary">Mô tả chi tiết</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group input-group-static mb-4">
                                    <label>Mô tả sự cố *</label>
                                    <textarea name="incident_description" class="form-control" rows="5" required 
                                              placeholder="Mô tả chi tiết tình huống, nguyên nhân (nếu biết), và tác động của sự cố..."><?= $incident->incident_description; ?></textarea>
                                    <small class="form-text text-muted">Tối thiểu 10 ký tự</small>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Resolution (for Technical staff) -->
                        <?php if ($user_role === 'technical' || $user_role === 'technical_staff'): ?>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="text-sm text-uppercase text-secondary">Trạng thái & Giải pháp</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Trạng thái</label>
                                    <select name="status" class="form-control">
                                        <option value="0" <?= ($incident->status == 0) ? 'selected' : ''; ?>>⏸ Chưa xử lý</option>
                                        <option value="2" <?= ($incident->status == 2) ? 'selected' : ''; ?>>⏳ Đang xử lý</option>
                                        <option value="1" <?= ($incident->status == 1) ? 'selected' : ''; ?>>✓ Đã hoàn thành</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group input-group-static mb-4">
                                    <label>Ghi chú giải pháp</label>
                                    <textarea name="resolution_notes" class="form-control" rows="3" 
                                              placeholder="Cách xử lý, kết quả, biện pháp phòng ngừa..."><?= $incident->resolution_notes ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Media Upload -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="text-sm text-uppercase text-secondary">Hình ảnh/Video</h6>
                            </div>
                            <div class="col-md-12">
                                <?php if (!empty($incident->media_path)): ?>
                                    <div class="mb-2">
                                        <small class="text-muted">File hiện tại: <?= basename($incident->media_path); ?></small>
                                    </div>
                                <?php endif; ?>
                                <div class="input-group input-group-static mb-4">
                                    <label>Cập nhật file mới (tùy chọn)</label>
                                    <input type="file" name="media" class="form-control" accept="image/*,video/*">
                                    <small class="form-text text-muted">Định dạng: JPG, PNG, GIF, MP4, AVI, MOV, MKV. Tối đa 51MB</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= site_url('uc15_bcsc/' . $controller_segment); ?>" class="btn btn-light m-0">
                                <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Hủy
                            </a>
                            <button type="submit" class="btn btn-warning m-0">
                                <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Cập Nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Cascade filtering: Line → Machine
document.addEventListener('DOMContentLoaded', function() {
    const lineSelect = document.getElementById('line_select');
    const machineSelect = document.getElementById('machine_select');
    
    function filterMachines() {
        const selectedLine = lineSelect.options[lineSelect.selectedIndex];
        const lineCode = selectedLine && selectedLine.value ? selectedLine.getAttribute('data-line-code') : '';
        
        const machineOptions = machineSelect.querySelectorAll('option');
        
        // Reset machine selection
        const currentMachineValue = machineSelect.value;
        let keepSelection = false;
        
        machineOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = ''; // Always show default option
            } else {
                const machineLineCode = option.getAttribute('data-line');
                if (lineCode && machineLineCode === lineCode) {
                    option.style.display = '';
                    if (option.value === currentMachineValue) {
                        keepSelection = true;
                    }
                } else {
                    option.style.display = 'none';
                }
            }
        });
        
        // If current selection not valid for new line, reset
        if (!keepSelection && currentMachineValue) {
            machineSelect.value = '';
        }
    }
    
    // Filter on line change
    lineSelect.addEventListener('change', filterMachines);
    
    // Initial filter on page load (handles pre-selected values)
    filterMachines();
});
</script>
