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
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><?= $current_shift->shift_name ?> (<?= $current_shift->shift_code ?>)</h5>
                                <p class="mb-2"><strong>Ngày:</strong> <?= date('d/m/Y', strtotime($current_shift->shift_date)) ?></p>
                                <p class="mb-2"><strong>Thời gian:</strong> <?= $current_shift->start_time ?> - <?= $current_shift->end_time ?></p>
                                <p class="mb-2"><strong>Trạng thái:</strong>
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
                            <div class="col-md-6">
                                <?php if (!empty($current_shift->zone_name)): ?>
                                    <p class="mb-2"><strong>Khu vực:</strong> <?= $current_shift->zone_name ?> (<?= $current_shift->zone_code ?>)</p>
                                <?php endif; ?>
                                <?php if (!empty($current_shift->line_name)): ?>
                                    <p class="mb-2"><strong>Dây chuyền:</strong> <?= $current_shift->line_name ?> (<?= $current_shift->line_code ?>)</p>
                                <?php endif; ?>
                                <?php if (!empty($current_shift->machine_name)): ?>
                                    <p class="mb-2"><strong>Máy được gán:</strong> <?= $current_shift->machine_name ?> (<?= $current_shift->machine_code ?>)</p>
                                <?php endif; ?>
                            </div>
                        </div>
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
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Ca làm việc</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Ngày</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Thời gian</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Dây chuyền</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Máy</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shift_history as $shift): ?>
                                    <tr>
                                        <td class="pl-4">
                                            <div>
                                                <span class="text-xs font-weight-bold"><?= $shift->shift_code; ?></span>
                                                <br>
                                                <span class="text-xs text-secondary"><?= $shift->shift_name; ?></span>
                                            </div>
                                        </td>
                                        <td class="pl-4">
                                            <span class="text-xs"><?= date('d/m/Y', strtotime($shift->shift_date)) ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <span class="text-xs"><?= $shift->start_time ?> - <?= $shift->end_time ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <?php if (!empty($shift->line_name)): ?>
                                                <span class="text-xs font-weight-bold"><?= $shift->line_code; ?></span>
                                                <br>
                                                <span class="text-xs text-secondary"><?= $shift->line_name; ?></span>
                                            <?php else: ?>
                                                <span class="text-xs text-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pl-4">
                                            <?php if (!empty($shift->machine_name)): ?>
                                                <span class="text-xs font-weight-bold"><?= $shift->machine_code; ?></span>
                                                <br>
                                                <span class="text-xs text-secondary"><?= $shift->machine_name; ?></span>
                                            <?php else: ?>
                                                <span class="text-xs text-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pl-4">
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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