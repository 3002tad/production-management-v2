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
                        <i class="material-icons text-danger me-2">report_problem</i>
                        <h5 class="mb-0">Báo Cáo Sự Cố Mới</h5>
                    </div>
                    <p class="text-sm mb-0 mt-2">Vui lòng điền đầy đủ thông tin sự cố xảy ra</p>
                </div>

                <div class="card-body">
                    <?php if (ENVIRONMENT === 'development'): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong>Debug Info:</strong> 
                            Lines: <?= count($lines ?? []); ?> | 
                            Machines: <?= count($machines ?? []); ?> | 
                            Shifts: <?= count($shifts ?? []); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= site_url('uc15_bcsc/uc15_bcsc/store'); ?>" method="post" enctype="multipart/form-data">
                        <!-- Incident Location -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="text-sm text-uppercase text-secondary">Vị trí & Thời gian sự cố</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Ca làm việc (nếu sự cố xảy ra trong ca)</label>
                                    <select name="shift_id" class="form-control" id="shift_select">
                                        <option value="">-- Không chọn ca --</option>
                                        <?php if (!empty($shifts)): ?>
                                            <?php foreach ($shifts as $shift): ?>
                                                <option value="<?= $shift->shift_id; ?>" 
                                                        <?= (isset($current_shift_id) && $current_shift_id == $shift->shift_id) ? 'selected' : ''; ?>>
                                                    <?= $shift->shift_code; ?> - <?= $shift->shift_name; ?> 
                                                    (<?= date('d/m', strtotime($shift->shift_date)); ?> 
                                                    <?= date('H:i', strtotime($shift->start_time)); ?>-<?= date('H:i', strtotime($shift->end_time)); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">Tùy chọn</small>
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
                                            <option value="<?= $line->id; ?>" data-zone="<?= $line->zone_name; ?>" data-line-code="<?= $line->line_code; ?>">
                                                <?= $line->line_code; ?> - <?= $line->line_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php if ($current_zone != '') echo '</optgroup>'; ?>
                                    </select>
                                    <small class="form-text text-muted">Bắt buộc</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-static mb-4">
                                    <label>Máy móc (nếu liên quan máy cụ thể)</label>
                                    <select name="id_machine" class="form-control" id="machine_select">
                                        <option value="">-- Chọn máy (hoặc để trống cho cả dây chuyền) --</option>
                                        <?php if (!empty($machines)): ?>
                                            <?php foreach ($machines as $m): ?>
                                                <option value="<?= $m->id; ?>" data-line="<?= $m->line_code; ?>">
                                                    <?= $m->machine_code; ?> - <?= $m->machine_name; ?> (<?= $m->stage_type; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Không có máy nào</option>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">Tùy chọn - <?= count($machines ?? []); ?> máy có sẵn</small>
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
                                        <option value="equipment">🔧 Thiết bị/Máy móc</option>
                                        <option value="quality">✓ Chất lượng sản phẩm</option>
                                        <option value="safety">⚠ An toàn lao động</option>
                                        <option value="other">• Khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-4">
                                    <label>Mức độ nghiêm trọng *</label>
                                    <select name="severity_level" class="form-control" required>
                                        <option value="">-- Chọn mức độ --</option>
                                        <option value="1">1 - Thấp (Low)</option>
                                        <option value="2">2 - Trung bình (Medium)</option>
                                        <option value="3">3 - Cao (High)</option>
                                        <option value="4">4 - Nghiêm trọng (Critical)</option>
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
                                              placeholder="Mô tả chi tiết tình huống, nguyên nhân (nếu biết), và tác động của sự cố..."></textarea>
                                    <small class="form-text text-muted">Tối thiểu 10 ký tự</small>
                                </div>
                            </div>
                        </div>

                        <!-- Media Upload -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="text-sm text-uppercase text-secondary">Hình ảnh/Video (Tùy chọn)</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group input-group-static mb-4">
                                    <label>Chứng từ hình ảnh/video</label>
                                    <input type="file" name="media" class="form-control" accept="image/*,video/*">
                                    <small class="form-text text-muted">Định dạng: JPG, PNG, GIF, MP4, AVI, MOV, MKV. Tối đa 51MB</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= site_url('uc15_bcsc/uc15_bcsc'); ?>" class="btn btn-light m-0">
                                <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Hủy
                            </a>
                            <button type="submit" class="btn btn-danger m-0">
                                <i class="material-icons text-sm">report</i>&nbsp;&nbsp;Gửi Báo Cáo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Cascade: When line is selected, show only machines of that line
document.getElementById('line_select').addEventListener('change', function() {
    const selectedLine = this.options[this.selectedIndex];
    const lineId = this.value;
    const lineCode = selectedLine && selectedLine.value ? selectedLine.getAttribute('data-line-code') : '';
    
    const machineSelect = document.getElementById('machine_select');
    const machineOptions = machineSelect.querySelectorAll('option');
    
    // Reset machine selection
    machineSelect.value = '';
    
    console.log('Selected line code:', lineCode);
    
    // Show/hide machines based on selected line
    let matchCount = 0;
    machineOptions.forEach(option => {
        if (option.value === '') {
            option.style.display = ''; // Always show default option
        } else {
            const machineLineCode = option.getAttribute('data-line');
            if (lineCode && machineLineCode === lineCode) {
                option.style.display = '';
                matchCount++;
            } else {
                option.style.display = 'none';
            }
        }
    });
    
    console.log('Matched machines:', matchCount);
});

// Initialize on page load
window.addEventListener('DOMContentLoaded', function() {
    const lineSelect = document.getElementById('line_select');
    const machineSelect = document.getElementById('machine_select');
    
    // Debug: Log all line codes and machine data-line attributes
    console.log('=== Debugging Machine Selection ===');
    console.log('Total lines:', lineSelect.options.length - 1); // -1 for default option
    console.log('Total machines:', machineSelect.options.length - 1);
    
    const lineOptions = lineSelect.querySelectorAll('option[data-line-code]');
    console.log('Lines with data-line-code:', lineOptions.length);
    lineOptions.forEach(opt => {
        console.log('Line:', opt.text, '| data-line-code:', opt.getAttribute('data-line-code'));
    });
    
    const machineOptions = machineSelect.querySelectorAll('option[data-line]');
    console.log('Machines with data-line:', machineOptions.length);
    machineOptions.forEach(opt => {
        console.log('Machine:', opt.text, '| data-line:', opt.getAttribute('data-line'));
    });
    
    // Initially, if no line is selected, show message but don't hide
    if (!lineSelect.value) {
        console.log('No line selected initially - machines hidden until line is chosen');
        machineOptions.forEach(option => {
            if (option.value !== '') {
                option.style.display = 'none';
            }
        });
    } else {
        // If line is pre-selected, trigger filter
        console.log('Line pre-selected:', lineSelect.value);
        lineSelect.dispatchEvent(new Event('change'));
    }
});
</script>
