<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible text-white fade show" role="alert">
          <?php echo $this->session->flashdata('error'); ?>
          <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible text-white fade show" role="alert">
          <?php echo $this->session->flashdata('success'); ?>
          <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><?php echo $this->lang->line('material_entry'); ?> - Nhập kho</h3>
          </div>
              <div class="card-body">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">Nhập kho vật liệu</h6>
          </div>
        </div>
        <div class="card-body">
          <form id="materialEntryForm" action="<?= site_url('warehouse/save_material_entry'); ?>" method="POST" class="needs-validation" novalidate enctype="multipart/form-data" data-default-max="<?= isset($max_stock_default) ? (int)$max_stock_default : 10000; ?>">
            <div class="row">
                <div class="col-md-6">
                  <div class="input-group input-group-outline mb-4">
                    <select class="form-control" name="material_name" id="material_name" required>
                      <option value=""><?php echo $this->lang->line('select_material'); ?></option>
                      <?php if (!empty($materials) && is_array($materials) || is_object($materials)): ?>
                        <?php foreach($materials as $m): ?>
                          <?php
                            $mname = htmlspecialchars($m->material_name, ENT_QUOTES, 'UTF-8');
                            $mtype = isset($m->material_type) ? htmlspecialchars($m->material_type, ENT_QUOTES, 'UTF-8') : '';
                            $munit = isset($m->unit) ? htmlspecialchars($m->unit, ENT_QUOTES, 'UTF-8') : '';
                          ?>
                          <option value="<?= $mname ?>" data-type="<?= $mtype ?>" data-unit="<?= $munit ?>"><?= $mname ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">
                      Vui lòng chọn vật liệu
                    </div>
                  </div>
              </div>
                  <div class="col-md-2">
                    <div class="input-group input-group-outline mb-4">
                      <input type="number" class="form-control" name="quantity" required min="1" placeholder="Số lượng">
                      <div class="invalid-feedback">
                        Số lượng phải lớn hơn 0
                      </div>
                    </div>
                  </div>
                  <div class="col-md-2 d-none">
                    <div class="input-group input-group-outline mb-4">
                      <!-- Display material type (readonly) - hidden visually, hidden input kept -->
                      <input type="text" id="display_material_type" class="form-control" placeholder="Loại" readonly>
                      <input type="hidden" name="material_type" id="material_type" value="">
                    </div>
                  </div>
                  <div class="col-md-2 d-none">
                    <div class="input-group input-group-outline mb-4">
                      <!-- Display unit (readonly) - hidden visually, hidden input kept -->
                      <input type="text" id="display_unit" class="form-control" placeholder="Đơn vị" readonly>
                      <input type="hidden" name="unit" id="unit" value="">
                    </div>
                  </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="input-group input-group-outline mb-4">
                  <input type="date" class="form-control" name="date_entry" required placeholder="Ngày nhập">
                </div>
              </div>
              <div class="col-md-4">
                <div class="input-group input-group-outline mb-4">
                  <input type="text" class="form-control" name="supplier" placeholder="<?php echo $this->lang->line('supplier'); ?>">
                </div>
              </div>
              <div class="col-md-4">
                <div class="input-group input-group-outline mb-4">
                  <input type="file" class="form-control" name="attachment">
                </div>
              </div>
            </div>

              <div class="row">
                <div class="col-12">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="confirm_over_max" id="confirm_over_max">
                    <label class="form-check-label" for="confirm_over_max">Tôi xác nhận muốn nhập vượt mức quy định (nếu có)</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="confirm_unit_mismatch" id="confirm_unit_mismatch">
                    <label class="form-check-label" for="confirm_unit_mismatch">Tôi xác nhận đơn vị nhập có thể khác (ví dụ: đặc thù)</label>
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
              <a href="<?= base_url('warehouse/material_entry') ?>" class="btn btn-default"><?php echo $this->lang->line('cancel'); ?></a>
                        </form>
                        
                        <!-- Confirmation Modal -->
                        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Xác nhận hành động</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div id="confirmModalMessages"></div>
                                <p>Bạn có chắc muốn tiếp tục?</p>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                                <button type="button" id="confirmModalContinue" class="btn btn-primary">Tiếp tục</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
 
