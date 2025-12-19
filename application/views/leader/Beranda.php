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
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(14, 165, 233, 0.06));
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
    }

    .md3-navbar-title {
        font-weight: 600;
        letter-spacing: 0.02em;
        color: var(--md3-on-surface);
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
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
            </ol>
            <h6 class="md3-navbar-title mb-0">Leader Dashboard</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center justify-content-end w-100">
                <span class="me-3 text-sm text-secondary d-none d-md-inline">Production System</span>
                <a href="<?= site_url('leader/logout'); ?>" class="btn btn-md3-logout mb-0">
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
                            <p class="md3-stat-label">Projects</p>
                            <h3 class="md3-stat-value counter"><?= $project ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-primary">
                            <span class="material-icons-round">add_task</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">insights</span>
                        <span>Tổng số dự án</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card md3-stat-card">
                    <div class="md3-stat-header">
                        <div>
                            <p class="md3-stat-label">Planning</p>
                            <h3 class="md3-stat-value counter"><?= $planning ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-success">
                            <span class="material-icons-round">exit_to_app</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">event_note</span>
                        <span>Kế hoạch đã tạo</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card md3-stat-card">
                    <div class="md3-stat-header">
                        <div>
                            <p class="md3-stat-label">Production</p>
                            <h3 class="md3-stat-value counter"><?= $plan_shift ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-warning">
                            <span class="material-icons-round">factory</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">pending_actions</span>
                        <span>Ca sản xuất đang xử lý</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card md3-stat-card">
                    <div class="md3-stat-header">
                        <div>
                            <p class="md3-stat-label">Project Progress</p>
                            <h3 class="md3-stat-value counter"><?= $finished_report ?></h3>
                        </div>
                        <div class="md3-stat-icon gradient-info">
                            <span class="material-icons-round">done_all</span>
                        </div>
                    </div>
                    <div class="md3-stat-footer">
                        <span class="material-icons-round" style="font-size:16px;">update</span>
                        <span>Dự án đã hoàn thành / đang chạy</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD FOR DATA BARANG MASUK & BARANG KELUAR -->
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Production Progress</h4>
                        <p class="card-category">Summary of production progress</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover md3-table">
                            <thead>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Qty Request</th>
                                <th>Finished</th>
                            </thead>
                            <tbody>
                            <?php if (!empty($finished)) : $i = 1; foreach ($finished as $value) : ?>

                                <tr>
                                    <td class ="pl-4"> <?= $i++; ?> </td>
                                    <td class ="pl-4"> <?= $value->cust_name?> </td>
                                    <td class ="pl-4"> <?= $value->qty_request?> Kg</td>
                                    <td class ="pl-4"> <?= $value->total_finished?> Kg</td>
                                </tr>
                            <?php  endforeach; endif;?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Production History</h4>
                        <p class="card-category">Summary of last production</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover md3-table">
                            <thead>
                                <th>ID</th>
                                <th>Planning</th>
                                <th>Shiftment</th>
                                <th>Finished</th>
                            </thead>
                            <tbody>
                            <?php if (!empty($sorting)) : $i = 1; foreach ($sorting as $value) : ?>
                                <tr>
                                    <td class ="pl-4"> <?= $i++; ?> </td>
                                    <td class ="pl-4"> <?= $value->plan_name?> </td>
                                    <td class ="pl-4"> <?= $value->staff_name?> </td>
                                    <td class ="pl-4"> <?= $value->finished?> Kg</td>
                                </tr>
                            <?php  endforeach; endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW INCIDENT NOTIFICATION BOX -->
        <div class="row mt-4">
            <div class="col-lg-12 col-md-12">
                <div class="card md3-section-card">
                    <div class="md3-section-header">
                        <h4 class="card-title">Sự Cố Mới
                            <?php if (isset($new_incident_count) && $new_incident_count > 0): ?>
                                <span class="badge badge-danger" style="background-color: #f4623a; color: white; font-size: 14px; padding: 5px 10px; border-radius: 20px;">
                                    <?= $new_incident_count; ?>
                                </span>
                            <?php endif; ?>
                        </h4>
                        <p class="card-category">Danh sách sự cố chưa được xử lý</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <?php if (!empty($new_incidents)): ?>
                            <table class="table table-hover">
                                <thead class="text-danger">
                                    <th>ID</th>
                                    <th>Máy</th>
                                    <th>Loại Sự Cố</th>
                                    <th>Mức Độ</th>
                                    <th>Mô Tả</th>
                                    <th>Hành Động</th>
                                </thead>
                                <tbody>
                                <?php $i = 1; foreach ($new_incidents as $incident): ?>
                                    <tr>
                                        <td class="pl-4"><?= $i++; ?></td>
                                        <td class="pl-4"><?= $incident->id_machine; ?></td>
                                        <td class="pl-4">
                                            <?php
                                                $categories = [
                                                    'equipment' => 'Thiết Bị',
                                                    'quality' => 'Chất Lượng',
                                                    'safety' => 'An Toàn',
                                                    'other' => 'Khác'
                                                ];
                                                echo $categories[$incident->category] ?? $incident->category;
                                            ?>
                                        </td>
                                        <td class="pl-4">
                                            <?php
                                                $severityClass = 'badge-md3-severity-2';
                                                if ($incident->severity_level == 4) {
                                                    $severityClass = 'badge-md3-severity-4';
                                                } elseif ($incident->severity_level == 3) {
                                                    $severityClass = 'badge-md3-severity-3';
                                                }
                                            ?>
                                            <span class="<?= $severityClass; ?>">Mức <?= $incident->severity_level; ?></span>
                                        </td>
                                        <td class="pl-4">
                                            <?= substr($incident->incident_description, 0, 50); ?>...
                                        </td>
                                        <td class="pl-4">
                                            <a href="<?= site_url('uc16_gn_dp/view/' . $incident->id) . '#assign'; ?>" class="btn btn-sm btn-info">Chi tiết / Điều phối</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
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
