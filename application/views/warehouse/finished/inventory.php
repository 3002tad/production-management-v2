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
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tồn kho thành phẩm</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Tồn kho thành phẩm</h6>
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

<!-- Finished Products Inventory -->
<div class="container-fluid mt-4">
    <br/>

    <!-- Card tiêu đề -->
    <div class="container-fluid pt-0 px-4">
        <div class="card-header p-0 mt-n4 mx-2 z-index-2">
            <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
                <div class="col-8 d-flex align-items-center">
                    <i class="material-icons pr-3 text-lg">inventory</i>
                    <h6 class="mb-0 pr-4">Tồn kho thành phẩm</h6>
                </div>
                <div class="col-4 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <span class="badge bg-success text-white">
                            <i class="material-icons" style="font-size: 16px; vertical-align: middle;">check_circle</i>
                            Tổng: <strong><?= $total_quantity ?></strong> cái
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Card -->
    <div class="container-fluid py-4 pt-2 px-4">
        <div class="row mx-1">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-header-success py-3 px-4">
                        <div class="row">
                            <div class="col-8 align-items-center">
                                <h6 class="mb-0">Danh sách thành phẩm trong kho</h6>
                                <span class="text-sm mb-0">Hiện tại có <?= count($inventory) ?> sản phẩm</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3 px-4 pb-3">
                        <div class="table-responsive p-0">
                            <table id="inventory-table" class="table align-items-center justify-content-center mb-0 table-center">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0">STT</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mã sản phẩm</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tên sản phẩm</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Số lượng tồn (cái)</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Cập nhật lần cuối</th>
                                    </tr>
                                </thead>
                                <tbody class="pl-3">
                                    <?php if (!empty($inventory)): ?>
                                        <?php $i = 1; foreach ($inventory as $item): ?>
                                        <tr>
                                            <td>
                                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                                            </td>
                                            <td class="pl-4">
                                                <span class="text-sm font-weight-bold">
                                                    <?= isset($item->product_code) ? $item->product_code : 'N/A' ?>
                                                </span>
                                            </td>
                                            <td class="pl-4">
                                                <span class="text-sm font-weight-bold">
                                                    <?= isset($item->product_name) ? $item->product_name : 'N/A' ?>
                                                </span>
                                            </td>
                                            <td class="pl-4">
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                        $qty = isset($item->quantity_in_stock) ? intval($item->quantity_in_stock) : 0;
                                                        if ($qty > 100) {
                                                            $badge_class = 'badge-success';
                                                        } elseif ($qty > 10) {
                                                            $badge_class = 'badge-info';
                                                        } elseif ($qty > 0) {
                                                            $badge_class = 'badge-warning';
                                                        } else {
                                                            $badge_class = 'badge-danger';
                                                        }
                                                    ?>
                                                    <span class="badge <?= $badge_class ?> text-white">
                                                        <?= $qty ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="pl-4">
                                                <span class="text-xs font-weight-bold text-muted">
                                                    <?= isset($item->last_updated) ? date('d/m/Y H:i', strtotime($item->last_updated)) : 'N/A' ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <span class="text-sm text-muted">
                                                    <i class="material-icons" style="font-size: 40px; opacity: 0.3;">info</i>
                                                    <p>Không có dữ liệu tồn kho</p>
                                                </span>
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

    <!-- Summary Card -->
    <div class="container-fluid py-4 px-4">
        <div class="row mx-1">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-sm text-secondary mb-1">Tổng sản phẩm</p>
                                <h4 class="font-weight-bold"><?= count($inventory) ?></h4>
                            </div>
                            <div class="ms-auto">
                                <i class="material-icons text-lg text-info opacity-10">category</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-sm text-secondary mb-1">Tổng số lượng</p>
                                <h4 class="font-weight-bold"><?= $total_quantity ?> cái</h4>
                            </div>
                            <div class="ms-auto">
                                <i class="material-icons text-lg text-success opacity-10">done_all</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-sm text-secondary mb-1">Bình quân</p>
                                <h4 class="font-weight-bold">
                                    <?= (count($inventory) > 0) ? round($total_quantity / count($inventory), 0) : 0 ?> cái
                                </h4>
                            </div>
                            <div class="ms-auto">
                                <i class="material-icons text-lg text-warning opacity-10">trending_up</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add DataTable for better table management
    if (typeof jQuery !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
        $('#inventory-table').DataTable({
            pageLength: 25,
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Vietnamese.json"
            }
        });
    }
});
</script>