<script>
  (function(){
    // Client-side confirmation before submitting the material entry form
    var form = document.getElementById('materialEntryForm');
    if(!form) return;

    var modalEl = document.getElementById('confirmModal');
    var bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;
    var messagesEl = document.getElementById('confirmModalMessages');
    var continueBtn = document.getElementById('confirmModalContinue');

    // default threshold from data attribute (fallback to 10000)
    var defaultMax = parseInt(form.getAttribute('data-default-max') || '10000', 10);

    // When material selection changes, populate the readonly/display fields and hidden inputs
    var materialSelect = form.querySelector('#material_name');
    var displayType = form.querySelector('#display_material_type');
    var hiddenType = form.querySelector('#material_type');
    var displayUnit = form.querySelector('#display_unit');
    var hiddenUnit = form.querySelector('#unit');

    function populateFromSelection(){
      if(!materialSelect) return;
      var opt = materialSelect.options[materialSelect.selectedIndex];
      var dtype = opt ? (opt.getAttribute('data-type') || '') : '';
      var dunit = opt ? (opt.getAttribute('data-unit') || '') : '';
      if(displayType) displayType.value = dtype;
      if(hiddenType) hiddenType.value = dtype;
      if(displayUnit) displayUnit.value = dunit;
      if(hiddenUnit) hiddenUnit.value = dunit;
    }

    if(materialSelect){
      materialSelect.addEventListener('change', populateFromSelection);
      // populate initially if an option is preselected
      populateFromSelection();
    }

    form.addEventListener('submit', function(e){
      // perform quick client-side checks; if any warning, show modal
      var materialType = (hiddenType && hiddenType.value) ? hiddenType.value : '';
      var unit = (hiddenUnit && hiddenUnit.value) ? hiddenUnit.value : '';
      var qty = parseFloat((form.querySelector('input[name="quantity"]')||{value:0}).value) || 0;

      var warnings = [];

      // unit mismatch warning (example rule: Ink + unit 'cái' is suspicious)
      if(materialType === 'Ink' && unit === 'cái'){
        warnings.push('Đơn vị "cái" có vẻ không phù hợp cho loại Mực (Ink).');
      }

      // over-max warning
      if(qty > defaultMax){
        warnings.push('Số lượng nhập ('+ qty +') vượt mức quy định ('+ defaultMax +').');
      }

      if(warnings.length === 0){
        // no warnings, allow submit
        return true;
      }

      // prevent actual submit and show modal with warnings
      e.preventDefault();
      e.stopPropagation();

      if(messagesEl){
        messagesEl.innerHTML = '<ul style="margin:0;padding-left:18px">' + warnings.map(function(w){ return '<li>'+ w +'</li>'; }).join('') + '</ul>';
      }

      if(bsModal){
        bsModal.show();
      } else {
        // fallback to browser confirm
        var ok = confirm(warnings.join('\n') + '\n\nBạn có muốn tiếp tục?');
        if(ok){
          // tick confirm checkboxes if present
          var over = form.querySelector('input[name="confirm_over_max"]'); if(over) over.checked = true;
          var unitc = form.querySelector('input[name="confirm_unit_mismatch"]'); if(unitc) unitc.checked = true;
          form.submit();
        }
      }
    });

    if(continueBtn){
      continueBtn.addEventListener('click', function(){
        // mark confirmations and submit
        var over = form.querySelector('input[name="confirm_over_max"]'); if(over) over.checked = true;
        var unitc = form.querySelector('input[name="confirm_unit_mismatch"]'); if(unitc) unitc.checked = true;
        if(bsModal) bsModal.hide();
        form.submit();
      });
    }
  })();
</script>
