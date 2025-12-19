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

<div class="container-fluid">
    <!-- Small summary cards were removed as requested (were duplicating the lower lists) -->
    <div class="row mt-4">
            <!-- Receipt List (50% chiều rộng) -->
            <div class="col-md-6 mb-4">
                <?php $this->load->view('warehouse/finished/receipt_list'); ?>
            </div>

            <!-- Delivery List (50% chiều rộng) -->
            <div class="col-md-6 mb-4">
                <?php $this->load->view('warehouse/finished/delivery_list'); ?>
            </div>
        </div>
    <!-- Modal/Container cho Receipt Form -->
    <div id="receiptFormContainer" style="display: none; margin-top: 20px;">
        <div class="row">
            <div class="col-12">
                <?php $this->load->view('warehouse/finished/receipt_form'); ?>
            </div>
        </div>
    </div>

    <!-- Modal/Container cho Delivery Form -->
    <div id="deliveryFormContainer" style="display: none; margin-top: 20px;">
        <div class="row">
            <div class="col-12">
                <?php $this->load->view('warehouse/finished/delivery_form'); ?>
            </div>
        </div>
    </div>

    <!-- Modal/Container cho Receipt Detail -->
    <div id="receiptDetailContainer" style="display: none; margin-top: 20px;">
        <div class="row">
            <div class="col-12" id="receiptDetailContent"></div>
        </div>
    </div>

    <!-- Modal/Container cho Delivery Detail -->
    <div id="deliveryDetailContainer" style="display: none; margin-top: 20px;">
        <div class="row">
            <div class="col-12" id="deliveryDetailContent"></div>
        </div>
    </div>
</div>

<script>
// Toggle form hiển thị
function showReceiptForm() {
    document.getElementById('receiptFormContainer').style.display = 'block';
    document.getElementById('deliveryFormContainer').style.display = 'none';
    document.getElementById('receiptDetailContainer').style.display = 'none';
    document.getElementById('deliveryDetailContainer').style.display = 'none';
    // Scroll to form
    document.getElementById('receiptFormContainer').scrollIntoView({ behavior: 'smooth' });
}

function showDeliveryForm() {
    document.getElementById('deliveryFormContainer').style.display = 'block';
    document.getElementById('receiptFormContainer').style.display = 'none';
    document.getElementById('receiptDetailContainer').style.display = 'none';
    document.getElementById('deliveryDetailContainer').style.display = 'none';
    // Scroll to form
    document.getElementById('deliveryFormContainer').scrollIntoView({ behavior: 'smooth' });
}

function showReceiptDetail(id) {
    // Load chi tiết phiếu nhập via AJAX
    fetch('<?= site_url("warehouse/finished/receipt_view/") ?>' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(data => {
            document.getElementById('receiptDetailContent').innerHTML = data;
            document.getElementById('receiptDetailContainer').style.display = 'block';
            document.getElementById('deliveryDetailContainer').style.display = 'none';
            document.getElementById('receiptFormContainer').style.display = 'none';
            document.getElementById('deliveryFormContainer').style.display = 'none';
            document.getElementById('receiptDetailContainer').scrollIntoView({ behavior: 'smooth' });
        });
}

function showDeliveryDetail(id) {
    // Load chi tiết phiếu xuất via AJAX
    fetch('<?= site_url("warehouse/finished/delivery_view/") ?>' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(data => {
            document.getElementById('deliveryDetailContent').innerHTML = data;
            document.getElementById('deliveryDetailContainer').style.display = 'block';
            document.getElementById('receiptDetailContainer').style.display = 'none';
            document.getElementById('receiptFormContainer').style.display = 'none';
            document.getElementById('deliveryFormContainer').style.display = 'none';
            document.getElementById('deliveryDetailContainer').scrollIntoView({ behavior: 'smooth' });
        });
}

function hideAllContainers() {
    document.getElementById('receiptFormContainer').style.display = 'none';
    document.getElementById('deliveryFormContainer').style.display = 'none';
    document.getElementById('receiptDetailContainer').style.display = 'none';
    document.getElementById('deliveryDetailContainer').style.display = 'none';
}
</script>
<!-- Finished Goods Management Dashboard -->
<div class="container-fluid mt-4">

    <br/>

   

                </div>
            </div>
        </div>
    </div>
</div>