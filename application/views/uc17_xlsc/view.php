<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6>Chi tiết sự cố #<?= $incident->id; ?></h6>
          <div>
            <a href="<?= site_url('uc17_xlsc'); ?>" class="btn btn-sm btn-secondary">Quay lại</a>
          </div>
        </div>
        <div class="card-body px-5 py-4">
          <!-- Location & Hierarchy -->
          <div class="card bg-light mb-4">
            <div class="card-body">
              <h6 class="mb-3"><i class="fas fa-map-marker-alt"></i> Vị trí & Thông tin</h6>
              <div class="row">
                <div class="col-md-3">
                  <small class="text-muted">Ca làm việc</small>
                  <?php if (!empty($incident->shift_code)): ?>
                    <p class="mb-0"><span class="badge badge-info"><?= htmlspecialchars($incident->shift_code); ?></span></p>
                    <small><?= htmlspecialchars($incident->shift_name ?? ''); ?></small>
                  <?php else: ?>
                    <p class="text-muted"><em>Chưa xác định</em></p>
                  <?php endif; ?>
                </div>
                <div class="col-md-3">
                  <small class="text-muted">Khu vực</small>
                  <p class="mb-0"><?= htmlspecialchars($incident->zone_name ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                  <small class="text-muted">Dây chuyền</small>
                  <p class="mb-0"><span class="badge badge-secondary"><?= htmlspecialchars($incident->line_code ?? 'N/A'); ?></span></p>
                  <small><?= htmlspecialchars($incident->line_name ?? ''); ?></small>
                </div>
                <div class="col-md-3">
                  <small class="text-muted">Máy móc</small>
                  <?php if (!empty($incident->machine_code)): ?>
                    <p class="mb-0"><span class="badge badge-dark"><?= htmlspecialchars($incident->machine_code); ?></span></p>
                    <small><?= htmlspecialchars($incident->machine_name ?? ''); ?></small>
                  <?php else: ?>
                    <p class="text-warning"><em>Toàn dây chuyền</em></p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <h6>Trạng thái</h6>
              <p><?= ($incident->status==1)?'Đã hoàn thành':(($incident->status==2)?'Đang xử lý':'Chưa hoàn thành'); ?></p>
            </div>
          </div>

          <div class="card bg-light mb-4">
            <div class="card-body">
              <h6>Mô tả</h6>
              <p><?= nl2br(htmlspecialchars($incident->incident_description)); ?></p>
            </div>
          </div>

          <!-- Coordination / Progress History -->
          <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h6 class="mb-0">Tiến Độ & Lịch Sử Xử Lý</h6>
            </div>
            <div class="card-body">
              <?php if (!empty($coordination)): foreach ($coordination as $c): ?>
                <div class="mb-3 border p-3" style="background-color:#f8f9fa;border-radius:4px;">
                  <div class="d-flex justify-content-between">
                    <div>
                      <strong><?= htmlspecialchars($c->action_type); ?></strong>
                      <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($c->created_at)); ?></div>
                    </div>
                    <div>
                      <span class="badge badge-<?= ($c->status=='in_progress' || $c->status=='submitted')?'warning':'secondary'; ?>"><?php echo htmlspecialchars($c->status ?? ''); ?></span>
                    </div>
                  </div>
                  <div class="mt-2">
                    <?php if (!empty($c->shift_info)): ?><strong>Info:</strong> <?= nl2br(htmlspecialchars($c->shift_info)); ?><br><?php endif; ?>
                    <?php if (!empty($c->notes)): ?><strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($c->notes)); ?><?php endif; ?>
                  </div>
                </div>
              <?php endforeach; else: ?>
                <div class="alert alert-info">Chưa có tiến độ xử lý nào.</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Technical-only actions -->
          <?php if (isset($user_role) && in_array($user_role, ['technical','technical_staff'])): ?>
            <div class="card mb-4">
              <div class="card-header bg-light"><h6>Gửi Thời Gian Dự Kiến Hoàn Thành</h6></div>
              <div class="card-body">
                <form method="post" action="<?= site_url('uc17_xlsc/submit_estimate'); ?>">
                  <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label>Thời gian dự kiến (vd: 2025-12-08 15:30 hoặc '2 giờ')</label>
                      <input type="text" name="estimated_completion" class="form-control" required>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label>Ghi chú (tùy chọn)</label>
                      <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                  </div>
                  <button class="btn btn-primary">Gửi Thời Gian Dự Kiến</button>
                </form>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header bg-light"><h6>Cập Nhật Tiến Độ</h6></div>
              <div class="card-body">
                <form method="post" action="<?= site_url('uc17_xlsc/update_progress'); ?>">
                  <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label>Tiến độ (%)</label>
                      <input type="number" name="percent" min="0" max="100" value="0" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                      <label>Ghi chú</label>
                      <input type="text" name="notes" class="form-control">
                    </div>
                  </div>
                  <button class="btn btn-warning">Cập Nhật Tiến Độ</button>
                </form>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header bg-light"><h6>Ghi Nhận Hoàn Tất Sửa Chữa (Kỹ Thuật)</h6></div>
              <div class="card-body">
                <form method="post" action="<?= site_url('uc17_xlsc/mark_repair_done'); ?>">
                  <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label>Ghi chú (tùy chọn)</label>
                      <textarea name="notes" class="form-control"></textarea>
                    </div>
                  </div>
                  <button class="btn btn-success">Ghi nhận hoàn tất (chờ Leader xác nhận)</button>
                </form>
              </div>
            </div>
          <?php else: ?>
            <div class="alert alert-info">Bạn đang xem tiến độ; chỉ Technical mới có quyền cập nhật.</div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
