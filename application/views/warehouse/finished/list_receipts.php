<div class="container-fluid py-4">
  <h3>Danh sách phiếu nhập</h3>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $this->session->flashdata('success'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
      <?= $this->session->flashdata('error'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (empty($receipts)): ?>
    <div class="alert alert-info">Chưa có phiếu nhập nào.</div>
  <?php else: ?>
    <table class="table table-striped">
      <thead><tr><th>Phiếu</th><th>Lô/Ca</th><th>SL</th><th>Ngày</th><th>Người lập</th></tr></thead>
      <tbody>
        <?php foreach($receipts as $r): ?>
          <tr>
            <td><a href="<?= site_url('warehouse/finished/receipt/'.$r->id); ?>"><?= $r->id; ?></a></td>
            <td><?= $r->closure_id; ?></td>
            <td><?= $r->qty; ?></td>
            <td><?= $r->created_at; ?></td>
            <td><?= $r->created_by; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>