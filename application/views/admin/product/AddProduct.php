<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_products'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('breadcrumb_products'); ?></h6>
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
                    <h4 class="mb-0"><?= lang('title_add_product'); ?></h4>
                    <span class="text-sm mb-0 text-end"><?= lang('subtitle_add_product'); ?></span>
                </div>
            </div>
            <div class="d-flex pt-4" method="post">
                <div class="col-8">
                    <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
                    <form class="pt-4" action="<?= site_url('admin/addProduct'); ?>" method="post">
                        <span><?= lang('form_product_name'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="text" name="product_name" class="form-control">
                        </div>
                        <span><?= lang('form_product_info'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <textarea type="textarea" name="summary" class="form-control"></textarea>
                        </div>
                        <span><?= lang('form_ink_color'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="text" name="application" class="form-control" placeholder="VD: Xanh, Đen, Đỏ, Nhiều màu">
                        </div>
                        <span><?= lang('label_diameter'); ?> (<?= lang('unit_mm'); ?>)</span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <label class="form-label"></label>
                            <input type="number" name="diameter" class="form-control" placeholder="VD: 0.5, 0.7, 1.0" step="0.1" min="0.1" max="2.0" value="0.5">
                        </div>
                        <small class="text-muted">Nhập đường kính bi viết (mm). Phổ biến: 0.5mm, 0.7mm, 1.0mm</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2">
                        <span><?= lang('msg_confirm_save_product'); ?></span></br>
                    </div>
                    <div class="d-flex">
                        <div class="pt-2 pl-2">
                            <a class="btn btn-outline-dark btn-sm mb-0" href="<?= site_url('admin/product'); ?>"><?= lang('btn_back'); ?></a>
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