<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_project'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('breadcrumb_project'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
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
                    <h4 class="mb-0"><?= lang('title_update_project'); ?></h4>
                    <span class="text-sm mb-0 text-end"><?= lang('subtitle_update_project'); ?></span>
                </div>
            </div>
            <div class="d-flex pt-4" method="post">
                <div class="col-8">
                    <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
                    <form class="pt-4" action="<?= site_url('admin/updateProject'); ?>" method="post">
                    
                        <div class="row d-flex">
                            <div class="col-4">
                                <span><?= lang('form_project_name'); ?></span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <label class="form-label"></label>
                                    <input type="hidden" name="id_project" value="<?= $detail['id_project']; ?>">
                                    <input type="text" name="project_name" value="<?= $detail['project_name']?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-1">
                            </div>
                            <div class="col-7">
                            <span><?= lang('form_date'); ?></span></br>
                            <div class="input-group input-group-dynamic mb-4">
                                <label class="form-label"></label>
                                <input type="date" name="entry_date" value="<?= $detail['entry_date']?>" class="form-control">
                            </div>
                            </div>
                        </div>
                        <span><?= lang('menu_customer'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <select class="selectpicker form-control" name="id_cust" data-style="btn btn-link" data-live-search="true">
                                <option disabled selected><?= lang('form_select_customer'); ?></option>
                                <?php if (!empty($customer)) : $i = 1; foreach ($customer as $value) : ?>
                                    <option value="<?= $value->id_cust; ?>" <?= $detail['id_cust'] === $value->id_cust ? 'selected' : ''; ?> ><?= $value->cust_name; ?></option> 
                                <?php endforeach; endif; ?>
                            </select>                      
                        </div>
                        <span><?= lang('menu_product'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <select class="selectpicker form-control" id="product_select_update" name="id_product" data-style="btn btn-link" data-live-search="true">
                                <option disabled selected><?= lang('form_select_product'); ?></option>
                                <?php if (!empty($product)) : $i = 1; foreach ($product as $value) : ?>
                                    <option value="<?= $value->id_product; ?>" data-diameter="<?= $value->diameter; ?>" <?= $detail['id_product'] === $value->id_product ? 'selected' : ''; ?>><?= $value->product_name; ?></option> 
                                <?php endforeach; endif; ?>
                            </select>    
                        </div>
                        <div class="row d-flex">
                            <div class="col-5">
                                <span><?= lang('form_quantity'); ?></span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <label class="form-label"></label>
                                    <input type="text" name="qty_request" value="<?= $detail['qty_request']?>" class="form-control">
                                    <p class="text-end pt-2"><?= lang('unit_pieces'); ?></p>
                                </div>
                            </div>
                            <div class="col-2">
                            </div>
                            <div class="col-5 pl-3">
                                <span><?= lang('form_diameter'); ?></span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <label class="form-label"></label>
                                    <input type="number" step="0.1" id="diameter_input_update" name="diameter" value="<?= $detail['diameter']?>" class="form-control">
                                    <p class="text-end pt-2"><?= lang('unit_mm'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="pr-2">
                        <span><?= lang('msg_confirm_update'); ?></span></br>
                    </div>
                    <div class="d-flex">
                        <div class="pt-2 pl-2">
                            <a class="btn btn-outline-dark btn-sm mb-0" href="<?= site_url('admin/project'); ?>"><?= lang('btn_back'); ?></a>
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
// Auto-fill diameter khi thay đổi product trong Update form
$(document).ready(function() {
    $('#product_select_update').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var diameter = selectedOption.data('diameter');
        
        if (diameter) {
            $('#diameter_input_update').val(diameter);
            $('#diameter_input_update').addClass('is-valid');
            setTimeout(function() {
                $('#diameter_input_update').removeClass('is-valid');
            }, 1500);
        }
    });
    
    $('.selectpicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        if ($(this).attr('id') === 'product_select_update') {
            var selectedOption = $(this).find('option:selected');
            var diameter = selectedOption.data('diameter');
            
            if (diameter) {
                $('#diameter_input_update').val(diameter);
                $('#diameter_input_update').addClass('is-valid');
                setTimeout(function() {
                    $('#diameter_input_update').removeClass('is-valid');
                }, 1500);
            }
        }
    });
});
</script>