<style>
    .closure-card {
        transition: all 0.3s ease;
    }
    .closure-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .machine-row.warning {
        background-color: #fff3cd !important;
        border-left: 4px solid #ffc107;
    }
    .machine-row.critical {
        background-color: #f8d7da !important;
        border-left: 4px solid #dc3545;
    }
    .confirmed-input {
        max-width: 120px;
    }
    .warning-badge {
        font-size: 12px;
        padding: 4px 8px;
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/'); ?>">Leader</a></li>
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/shift'); ?>">Ca làm việc</a></li>
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/shift/detail/' . $shift->shift_id); ?>"><?= $shift->shift_name ?></a></li>
                <li class="breadcrumb-item text-sm text-dark active">Chốt ca</li>
            </ol>
            <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
        </nav>
    </div>
</nav>

<div class="container-fluid py-4">
    <!-- Alert: Warnings -->
    <?php if ($has_warnings): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="material-icons">warning</i></span>
        <div class="alert-text">
            <strong>Cảnh báo!</strong> Phát hiện <?= count($warnings) ?> vấn đề cần lưu ý:
            <ul class="mt-2 mb-0">
                <?php foreach ($warnings as $warning): ?>
                <li>
                    <strong><?= $warning['machine_name'] ?>:</strong> <?= $warning['message'] ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Shift Summary Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">
                                <i class="material-icons text-lg" style="vertical-align: middle;">fact_check</i>
                                Tổng quan ca làm việc
                            </h5>
                            <p class="text-sm text-secondary mb-0">
                                Ca: <strong><?= $shift->shift_name ?></strong> | 
                                Ngày: <strong><?= date('d/m/Y', strtotime($shift->shift_date)) ?></strong> | 
                                Giờ: <strong><?= date('H:i', strtotime($shift->start_time)) ?> - <?= date('H:i', strtotime($shift->end_time)) ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Total Target -->
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card closure-card bg-gradient-primary text-white h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape bg-white shadow text-center border-radius-md me-3">
                                            <i class="material-icons text-primary text-lg opacity-10">flag</i>
                                        </div>
                                        <div>
                                            <p class="text-sm mb-0 text-white opacity-8">Mục tiêu</p>
                                            <h5 class="font-weight-bolder mb-0 text-white"><?= number_format($totals['total_target']) ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Produced -->
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card closure-card bg-gradient-info text-white h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape bg-white shadow text-center border-radius-md me-3">
                                            <i class="material-icons text-info text-lg opacity-10">inventory_2</i>
                                        </div>
                                        <div>
                                            <p class="text-sm mb-0 text-white opacity-8">Sản lượng thô</p>
                                            <h5 class="font-weight-bolder mb-0 text-white"><?= number_format($totals['total_produced']) ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Good -->
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card closure-card bg-gradient-success text-white h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape bg-white shadow text-center border-radius-md me-3">
                                            <i class="material-icons text-success text-lg opacity-10">check_circle</i>
                                        </div>
                                        <div>
                                            <p class="text-sm mb-0 text-white opacity-8">Thành phẩm</p>
                                            <h5 class="font-weight-bolder mb-0 text-white"><?= number_format($totals['total_good']) ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Defect -->
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card closure-card bg-gradient-warning text-white h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape bg-white shadow text-center border-radius-md me-3">
                                            <i class="material-icons text-warning text-lg opacity-10">report_problem</i>
                                        </div>
                                        <div>
                                            <p class="text-sm mb-0 text-white opacity-8">Phế phẩm</p>
                                            <h5 class="font-weight-bolder mb-0 text-white"><?= number_format($totals['total_defect']) ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- Efficiency Rate -->
                        <div class="col-md-4 col-6">
                            <div class="text-center">
                                <p class="text-sm text-secondary mb-1">Hiệu suất ca</p>
                                <h4 class="font-weight-bolder mb-0 
                                    <?= $totals['efficiency_rate'] >= 90 ? 'text-success' : ($totals['efficiency_rate'] >= 70 ? 'text-warning' : 'text-danger') ?>">
                                    <?= number_format($totals['efficiency_rate'], 2) ?>%
                                </h4>
                            </div>
                        </div>

                        <!-- Defect Rate -->
                        <div class="col-md-4 col-6">
                            <div class="text-center">
                                <p class="text-sm text-secondary mb-1">Tỷ lệ phế phẩm</p>
                                <h4 class="font-weight-bolder mb-0 
                                    <?= $totals['defect_rate'] < $thresholds['defect_warning'] ? 'text-success' : ($totals['defect_rate'] < $thresholds['defect_critical'] ? 'text-warning' : 'text-danger') ?>">
                                    <?= number_format($totals['defect_rate'], 2) ?>%
                                </h4>
                            </div>
                        </div>

                        <!-- Total Downtime -->
                        <div class="col-md-4 col-12">
                            <div class="text-center">
                                <p class="text-sm text-secondary mb-1">Tổng downtime</p>
                                <h4 class="font-weight-bolder mb-0 text-info">
                                    <?= number_format($totals['total_downtime']) ?> phút
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Closure Form -->
    <form id="closureForm">
        <input type="hidden" name="shift_id" value="<?= $shift->shift_id ?>">

        <!-- Machine Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">precision_manufacturing</i>
                            Chi tiết theo máy (<?= count($machines) ?> máy)
                        </h6>
                        <p class="text-sm text-secondary mb-0">
                            Xác nhận số lượng thành phẩm và phế phẩm cho từng máy
                        </p>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Máy</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nhân viên</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Mục tiêu</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Thành phẩm</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Phế phẩm</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Xác nhận<br>Thành phẩm</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Xác nhận<br>Phế phẩm</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Hiệu suất</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tỷ lệ lỗi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($machines as $index => $machine): ?>
                                    <?php 
                                        $row_class = '';
                                        if ($machine->defect_rate >= $thresholds['defect_critical']) {
                                            $row_class = 'critical';
                                        } elseif ($machine->defect_rate >= $thresholds['defect_warning'] || $machine->efficiency_rate < $thresholds['efficiency_warning']) {
                                            $row_class = 'warning';
                                        }
                                    ?>
                                    <tr class="machine-row <?= $row_class ?>">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm"><?= $machine->machine_name ?></h6>
                                                    <p class="text-xs text-secondary mb-0"><?= $machine->machine_code ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= $machine->staff_name ?? '<em>Chưa gán</em>' ?></p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold"><?= number_format($machine->target_count) ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold"><?= number_format($machine->good_count) ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold"><?= number_format($machine->defect_count) ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="number" 
                                                   class="form-control form-control-sm confirmed-input" 
                                                   name="confirmed_data[<?= $machine->machine_id ?>][good]" 
                                                   value="<?= $machine->good_count ?>" 
                                                   min="0" 
                                                   required
                                                   data-machine-id="<?= $machine->machine_id ?>">
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="number" 
                                                   class="form-control form-control-sm confirmed-input" 
                                                   name="confirmed_data[<?= $machine->machine_id ?>][defect]" 
                                                   value="<?= $machine->defect_count ?>" 
                                                   min="0" 
                                                   required
                                                   data-machine-id="<?= $machine->machine_id ?>">
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm 
                                                <?= $machine->efficiency_rate >= 90 ? 'bg-gradient-success' : ($machine->efficiency_rate >= 70 ? 'bg-gradient-warning' : 'bg-gradient-danger') ?>">
                                                <?= number_format($machine->efficiency_rate, 1) ?>%
                                            </span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm 
                                                <?= $machine->defect_rate < $thresholds['defect_warning'] ? 'bg-gradient-success' : ($machine->defect_rate < $thresholds['defect_critical'] ? 'bg-gradient-warning' : 'bg-gradient-danger') ?>">
                                                <?= number_format($machine->defect_rate, 2) ?>%
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quantity Confirmation Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">verified_user</i>
                            Xác nhận số lượng thực
                        </h6>
                        <p class="text-sm text-secondary mb-0">
                            Các số liệu dưới đây được lấy mặc định từ hệ thống ghi nhận sản xuất. Bạn có thể chỉnh sửa nếu cần.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Sản lượng thô (Raw Production) -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Sản lượng thô (chiếc)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="confirmed_raw_qty" 
                                               id="confirmed_raw_qty" min="0" 
                                               value="<?= isset($totals['total_produced']) ? (int)$totals['total_produced'] : 0 ?>"
                                               placeholder="Sản lượng thô">
                                        <span class="input-group-text text-secondary text-xs">
                                            <small>Mặc định: <?= isset($totals['total_produced']) ? number_format($totals['total_produced']) : '0' ?></small>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Thành phẩm (Good Products) -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Thành phẩm (chiếc)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="confirmed_good_qty" 
                                               id="confirmed_good_qty" min="0" 
                                               value="<?= isset($totals['total_good']) ? (int)$totals['total_good'] : 0 ?>"
                                               placeholder="Thành phẩm">
                                        <span class="input-group-text text-secondary text-xs">
                                            <small>Mặc định: <?= isset($totals['total_good']) ? number_format($totals['total_good']) : '0' ?></small>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Phế phẩm (Defect Products) -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Phế phẩm (chiếc)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="confirmed_defect_qty" 
                                               id="confirmed_defect_qty" min="0" 
                                               value="<?= isset($totals['total_defect']) ? (int)$totals['total_defect'] : 0 ?>"
                                               placeholder="Phế phẩm">
                                        <span class="input-group-text text-secondary text-xs">
                                            <small>Mặc định: <?= isset($totals['total_defect']) ? number_format($totals['total_defect']) : '0' ?></small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Validation Info -->
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="material-icons text-xs" style="vertical-align: middle;">info</i>
                                <strong>Lưu ý:</strong> Tổng thành phẩm + phế phẩm nên bằng sản lượng thô. Hệ thống sẽ kiểm tra khi lưu.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">comment</i>
                            Ghi chú
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Ghi chú khi chốt ca (nếu có)</label>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Nhập ghi chú về ca làm việc, sự cố phát sinh, điều chỉnh..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-sm text-secondary mb-0">
                                    <i class="material-icons text-xs text-warning">info</i>
                                    Sau khi chốt ca, hệ thống sẽ:
                                </p>
                                <ul class="text-sm text-secondary mb-0">
                                    <li>Lưu phiếu chốt ca với dữ liệu đã xác nhận</li>
                                    <li>Cập nhật trạng thái ca thành "Hoàn thành"</li>
                                    <li>Tạo đề nghị nhập kho thành phẩm (trạng thái: Chờ QC)</li>
                                </ul>
                            </div>
                            <div>
                                <a href="<?= site_url('leader/shift/detail/' . $shift->shift_id); ?>" 
                                   class="btn btn-light me-2">
                                    <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Quay lại
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="material-icons text-sm">check_circle</i>&nbsp;&nbsp;Chốt ca
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const closureForm = document.getElementById('closureForm');
    const submitBtn = document.getElementById('submitBtn');
    
    closureForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate quantity fields
        const rawQty = parseInt(document.getElementById('confirmed_raw_qty').value) || 0;
        const goodQty = parseInt(document.getElementById('confirmed_good_qty').value) || 0;
        const defectQty = parseInt(document.getElementById('confirmed_defect_qty').value) || 0;
        
        // Check if sum of good + defect equals raw
        if (rawQty > 0 && (goodQty + defectQty !== rawQty)) {
            alert('Lỗi: Tổng thành phẩm (' + goodQty + ') + phế phẩm (' + defectQty + ') = ' + 
                  (goodQty + defectQty) + ' không bằng sản lượng thô (' + rawQty + ')');
            return;
        }
        
        // Confirm action
        if (!confirm('Xác nhận chốt ca? Hành động này không thể hoàn tác.')) {
            return;
        }
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
        
        // Prepare data
        const formData = new FormData(closureForm);
        
        // Convert confirmed_data to JSON structure
        const confirmed_data = {};
        const inputs = closureForm.querySelectorAll('input[name^="confirmed_data"]');
        inputs.forEach(input => {
            const matches = input.name.match(/confirmed_data\[(\d+)\]\[(\w+)\]/);
            if (matches) {
                const machine_id = matches[1];
                const field = matches[2];
                
                if (!confirmed_data[machine_id]) {
                    confirmed_data[machine_id] = {};
                }
                confirmed_data[machine_id][field] = parseInt(input.value) || 0;
            }
        });
        
        // Create POST data
        const postData = new FormData();
        postData.append('shift_id', formData.get('shift_id'));
        postData.append('notes', formData.get('notes'));
        postData.append('confirmed_raw_qty', rawQty);
        postData.append('confirmed_good_qty', goodQty);
        postData.append('confirmed_defect_qty', defectQty);
        postData.append('confirmed_data', JSON.stringify(confirmed_data));
        
        // Send AJAX request
        fetch('<?= site_url('leader/save_closure'); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: postData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Chốt ca thành công!\nMã phiếu: ' + data.closure_code + 
                      (data.warehouse_request_code ? '\nMã đề nghị nhập kho: ' + data.warehouse_request_code : ''));
                
                // Redirect to shift detail
                window.location.href = '<?= site_url('leader/shift/detail/' . $shift->shift_id); ?>';
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi chốt ca: ' + error.message);
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="material-icons text-sm">check_circle</i>&nbsp;&nbsp;Chốt ca';
        });
    });
});
</script>
