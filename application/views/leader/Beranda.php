<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Dashboard</h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
            <div class="col-6 d-flex text-end">
                <a href="<?= site_url('leader/logout'); ?>" class="btn gradient-dark mb-0">|  Logout
                <i class="material-icons">arrow_forward</i>
                </a>
            </div>     
            </div>
        </div>
    </nav>
</br>
<div class="content">
    <div class="container-fluid">
        <div class="row pb-3">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">add_task</i>
                        </div>
                        <p class="card-category">Projects</p>
                        <h3 class="card-title counter"><?= $project ?> </h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">add_task</i> Projects
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">exit_to_app</i>
                        </div>
                        <p class="card-category">Planning</p>
                        <h3 class="card-title counter"><?= $planning ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">exit_to_app</i> Planning Created
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-danger card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">info_outline</i>
                        </div>
                        <p class="card-category">Production</p>
                        <h3 class="card-title counter"><?= $plan_shift ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">local_offer</i> Process Production
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">done_all</i>
                        </div>
                        <p class="card-category">Project Progress</p>
                        <h3 class="card-title counter"><?= $finished_report ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">update</i>Project on Progress
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD FOR DATA BARANG MASUK & BARANG KELUAR -->
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header card-header-rose pb-0">
                        <h4 class="card-title">Production Progress</h4>
                        <p class="card-category">
                            Summary of production progress
                        </p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover">
                            <thead class="text-rose">
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
            <div class="col-lg-6 col-md-1">
                <div class="card">
                    <div class="card-header card-header-warning pb-0">
                        <h4 class="card-title">Production History</h4>
                        <p class="card-category">
                        Summary of last production
                        </p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover">
                            <thead class="text-warning">
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
                <div class="card">
                    <div class="card-header card-header-danger pb-0">
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
                                            <span class="badge badge-<?= ($incident->severity_level == 4) ? 'danger' : (($incident->severity_level == 3) ? 'warning' : 'info'); ?>">
                                                Mức <?= $incident->severity_level; ?>
                                            </span>
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
                            <div class="alert alert-success alert-with-icon" data-notify="container">
                                <span data-notify="icon" class="nc-icon nc-check-2"></span>
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
