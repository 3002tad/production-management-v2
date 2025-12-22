<div class="row pr-2">
    <div class="col-12">

        <!-- Current Shift -->
        <div class="card my-2">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="shadow-primary border-radius-lg d-flex justify-content-between align-items-center px-5 pt-4 pb-3">
                    <div class="d-flex align-items-center">
                        <i class="material-icons pr-3">schedule</i>
                        <h6 class="mb-0">Ca làm việc hiện tại</h6>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <?php if ($current_shift): ?>
                    <div class="px-4 py-3">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="text-primary mb-3"><?= $current_shift->shift_name ?> (<?= $current_shift->shift_code ?>)</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="material-icons text-info me-2" style="font-size: 20px;">event</i>
                                            <div>
                                                <h6 class="text-xs text-secondary mb-0">Ngày</h6>
                                                <p class="text-sm font-weight-bold mb-0"><?= date('d/m/Y', strtotime($current_shift->shift_date)) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="material-icons text-warning me-2" style="font-size: 20px;">schedule</i>
                                            <div>
                                                <h6 class="text-xs text-secondary mb-0">Thời gian</h6>
                                                <p class="text-sm font-weight-bold mb-0"><?= $current_shift->start_time ?> - <?= $current_shift->end_time ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <i class="material-icons text-success me-2" style="font-size: 20px;">info</i>
                                    <div>
                                        <h6 class="text-xs text-secondary mb-0">Trạng thái</h6>
                                        <p class="text-sm font-weight-bold mb-0">
                                            <?php
                                            $status_map = [
                                                1 => ['Chờ bắt đầu', 'warning'],
                                                2 => ['Đang chạy', 'success'],
                                                3 => ['Đã kết thúc', 'info'],
                                                4 => ['Đã chốt', 'secondary']
                                            ];
                                            $status = $status_map[$current_shift->shift_status] ?? ['N/A', 'secondary'];
                                            ?>
                                            <span class="badge badge-<?= $status[1] ?>"><?= $status[0] ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?php if (!empty($current_shift->zone_name)): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="material-icons text-primary me-2" style="font-size: 18px;">domain</i>
                                        <div>
                                            <h6 class="text-xs text-secondary mb-0">Khu vực</h6>
                                            <p class="text-xs font-weight-bold mb-0"><?= $current_shift->zone_name ?> (<?= $current_shift->zone_code ?>)</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($current_shift->line_name)): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="material-icons text-info me-2" style="font-size: 18px;">view_timeline</i>
                                        <div>
                                            <h6 class="text-xs text-secondary mb-0">Dây chuyền</h6>
                                            <p class="text-xs font-weight-bold mb-0"><?= $current_shift->line_name ?> (<?= $current_shift->line_code ?>)</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($current_shift->machine_name)): ?>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons text-warning me-2" style="font-size: 18px;">precision_manufacturing</i>
                                        <div>
                                            <h6 class="text-xs text-secondary mb-0">Máy được gán</h6>
                                            <p class="text-xs font-weight-bold mb-0"><?= $current_shift->machine_name ?> (<?= $current_shift->machine_code ?>)</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Production Data for Current Shift -->
                        <?php if (!empty($current_shift->production_records)): ?>
                        <div class="mt-4">
                            <h6 class="text-sm font-weight-bold mb-3">Dữ liệu sản lượng</h6>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Thời gian</th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Máy</th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tốt/Lỗi</th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hiệu suất</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($current_shift->production_records as $record): ?>
                                        <tr>
                                            <td class="text-xs"><?= date('H:i', strtotime($record->timestamp)) ?></td>
                                            <td class="text-xs"><?= $record->machine_code; ?></td>
                                            <td class="text-xs"><span class="text-success"><?= $record->good_count; ?></span>/<span class="text-danger"><?= $record->defect_count; ?></span></td>
                                            <td class="text-xs <?= ($record->efficiency_rate >= 80) ? 'text-success' : (($record->efficiency_rate >= 70) ? 'text-warning' : 'text-danger'); ?>"><?= number_format($record->efficiency_rate, 1); ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="material-icons text-secondary" style="font-size: 48px;">schedule</i>
                        <h6 class="text-secondary mt-3">Không có ca làm việc hiện tại</h6>
                        <p class="text-sm text-secondary">Bạn chưa được phân công vào ca nào đang hoạt động.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Shift History -->
        <div class="card my-2">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="shadow-info border-radius-lg d-flex justify-content-between align-items-center px-5 pt-4 pb-3">
                    <div class="d-flex align-items-center">
                        <i class="material-icons pr-3">history</i>
                        <h6 class="mb-0">Lịch sử ca làm việc</h6>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <?php if (!empty($shift_history)): ?>
                    <?php foreach ($shift_history as $shift): ?>
                    <div class="mb-4 pb-4" style="border-bottom: 1px solid #e9ecef;">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><?= $shift->shift_code; ?> - <?= $shift->shift_name; ?></h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="material-icons text-info me-2" style="font-size: 18px;">event</i>
                                            <div>
                                                <h6 class="text-xs text-secondary mb-0">Ngày</h6>
                                                <p class="text-xs font-weight-bold mb-0"><?= date('d/m/Y', strtotime($shift->shift_date)) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="material-icons text-warning me-2" style="font-size: 18px;">schedule</i>
                                            <div>
                                                <h6 class="text-xs text-secondary mb-0">Giờ</h6>
                                                <p class="text-xs font-weight-bold mb-0"><?= $shift->start_time ?> - <?= $shift->end_time ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="material-icons text-info me-2" style="font-size: 18px;">view_timeline</i>
                                            <div>
                                                <h6 class="text-xs text-secondary mb-0">Dây chuyền</h6>
                                                <p class="text-xs font-weight-bold mb-0"><?= $shift->line_code ?? '-'; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="material-icons text-warning me-2" style="font-size: 18px;">precision_manufacturing</i>
                                            <div>
                                                <h6 class="text-xs text-secondary mb-0">Máy</h6>
                                                <p class="text-xs font-weight-bold mb-0"><?= $shift->machine_code ?? '-'; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="material-icons text-success me-2" style="font-size: 18px;">info</i>
                                    <div>
                                        <h6 class="text-xs text-secondary mb-0">Trạng thái</h6>
                                        <p class="text-xs font-weight-bold mb-0">
                                            <?php
                                            $status_map = [
                                                1 => ['Chờ bắt đầu', 'warning'],
                                                2 => ['Đang chạy', 'success'],
                                                3 => ['Đã kết thúc', 'info'],
                                                4 => ['Đã chốt', 'secondary']
                                            ];
                                            $status = $status_map[$shift->shift_status] ?? ['N/A', 'secondary'];
                                            ?>
                                            <span class="badge badge-<?= $status[1] ?> badge-sm"><?= $status[0] ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Production Data -->
                        <?php if (!empty($shift->production_records)): ?>
                        <div class="mt-3">
                            <p class="text-xs font-weight-bold mb-2">Dữ liệu sản lượng:</p>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Thời gian</th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nhân viên</th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tốt/Lỗi/Tổng</th>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hiệu suất</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($shift->production_records as $record): ?>
                                        <tr>
                                            <td class="text-xs"><?= date('H:i', strtotime($record->timestamp)) ?></td>
                                            <td class="text-xs"><?= $record->staff_name ?: 'N/A'; ?></td>
                                            <td class="text-xs"><span class="text-success"><?= $record->good_count; ?></span>/<span class="text-danger"><?= $record->defect_count; ?></span>/<span class="font-weight-bold"><?= ($record->good_count + $record->defect_count); ?></span></td>
                                            <td class="text-xs <?= ($record->efficiency_rate >= 80) ? 'text-success' : (($record->efficiency_rate >= 70) ? 'text-warning' : 'text-danger'); ?>"><?= number_format($record->efficiency_rate, 1); ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php else: ?>
                        <p class="text-xs text-secondary mb-0">Không có dữ liệu sản lượng</p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="material-icons text-secondary" style="font-size: 48px;">history</i>
                        <h6 class="text-secondary mt-3">Không có lịch sử ca làm việc</h6>
                        <p class="text-sm text-secondary">Bạn chưa tham gia ca làm việc nào trước đây.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Incident Reports -->
        <div class="card my-2">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="shadow-warning border-radius-lg d-flex justify-content-between align-items-center px-5 pt-4 pb-3">
                    <div class="d-flex align-items-center">
                        <i class="material-icons pr-3">report_problem</i>
                        <h6 class="mb-0">Báo cáo sự cố gần đây</h6>
                    </div>
                    <a href="<?= site_url('uc15_bcsc/uc15_bcsc'); ?>" class="btn bg-gradient-warning mb-0">
                        <i class="material-icons text-white">list</i> Xem tất cả
                    </a>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <?php if (!empty($incidents)): ?>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mô tả</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Loại/Mức độ</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Trạng thái</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($incidents as $incident): ?>
                                    <tr>
                                        <td class="pl-4">
                                            <span class="text-sm"><?= substr($incident->incident_description, 0, 50); ?><?= strlen($incident->incident_description) > 50 ? '...' : ''; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <?php 
                                                $category_map = [
                                                    'equipment' => ['🔧 Thiết bị', 'primary'],
                                                    'quality' => ['✓ Chất lượng', 'info'],
                                                    'safety' => ['⚠ An toàn', 'danger'],
                                                    'other' => ['• Khác', 'secondary']
                                                ];
                                                $cat = $category_map[$incident->category] ?? ['N/A', 'secondary'];
                                                
                                                $severity_map = [
                                                    1 => ['1', 'success'],
                                                    2 => ['2', 'warning'],
                                                    3 => ['3', 'danger'],
                                                    4 => ['4', 'dark']
                                                ];
                                                $sev = $severity_map[$incident->severity_level] ?? ['?', 'secondary'];
                                            ?>
                                            <div>
                                                <span class="badge bg-<?= $cat[1]; ?> badge-sm"><?= $cat[0]; ?></span>
                                                <br>
                                                <span class="badge bg-<?= $sev[1]; ?> badge-sm mt-1">Mức <?= $sev[0]; ?></span>
                                            </div>
                                        </td>
                                        <td class="pl-4">
                                            <?php 
                                                if ($incident->status == 1) {
                                                    echo '<span class="badge bg-success">✓ Hoàn thành</span>';
                                                } elseif ($incident->status == 2) {
                                                    echo '<span class="badge bg-info">⏳ Đang xử lý</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning">⏸ Chờ xử lý</span>';
                                                }
                                            ?>
                                        </td>
                                        <td class="pl-4">
                                            <span class="text-sm"><?= date('d/m/Y H:i', strtotime($incident->created_at)); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="material-icons text-secondary" style="font-size: 48px;">report_problem</i>
                        <h6 class="text-secondary mt-3">Không có báo cáo sự cố</h6>
                        <p class="text-sm text-secondary">Bạn chưa tạo báo cáo sự cố nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>