<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Danh sách sự cố (Xử lý kỹ thuật)</h6>
          <p class="text-sm">Số lượng: <strong><?= count($incidents); ?></strong></p>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Máy</th>
                  <th>Loại</th>
                  <th>Mức Độ</th>
                  <th>Trạng Thái</th>
                  <th>Ngày Tạo</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($incidents)): foreach ($incidents as $inc): ?>
                  <tr>
                    <td class="pl-4"><?= $inc->id; ?></td>
                    <td class="pl-4"><?= $inc->id_machine; ?></td>
                    <td class="pl-4"><?= htmlspecialchars($inc->category); ?></td>
                    <td class="pl-4">Mức <?= $inc->severity_level; ?></td>
                    <td class="pl-4"><?= ($inc->status==0)?'Mới':(($inc->status==2)?'Đang xử lý':'Hoàn thành'); ?></td>
                    <td class="pl-4"><?= date('d/m/Y H:i', strtotime($inc->created_at)); ?></td>
                    <td class="pl-4">
                      <a href="<?= site_url('uc17_xlsc/view/' . $inc->id); ?>" class="btn btn-sm btn-info">Chi tiết / Xử lý</a>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="7" class="text-center py-4">Không có sự cố</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
