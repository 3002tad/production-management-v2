<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Technical Staff Report Form -->
<!-- This form is shown ONLY to technical/technical_staff users -->
<!-- Submitting this does NOT change incident status, so leader can still see it in dashboard -->

<div class="card mb-4" id="report-form-wrapper">
  <div class="card-header bg-light">
    <h6>Gửi Báo Cáo Sửa Chữa (Technical)</h6>
  </div>
  <div class="card-body">
    <form method="post" action="<?= site_url('uc16_gn_dp/submit_report'); ?>">
      <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
      <input type="hidden" name="action_type" value="assign_technical">
      
      <div class="row mb-3">
        <div class="col-md-12">
          <label>Thời gian sửa chữa (dự kiến / thực tế)</label>
          <textarea name="shift_info" class="form-control" rows="2" placeholder="Ví dụ: Sửa chữa từ 10:00 - 12:30, Thời gian: 2.5 giờ" required></textarea>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <label>Chi tiết báo cáo sửa chữa</label>
          <textarea name="notes" class="form-control" rows="4" placeholder="Mô tả chi tiết các công việc sửa chữa đã thực hiện, vấn đề phát hiện, giải pháp áp dụng..." required></textarea>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-paper-plane"></i> Gửi Báo Cáo
        </button>
      </div>
    </form>
  </div>
</div>
