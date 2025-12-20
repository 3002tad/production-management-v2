<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Breadcrumb Navigation -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Kho thành phẩm</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Kho thành phẩm</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
                <div class="col-6 d-flex text-end">
                    <a href="<?= site_url('warehouse/logout'); ?>" class="btn gradient-dark mb-0">|  <?= lang('btn_logout'); ?>
                    <i class="material-icons">arrow_forward</i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- Finished Goods Management Dashboard -->
<div class="container-fluid mt-4">

    <br/>

    <!-- Card tiêu đề: Quản lý thành phẩm -->
    <div class="container-fluid pt-0 px-4">
        <div class="card-header p-0 mt-n4 mx-2 z-index-2">
            <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
                <div class="col-8 d-flex align-items-center">
                    <i class="material-icons pr-3 text-lg">inventory_2</i>
                    <h6 class="mb-0 pr-4">Quản lý thành phẩm</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Hai card dưới: Phiếu nhập & Phiếu xuất -->
    <div class="container-fluid py-4 pt-2 px-4">
        <div class="row g-4 mx-1">
            <!-- Phiếu nhập thành phẩm (40%) -->
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header card-header-info py-3 px-4">
                        <div class="row">
                            <div class="col-7 align-items-center">
                                <h6 class="mb-0">Danh sách phiếu nhập</h6>
                                <span class="text-sm mb-0">Nhập thành phẩm từ QC vào kho</span>
                            </div>
                            <div class="col-5 text-end">
                                <a href="<?= site_url('warehouse/finished/receipt') ?>" class="btn btn-sm btn-white mb-0 me-2" title="Xem danh sách">
                                    <i class="material-icons-round align-middle text-lg">view_list</i>
                                </a>
                                <a href="<?= site_url('warehouse/finished/receipt/new') ?>" class="btn btn-sm btn-white mb-0" title="Tạo phiếu mới">
                                    <i class="material-icons-round align-middle text-lg">add_circle</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3 px-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <p class="text-sm text-muted mb-0">Tổng phiếu nhập</p>
                            <h6 class="mb-0 font-weight-bold">
                                <?php 
                                    if (!empty($receipts_data)) {
                                        echo count($receipts_data);
                                    } else {
                                        echo '0';
                                    }
                                ?>
                            </h6>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center justify-content-center mb-0 table-center table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mã phiếu</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Dự án</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">SL nhập</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Trạng thái</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="pl-3">
                                    <?php if (!empty($receipts_data)): ?>
                                        <?php $count = 0; foreach ($receipts_data as $receipt): if ($count >= 5) break; $count++; ?>
                                        <tr>
                                            <td>
                                                <span class="text-xs font-weight-bold"><?= isset($receipt->id_receipt) ? $receipt->id_receipt : 'N/A' ?></span>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold"><?= isset($receipt->project_name) ? $receipt->project_name : 'N/A' ?></span>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold"><?= isset($receipt->quantity_received) ? $receipt->quantity_received : 0 ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                    $status = isset($receipt->status) ? $receipt->status : 'pending';
                                                    $badge_class = ($status === 'completed') ? 'badge-success' : (($status === 'cancelled') ? 'badge-danger' : 'badge-warning');
                                                ?>
                                                <span class="badge <?= $badge_class ?> text-white text-xs"><?= ucfirst($status) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('warehouse/finished/receipt_view/' . (isset($receipt->id_receipt) ? $receipt->id_receipt : '#')) ?>" class="btn btn-info btn-link btn-sm">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-xs text-muted">Chưa có phiếu nhập</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phiếu xuất giao hàng (60%) -->
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header card-header-success py-3 px-4">
                        <div class="row">
                            <div class="col-7 align-items-center">
                                <h6 class="mb-0">Danh sách phiếu xuất</h6>
                                <span class="text-sm mb-0">Xuất thành phẩm giao cho khách hàng</span>
                            </div>
                            <div class="col-5 text-end">
                                <a href="<?= site_url('warehouse/finished/deliveries') ?>" class="btn btn-sm btn-white mb-0 me-2" title="Xem danh sách">
                                    <i class="material-icons-round align-middle text-lg">view_list</i>
                                </a>
                                <a href="<?= site_url('warehouse/finished/deliveries/new') ?>" class="btn btn-sm btn-white mb-0" title="Tạo phiếu mới">
                                    <i class="material-icons-round align-middle text-lg">add_circle</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3 px-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <p class="text-sm text-muted mb-0">Tổng phiếu xuất</p>
                            <h6 class="mb-0 font-weight-bold">
                                <?php 
                                    if (!empty($deliveries_data)) {
                                        echo count($deliveries_data);
                                    } else {
                                        echo '0';
                                    }
                                ?>
                            </h6>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center justify-content-center mb-0 table-center table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0">Mã phiếu</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Dự án</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">SL xuất</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Trạng thái</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="pl-3">
                                    <?php if (!empty($deliveries_data)): ?>
                                        <?php $count = 0; foreach ($deliveries_data as $delivery): if ($count >= 5) break; $count++; ?>
                                        <tr>
                                            <td><span class="text-xs font-weight-bold"><?= isset($delivery->id_issue) ? $delivery->id_issue : 'N/A' ?></span></td>
                                            <td><span class="text-xs font-weight-bold"><?= isset($delivery->project_name) ? $delivery->project_name : 'N/A' ?></span></td>
                                            <td>
                                                <span class="text-xs font-weight-bold"><?= isset($delivery->quantity_issued) ? $delivery->quantity_issued : 0 ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                    $status = isset($delivery->status) ? $delivery->status : 'pending';
                                                    $badge_class = ($status === 'completed') ? 'badge-success' : (($status === 'cancelled') ? 'badge-danger' : 'badge-warning');
                                                ?>
                                                <span class="badge <?= $badge_class ?> text-white text-xs"><?= ucfirst($status) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('warehouse/finished/delivery_view/' . (isset($delivery->id_issue) ? $delivery->id_issue : '#')) ?>" class="btn btn-info btn-link btn-sm">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-xs text-muted">Chưa có phiếu xuất</td>
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
</div>