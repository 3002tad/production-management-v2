<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_production'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('label_sorting_production'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('label_details_production'); ?></h6>
            </div>
        </div>
    </nav>
</br>
    <!-- End Navbar -->
    <div class="container-fluid ">
    <div class="pb-4">
        <div class="row">
            <div class="d-flex align-items-center">
                <h4 class="mb-0"><?= lang('label_production_sorting'); ?></h4>
            </div>
        </div>
    </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="row">
            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card">
                <div class="card-header pb-0 p-3">
                  <div class="row d-flex">
                      <div class="col pb-2 pl-4 align-items-center">
                      <span class="text-lg"><?= lang('label_shiftment_head'); ?> :  <b><?= $detail['staff_name']?></b><br/></span>
                      <span class="text-sm mb-0"><?= lang('label_detail_on_production'); ?></span>
                      </div>
                  </div>
            </div>
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-md mb-md-0 mb-4">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                        <div class=" d-flex flex-column">
                            <span class="mb-3 text-sm"><?= lang('label_planning'); ?> : </br><b><?= $detail['plan_name']?></b></span>
                            <span class="mb-3 text-sm"><?= lang('label_product'); ?> : </br><b><?= $detail['product_name']?></b></span>
                        </div>
                        <div class="pl-5 d-flex flex-column">
                        <span class="mb-3 text-sm"><?= lang('label_diameter'); ?> : </br><b><?= $detail['diameter']?> mm</b></span>
                            <span class="mb-3 text-sm"><?= lang('label_shiftment'); ?> : </br><b><?= $detail['shift_name']?></b></span>
                        </div>
                        <div class="pl-5 d-flex flex-column">
                            <span class="mb-3 text-sm"><?= lang('label_start_date'); ?> : </br><b><?= $detail['start_date']?></b></span>
                            <span class="mb-3 text-sm"><?= lang('label_target'); ?> : </br><b><?= $detail['qty_target']?> Kg/Shift</b></span>
                        </div>

                        </li>
                    </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card pb-4">
          <form action="<?= site_url('admin/addsorting2/'); ?>" method="post">

            <div class="card-header pb-0 p-3">
            <div class="row">
                <div class="col-8 pl-4 align-items-center">
                    <span class="text-lg mb-0"><b><?= lang('label_sorting_this_production'); ?></b></span></br>
                    <span class="text-sm mb-0"><?= lang('label_input_result_production'); ?></span>
                </div>
                <div class="col-4 text-end">
                    <button type="submit" class="btn btn-info btn-sm mb-0"><?= lang('btn_complete'); ?></button>
                </div>
            </div>
            <div class="card-body p-3 pb-0">
            <div class="d-flex pt-2" method="post">
                    <div class="row d-flex">
                        <div class="col-5">
                            <span><?= lang('label_finished_goods'); ?></span></br>
                            <div class="input-group input-group-dynamic mb-4">
                                <label class="form-label"></label>
                                <input type="number" name="finished" class="form-control">
                                <input type="hidden" name="ps_status" value="0">
                                <input type="hidden" name="id_planshift" value="<?= $detail['id_planshift']?>">
                                <p class="text-end pt-2">Kg</p>
                            </div>
                        </div>
                        <div class="col-2">
                        </div>
                        <div class="col-5 pl-3">
                            <span><?= lang('label_production_waste'); ?></span></br>
                            <div class="input-group input-group-dynamic mb-4">
                                <label class="form-label"></label>
                                <input type="number" name="waste" class="form-control">
                                <p class="text-end pt-2">Kg</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
          </div>
        </div>
        
      </div>
    </div>
