<!-- Typography & Icons: Poppins + Material Icons Round -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

<style>
    :root {
        --md3-surface: #f5f7fb;
        --md3-surface-alt: #ffffff;
        --md3-on-surface: #111827;
        --md3-primary: #2563eb;
        --md3-secondary: #0ea5e9;
        --md3-success: #22c55e;
        --md3-warning: #f59e0b;
        --md3-danger: #ef4444;
        --md3-radius-lg: 18px;
        --md3-shadow-soft: 0 12px 30px rgba(15, 23, 42, 0.12);
    }

    body, .content {
        font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .material-icons, .material-icons-round {
        font-family: 'Material Icons Round', 'Material Icons';
        font-feature-settings: 'liga';
    }

    .md3-navbar {
        backdrop-filter: blur(12px);
        /* Darker pink gradient to match project header more closely */
        background: linear-gradient(90deg, #d81b60 0%, #ef3b7b 100%);
        color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
    }

    .md3-navbar-title {
        font-weight: 600;
        letter-spacing: 0.02em;
        color: #ffffff;
    }

    .md3-navbar .breadcrumb .breadcrumb-item a,
    .md3-navbar .breadcrumb .breadcrumb-item {
        color: rgba(255,255,255,0.85) !important;
    }

    .md3-navbar .text-secondary {
        color: rgba(255,255,255,0.9) !important;
    }

    .btn-md3-logout {
        border-radius: 999px;
        padding-inline: 18px;
        background: linear-gradient(135deg, #0f172a, #1f2937);
        color: #f9fafb !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
    }

    .btn-md3-logout .material-icons-round {
        font-size: 18px;
    }

    .md3-stat-card {
        border-radius: var(--md3-radius-lg);
        border: 0;
        background: var(--md3-surface-alt);
        box-shadow: var(--md3-shadow-soft);
        overflow: hidden;
    }

    .md3-stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px 10px;
    }

    .md3-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.18);
    }

    .md3-stat-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .md3-stat-value {
        font-size: 26px;
        font-weight: 600;
        color: var(--md3-on-surface);
        margin: 0;
    }

    .md3-stat-footer {
        border-top: 1px solid rgba(148, 163, 184, 0.18);
        padding: 10px 18px 12px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
    }

    /* Gradient status themes */
    .gradient-primary { background: linear-gradient(135deg, #2563eb, #3b82f6); }
    .gradient-success { background: linear-gradient(135deg, #16a34a, #22c55e); }
    .gradient-warning { background: linear-gradient(135deg, #f97316, #facc15); }
    .gradient-info { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
    .gradient-danger { background: linear-gradient(135deg, #ef4444, #f97316); }
    .gradient-rose { background: linear-gradient(135deg, #ec4899, #f97316); }

    .md3-section-card {
        border-radius: var(--md3-radius-lg);
        border: 0;
        background: var(--md3-surface-alt);
        box-shadow: var(--md3-shadow-soft);
    }

    .md3-section-header {
        padding: 16px 20px 10px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
    }

    .md3-section-header h4 {
        font-weight: 600;
        margin-bottom: 2px;
    }

    .md3-section-header p {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 0;
    }

    .md3-table thead th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        border-bottom-width: 1px;
        border-color: rgba(148, 163, 184, 0.35);
    }

    .md3-table tbody td {
        font-size: 13px;
        color: #111827;
        vertical-align: middle;
    }

    .badge-md3-severity-4 {
        background: linear-gradient(135deg, #b91c1c, #ef4444);
        color: #fef2f2;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
    }

    .badge-md3-severity-3 {
        background: linear-gradient(135deg, #d97706, #f97316);
        color: #fffbeb;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
    }

    .badge-md3-severity-2 {
        background: linear-gradient(135deg, #0369a1, #0ea5e9);
        color: #e0f2fe;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
    }
</style>

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl md3-navbar" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-2 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5" href="javascript:;" style="color: rgba(255,255,255,0.7);">Pages</a></li>
                <li class="breadcrumb-item text-sm active" aria-current="page" style="color: rgba(255,255,255,0.85);">Trưởng dây chuyền</li>
            </ol>
            <h6 class="md3-navbar-title mb-0">Trưởng dây chuyền</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center justify-content-end w-100">
                <span class="me-3 text-sm text-secondary d-none d-md-inline">Production System</span>
                <a href="<?= site_url('leader/logout'); ?>" class="btn btn-md3-logout mb-0" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                    <span>Logout</span>
                    <span class="material-icons-round">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</nav>
<br/>
<div class="content">
    <div class="container-fluid">
        <div class="row pb-3">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card md3-stat-card">
                    <div class="md3-stat-header">
                        <div>
                            <p class="md3-stat-label">Tổng Đơn Hàng</p>
                            <h3 class="md3-stat-value counter"><?= $project ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-primary">
                            <span class="material-icons-round">task</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">insights</span>
                        <span style="margin-left:6px;"><span class="text-success" style="font-weight:600;">+0%</span>&nbsp;so với tháng trước</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card md3-stat-card">
                    <div class="md3-stat-header">
                        <div>
                            <p class="md3-stat-label">Kế Hoạch Sản Xuất</p>
                            <h3 class="md3-stat-value counter"><?= $planning ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-success">
                            <span class="material-icons-round">event_note</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">event_note</span>
                        <span class="text-success" style="margin-left:6px;">Đã lập kế hoạch</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card md3-stat-card">
                    <div class="md3-stat-header">
                        <div>
                            <p class="md3-stat-label">Ca Đã Hoàn Thành</p>
                            <h3 class="md3-stat-value counter"><?= $plan_shift ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-warning">
                            <span class="material-icons-round">factory</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">pending_actions</span>
                        <span class="text-danger" style="margin-left:6px;">Theo ca máy</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card md3-stat-card">
                    <div class="md3-stat-header">
                        <div>
                            <p class="md3-stat-label">Đã Sản Xuất</p>
                            <h3 class="md3-stat-value counter"><?= $finished_report ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-info">
                            <span class="material-icons-round">done_all</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">update</span>
                        <span class="text-success" style="margin-left:6px;">Báo cáo sản xuất</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD FOR DATA BARANG MASUK & BARANG KELUAR -->
        <div class="row mb-3">
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Đơn hàng gần đây</h4>
                        <p class="card-category">Danh sách đơn hàng mới nhất</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover md3-table">
                            <thead>
                                <th>ID</th>
                                <th>Đơn hàng</th>
                                <th>Khách hàng</th>
                            </thead>
                            <tbody>
                            <?php if (!empty($finished)) : foreach ($finished as $p) : ?>
                                <tr>
                                    <td class ="pl-4"> <?= $p->id_project ?? '-' ?> </td>
                                    <td class ="pl-4"> <?= $p->project_name ?? '-' ?> </td>
                                    <td class ="pl-4"> <?= $p->cust_name ?? '-' ?> </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Chưa có đơn hàng</td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Kế hoạch sản xuất gần đây</h4>
                        <p class="card-category">Danh sách kế hoạch mới nhất</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover md3-table">
                            <thead>
                                <th>ID</th>
                                <th>Kế hoạch</th>
                                <th>Đơn hàng</th>
                                <th>Ngày</th>
                            </thead>
                            <tbody>
                            <?php if (!empty($sorting)) : foreach ($sorting as $pl) : ?>
                                <tr>
                                    <td class ="pl-4"> <?= $pl->id_sorting ?? '-' ?> </td>
                                    <td class ="pl-4"> <?= $pl->plan_name ?? '-' ?> </td>
                                    <td class ="pl-4"> <?= $pl->project_name ?? '-' ?> </td>
                                    <td class ="pl-4"> Hôm nay </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có kế hoạch</td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Công suất máy đang chạy trong ca</h4>
                        <p class="card-category">Tình trạng và công suất hiện tại</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover md3-table">
                            <thead>
                                <th>Máy</th>
                                <th class="text-center">Sản lượng</th>
                                <th class="text-center">Hiệu suất (%)</th>
                            </thead>
                            <tbody>
                            <?php if (!empty($machine_capacity)) : ?>
                                <?php foreach ($machine_capacity as $mc) : ?>
                                    <tr>
                                        <td class ="pl-4"> <?= htmlspecialchars(($mc['machine_code'] ?? '') . ' - ' . ($mc['machine_name'] ?? '')) ?> </td>
                                        <td class ="pl-4 text-center"> <?= number_format($mc['total_produced'] ?? 0) ?> </td>
                                        <td class ="pl-4 text-center"> <?= number_format($mc['avg_efficiency'] ?? 0, 2) ?> </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Không có dữ liệu công suất trong ca hiện tại</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Đơn hàng đã sản xuất gần đây</h4>
                        <p class="card-category">Danh sách đơn hàng đã sản xuất</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover md3-table">
                            <thead>
                                <th>Dự án</th>
                                <th>Khách hàng</th>
                                <th class="text-center">SL Hoàn thành</th>
                            </thead>
                            <tbody>
                            <?php if (!empty($finished)) : $i = 1; foreach ($finished as $value) : ?>
                                <?php if ($i > 5) break; ?>
                                <tr>
                                    <td class ="pl-4"> <?= $value->project_name?> </td>
                                    <td class ="pl-4"> <?= $value->cust_name?> </td>
                                    <td class ="pl-4 text-center"> <?= number_format($value->total_finished)?> </td>
                                </tr>
                            <?php $i++; endforeach; endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- INCIDENT DISPATCH WIDGET -->
        <div class="row mt-4">
            <div class="col-lg-12 col-md-12">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Sự Cố Cần Xử Lý
                            <?php if (isset($new_incident_count) && $new_incident_count > 0): ?>
                                <span class="badge badge-danger" style="background-color: #f4623a; color: white; font-size: 14px; padding: 5px 10px; border-radius: 20px;">
                                    <?= $new_incident_count; ?>
                                </span>
                            <?php endif; ?>
                        </h4>
                        <p class="card-category">Các sự cố cần điều phối và xử lý</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <?php if (!empty($new_incidents)): ?>
                            <table class="table table-hover md3-table">
                                <thead>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">STT</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Khu Vực</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Dây Chuyền</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Máy</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Loại</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mức Độ</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hành Động</th>
                                </thead>
                                <tbody>
                                <?php $i = 1; foreach ($new_incidents as $incident): ?>
                                    <tr>
                                        <td class="pl-4"><?= $i++; ?></td>
                                        <td class="pl-4"><?= htmlspecialchars($incident->zone_name ?? '-'); ?></td>
                                        <td class="pl-4"><span class="badge bg-secondary"><?= htmlspecialchars($incident->line_code ?? '-'); ?></span></td>
                                        <td class="pl-4"><?= htmlspecialchars($incident->machine_name ?? '-'); ?></td>
                                        <td class="pl-4">
                                            <?php
                                                $categories = [
                                                    'equipment' => ['🔧 Thiết Bị', 'primary'],
                                                    'quality' => ['✓ Chất Lượng', 'info'],
                                                    'safety' => ['⚠ An Toàn', 'danger'],
                                                    'other' => ['• Khác', 'secondary']
                                                ];
                                                $cat = $categories[$incident->category] ?? ['N/A', 'secondary'];
                                            ?>
                                            <span class="badge bg-<?= $cat[1]; ?>"><?= $cat[0]; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <?php
                                                $severity_map = [
                                                    1 => ['1', 'success'],
                                                    2 => ['2', 'warning'],
                                                    3 => ['3', 'danger'],
                                                    4 => ['4', 'dark']
                                                ];
                                                $sev = $severity_map[$incident->severity_level] ?? ['?', 'secondary'];
                                            ?>
                                            <span class="badge bg-<?= $sev[1]; ?>">Mức <?= $sev[0]; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <a href="<?= site_url('uc16_gn_dp/view/' . $incident->id); ?>" class="btn btn-sm btn-info">Điều phối</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="text-center mt-3">
                                <a href="<?= site_url('uc16_gn_dp'); ?>" class="btn btn-sm btn-primary">Xem tất cả sự cố →</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success alert-with-icon" data-notify="container" style="border-radius: 16px; border: 0; box-shadow: var(--md3-shadow-soft);">
                                <span data-notify="icon" class="material-icons-round">check_circle</span>
                                <span data-notify="message">
                                    <b>Tuyệt vời!</b> Hiện tại không có sự cố nào cần xử lý.
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
