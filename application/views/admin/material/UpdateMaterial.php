<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_material'); ?></li>
      </ol>
      <h6 class="font-weight-bolder mb-0"><?= lang('label_update_material'); ?></h6>
    </nav>
  </div>
</nav>
</br>
<div class="d-flex justify-content-center">
  <div class="col-lg-8 col-md-12">
    <div class="card">
      <div class="card-header card-header-primary">
        <div class="row">
          <div class="col-7 align-items-center pl-4">
            <h4 class="mb-0"><?= lang('title_update_material_data') ?? 'Update Material'; ?></h4>
            <span class="text-sm mb-0 text-end"><?= lang('subtitle_update_data') ?? ''; ?></span>
          </div>
        </div>
        <div class="d-flex pt-4">
          <div class="col-12">
            <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
              <form class="pt-4" action="<?= site_url('admin/updateMaterial'); ?>" method="post">
                <!-- Mã cũ để locate record -->
                <input type="hidden" name="old_id_material" value="<?= $detail['id_material'] ?>">
                <span><?= lang('table_code'); ?></span></br>
                <div class="input-group input-group-dynamic mb-4">
                  <label class="form-label"></label>
                  <!-- Mã mới (có thể giữ nguyên) -->
                  <input type="text" name="id_material" value="<?= $detail['id_material'] ?>" class="form-control" required>
                </div>
                <span><?= lang('form_material_name'); ?></span></br>
                <div class="input-group input-group-dynamic mb-4">
                  <label class="form-label"></label>
                  <input type="text" name="material_name" value="<?= $detail['material_name'] ?>" class="form-control" required>
                </div>
                <span><?= isset($this) && function_exists('lang') ? lang('label_uom') : 'Đơn vị tính'; ?></span></br>
                <div class="input-group input-group-dynamic mb-4">
                  <label class="form-label"></label>
                  <?php $u = isset($detail['uom']) ? $detail['uom'] : 'g'; ?>
                  <select name="uom" class="form-control">
                    <option value="pcs" <?= ($u==='pcs')?'selected':''; ?>>pcs</option>
                    <option value="kg" <?= ($u==='kg')?'selected':''; ?>>kg</option>
                    <option value="g" <?= ($u==='g')?'selected':''; ?>>g</option>
                    <option value="m" <?= ($u==='m')?'selected':''; ?>>m</option>
                    <option value="cm" <?= ($u==='cm')?'selected':''; ?>>cm</option>
                    <option value="box" <?= ($u==='box')?'selected':''; ?>>box</option>
                  </select>
                </div>
                <?php $u_init = isset($detail['uom']) ? $detail['uom'] : 'g';
                  switch ($u_init) {
                    case 'kg': $u_init_label = lang('unit_kilogram'); break;
                    case 'g': $u_init_label = lang('unit_gram'); break;
                    case 'pcs': $u_init_label = lang('unit_pieces'); break;
                    case 'm': $u_init_label = lang('unit_meter'); break;
                    case 'cm': $u_init_label = lang('unit_centimeter'); break;
                    case 'box': $u_init_label = lang('unit_box'); break;
                    default: $u_init_label = $u_init; break;
                  }
                ?>
                <span><?= lang('table_stock'); ?> (<span id="stock-uom-label"><?= $u_init_label; ?></span>)</span></br>
                <div class="input-group input-group-dynamic mb-4">
                  <label class="form-label"></label>
                  <input type="number" name="stock" value="<?= $detail['stock'] ?>" class="form-control" min="0" required>
                </div>
                <span><?= lang('label_min_stock'); ?> (<span id="min-stock-uom-label"><?= $u_init_label; ?></span>)</span></br>
                <div class="input-group input-group-dynamic mb-4">
                  <label class="form-label"></label>
                  <input type="number" name="min_stock" value="<?= isset($detail['min_stock']) ? $detail['min_stock'] : 0 ?>" class="form-control" min="0">
                </div>
                <div class="d-flex">
                  <div class="pt-2 pl-2">
                    <a class="btn btn-outline-dark btn-sm mb-0" href="<?= site_url('admin/material'); ?>"><?= lang('btn_back'); ?></a>
                  </div>
                  <div class="pt-2 pl-2">
                    <button class="btn btn-dark btn-sm mb-0" type="submit"><?= lang('btn_save'); ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div>
<script>
  (function() {
    var select = document.querySelector('select[name="uom"]');
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