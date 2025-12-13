<div class="row pr-2">
    <div class="col-12">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                <span class="alert-text"><?= $this->session->flashdata('success'); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-support-16"></i></span>
                <span class="alert-text"><?= $this->session->flashdata('error'); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card my-2">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="shadow-dark border-radius-lg d-flex justify-content-between align-items-center px-5 pt-4 pb-3">
                    <div class="d-flex align-items-center">
                        <i class="material-icons pr-3">report_problem</i>
                        <h6 class="mb-0">Báo cáo sự cố</h6>
                    </div>
                    <?php if ($user_role === 'worker'): ?>
                        <a href="<?= site_url('uc15_bcsc/uc15_bcsc/add'); ?>" class="btn bg-gradient-dark mb-0">
                            <i class="material-icons text-white">add</i> Thêm báo cáo
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table id="incident-table" class="table align-items-center justify-content-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">STT</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Người báo cáo</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Máy</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Loại sự cố</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mức độ</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mô tả</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Trạng thái</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Ngày tạo</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="pl-3">
                            <?php if (!empty($incidents)): ?>
                                <?php $i = 1; foreach ($incidents as $incident): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex pl-3">
                                                <div class="my-auto">
                                                    <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="pl-4">
                                            <span class="text-sm font-weight-bold"><?= $incident->user_name ?? 'N/A'; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <span class="text-sm font-weight-bold"><?= $incident->machine_name ?? 'N/A'; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <?php 
                                                $category_map = [
                                                    'equipment' => ['Thiết bị', 'primary'],
                                                    'quality' => ['Chất lượng', 'info'],
                                                    'safety' => ['An toàn', 'danger'],
                                                    'other' => ['Khác', 'secondary']
                                                ];
                                                $cat = $category_map[$incident->category] ?? ['N/A', 'secondary'];
                                            ?>
                                            <span class="badge bg-<?= $cat[1]; ?>"><?= $cat[0]; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <?php 
                                                $severity_map = [
                                                    1 => ['Thấp', 'success'],
                                                    2 => ['Trung bình', 'warning'],
                                                    3 => ['Cao', 'danger'],
                                                    4 => ['Nghiêm trọng', 'dark']
                                                ];
                                                $sev = $severity_map[$incident->severity_level] ?? ['N/A', 'secondary'];
                                            ?>
                                            <span class="badge bg-<?= $sev[1]; ?>"><?= $sev[0]; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <span class="text-sm"><?= substr($incident->incident_description, 0, 40); ?><?= strlen($incident->incident_description) > 40 ? '...' : ''; ?></span>
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
                                        <td>
                                            <a href="<?= site_url('uc15_bcsc/uc15_bcsc/detail/' . $incident->id); ?>" rel="tooltip" title="Xem chi tiết" class="badge bg-gradient-info">Xem</a>
                                            <?php if ($user_role === 'worker' || $user_role === 'technical'): ?>
                                                <a href="<?= site_url('uc15_bcsc/uc15_bcsc/edit/' . $incident->id); ?>" rel="tooltip" title="Sửa" class="badge bg-gradient-warning">Sửa</a>
                                            <?php endif; ?>
                                            <?php if ($user_role === 'worker'): ?>
                                                <a href="<?= site_url('uc15_bcsc/uc15_bcsc/delete/' . $incident->id); ?>" rel="tooltip" title="Xóa" class="badge bg-gradient-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?');">Xóa</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="material-icons" style="font-size: 48px; opacity: 0.3;">folder_open</i>
                                        <p class="text-sm mt-2">Không có báo cáo sự cố nào</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
