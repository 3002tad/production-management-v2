<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - QC Module</title>
    <link rel="stylesheet" href="<?= base_url('asset/Backend/assets/css/material-dashboard.min.css') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body class="g-sidenav-show bg-gray-200">
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3"><?= $title ?></h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã Session</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Closure</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dự án</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sản phẩm</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Trạng thái</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kết quả</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($sessions)): ?>
                                        <?php foreach ($sessions as $session): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?= $session->code ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $session->closure_code ?></p>
                                                <p class="text-xs text-secondary mb-0"><?= $session->line_code ?> - <?= $session->shift_code ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $session->project_name ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $session->product_name ?></p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <?php if ($session->status == 'OPEN'): ?>
                                                    <span class="badge badge-sm bg-gradient-info">ĐANG KIỂM TRA</span>
                                                <?php else: ?>
                                                    <span class="badge badge-sm bg-gradient-secondary">ĐÃ QUYẾT ĐỊNH</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?php if ($session->result): ?>
                                                    <?php if ($session->result == 'APPROVE'): ?>
                                                        <span class="badge badge-sm bg-gradient-success">✓ PHÊ DUYỆT</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-sm bg-gradient-danger">✗ TỪ CHỐI</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-secondary text-xs">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs"><?= date('d/m/Y H:i', strtotime($session->created_at)) ?></span>
                                                <?php if ($session->decided_at): ?>
                                                    <br><span class="text-xs text-success">Quyết định: <?= date('d/m H:i', strtotime($session->decided_at)) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <a href="<?= site_url('qc/sessions/' . $session->id) ?>" class="btn btn-sm btn-primary">
                                                    <i class="material-icons text-sm">visibility</i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">assignment</i>
                                                <p class="text-secondary">Chưa có phiên kiểm tra nào</p>
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
    </div>

    <script src="<?= base_url('asset/Backend/assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('asset/Backend/assets/js/core/bootstrap.min.js') ?>"></script>
</body>
</html>
