<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_material'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('btn_add_new_material'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
            </div>
        </div>
    </nav>
</br>
<div class="d-flex justify-content-center">
    <div class="col-lg-10 col-md-12">
        <div class="card">
        <div class="card-header card-header-primary">
            <div class="row">
                <div class="col-7 align-items-center pl-4">
                    <h4 class="mb-0"><?= lang('title_create_material_data'); ?></h4>
                    <span class="text-sm mb-0 text-end"><?= lang('subtitle_create_new_data'); ?></span>
                </div>
            </div>
            <div class="d-flex pt-4" method="post">
                <div class="col-8">
                    <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
                    <form class="pt-4" action="<?= site_url('warehouse/addnewmaterial'); ?>" method="post">
                        <span><?= lang('table_code'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="text" name="id_material" class="form-control" placeholder="VD: 1001">
                        </div>
                        <span><?= lang('form_material_name'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="text" name="material_name" class="form-control">
                        </div>
                        <span><?= isset($this) && function_exists('lang') ? lang('label_uom') : 'Đơn vị tính'; ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <select name="uom" id="uom-select" class="form-control">
                                <option value="pcs">pcs</option>
                                <option value="kg">kg</option>
                                <option value="g" selected>g</option>
                                <option value="m">m</option>
                                <option value="cm">cm</option>
                                <option value="box">box</option>
                            </select>
                        </div>
                        <span><?= lang('label_min_stock'); ?> (<span id="min-stock-uom-label"><?= lang('unit_gram'); ?></span>)</span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="number" name="min_stock" class="form-control" placeholder="VD: 1000" min="0">
                        </div>
                        <span><?= lang('table_stock'); ?> (<span id="stock-uom-label"><?= lang('unit_gram'); ?></span>)</span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="number" name="stock" class="form-control" placeholder="VD: 5000, 10000">
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2">
                        <span><?= lang('msg_confirm_save_data'); ?></span></br>
                    </div>
                    <div class="d-flex">
                        <div class="pt-2 pl-2">
                            <a class="btn btn-outline-dark btn-sm mb-0" href="<?= site_url('warehouse/material'); ?>"><?= lang('btn_back'); ?></a>
                        </div>
                        <div class="pt-2 pl-2">
                            <button class="btn btn-dark btn-sm mb-0" type="submit"><?= lang('btn_save'); ?></button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
        </div>
    </div>
<div>
<script>
    (function() {
        var select = document.getElementById('uom-select');
        if (!select) return;
        var stockLbl = document.getElementById('stock-uom-label');
        var minLbl = document.getElementById('min-stock-uom-label');
        var labels = {
            'g': '<?= lang('unit_gram'); ?>',
            'kg': '<?= lang('unit_kilogram'); ?>',
            'pcs': '<?= lang('unit_pieces'); ?>',
            'm': '<?= lang('unit_meter'); ?>',
            'cm': '<?= lang('unit_centimeter'); ?>',
            'box': '<?= lang('unit_box'); ?>'
        };
        function update() {
            var v = select.value;
            var txt = labels[v] || v;
            if (stockLbl) stockLbl.textContent = txt;
            if (minLbl) minLbl.textContent = txt;
        }
        select.addEventListener('change', update);
        update();
    })();
</script>