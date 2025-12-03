<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
      </ol>
      <h6 class="font-weight-bolder text-dark mb-0">Danh sách sự cố</h6>
    </nav>
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
      <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
    </div>
  </div>
</nav>
<!-- End Navbar -->

<div class="container-fluid py-4">
  <!-- Success Message -->
  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong><?= $this->session->flashdata('success'); ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Error Message -->
  <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong><?= $this->session->flashdata('error'); ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Danh sách báo cáo sự cố</h6>
          <p class="text-sm">
            Số lượng tổng: <strong><?= count($incidents); ?></strong> báo cáo
          </p>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0" id="dataTable">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Máy</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Loại sự cố</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mức độ</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày tạo</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hành động</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($incidents)): ?>
                  <?php foreach ($incidents as $incident): ?>
                    <tr>
                      <td class="ps-4">
                        <p class="text-xs font-weight-bold mb-0"><?= $incident->id; ?></p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?= $incident->id_machine; ?></p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">
                          <?php 
                            $category_map = ['equipment' => 'Thiết bị', 'quality' => 'Chất lượng', 'safety' => 'An toàn', 'other' => 'Khác'];
                            echo $category_map[$incident->category] ?? $incident->category;
                          ?>
                        </p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">
                          <span class="badge badge-sm bg-gradient-<?php echo ($incident->severity_level <= 2) ? 'info' : (($incident->severity_level <= 3) ? 'warning' : 'danger'); ?>">
                            Mức <?= $incident->severity_level; ?>
                          </span>
                        </p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">
                          <?php 
                            $status_map = [0 => 'Chưa hoàn thành', 1 => 'Đã hoàn thành', 2 => 'Đang xử lý'];
                            $status_color = [0 => 'danger', 1 => 'success', 2 => 'warning'];
                            echo '<span class="badge badge-sm bg-gradient-' . ($status_color[$incident->status] ?? 'secondary') . '">' . ($status_map[$incident->status] ?? 'N/A') . '</span>';
                          ?>
                        </p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0"><?= date('d/m/Y H:i', strtotime($incident->created_at)); ?></p>
                      </td>
                      <td class="align-middle">
                        <a href="<?= site_url('uc15_bcsc/technical/edit/' . $incident->id); ?>" class="btn btn-sm btn-info mb-0">
                          <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="<?= site_url('uc15_bcsc/technical/detail/' . $incident->id); ?>" class="btn btn-sm btn-primary mb-0">
                          <i class="fas fa-eye"></i> Chi tiết
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center py-4">
                      <p class="text-muted">Không có báo cáo sự cố nào</p>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#dataTable').DataTable({
      "language": {
        "search": "Tìm:",
        "lengthMenu": "Hiển thị _MENU_ mục",
        "info": "Hiển thị _START_ đến _END_ trong _TOTAL_ mục",
        "paginate": {
          "first": "Đầu",
          "last": "Cuối",
          "next": "Tiếp",
          "previous": "Trước"
        }
      }
    });
  });
</script>
