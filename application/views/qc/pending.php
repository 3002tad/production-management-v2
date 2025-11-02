<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - QC Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --qc-primary: #2c3e50;
            --qc-secondary: #16a085;
            --qc-warning: #f39c12;
            --qc-danger: #e74c3c;
            --qc-success: #27ae60;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ecf0f1;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: var(--qc-primary);
            color: white;
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .sidebar .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar .logo h3 {
            color: var(--qc-secondary);
            font-weight: bold;
            margin: 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(22,160,133,0.2);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        
        .top-bar {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-custom {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .badge-pending {
            background-color: var(--qc-warning);
        }
        
        .badge-verified {
            background-color: var(--qc-success);
        }
        
        .badge-rejected {
            background-color: var(--qc-danger);
        }
        
        .btn-qc {
            background: var(--qc-secondary);
            color: white;
            border: none;
        }
        
        .btn-qc:hover {
            background: #138f75;
            color: white;
        }
        
        .filter-panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-check-circle"></i> QC Module</h3>
            <small>Quality Control</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="<?= base_url('qc/') ?>">
                    <i class="fas fa-clipboard-list"></i> Pending Inspections
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('qc/adjustments') ?>">
                    <i class="fas fa-exclamation-triangle"></i> Adjustment Requests
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="<?= base_url('login/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div>
                <h4 class="mb-0"><?= $title ?></h4>
                <small class="text-muted"><?= date('l, d F Y') ?></small>
            </div>
            <div>
                <strong><?= $user['full_name'] ?></strong><br>
                <small class="text-muted"><?= $user['role_display_name'] ?></small>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filter-panel">
            <h5><i class="fas fa-filter"></i> Filters</h5>
            <form method="GET" action="<?= base_url('qc/pending') ?>" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Line Code</label>
                    <input type="text" class="form-control" name="line_code" 
                           value="<?= isset($filters['line_code']) ? $filters['line_code'] : '' ?>" 
                           placeholder="e.g., LINE-01">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Shift</label>
                    <select class="form-select" name="shift_code">
                        <option value="">All Shifts</option>
                        <option value="CA1" <?= (isset($filters['shift_code']) && $filters['shift_code'] === 'CA1') ? 'selected' : '' ?>>CA1</option>
                        <option value="CA2" <?= (isset($filters['shift_code']) && $filters['shift_code'] === 'CA2') ? 'selected' : '' ?>>CA2</option>
                        <option value="CA3" <?= (isset($filters['shift_code']) && $filters['shift_code'] === 'CA3') ? 'selected' : '' ?>>CA3</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" class="form-control" name="date_from" 
                           value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" class="form-control" name="date_to" 
                           value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-qc w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Closures Table -->
        <div class="card card-custom">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-clipboard-list"></i> Pending Shift Closures
                    <span class="badge bg-warning"><?= count($closures) ?></span>
                </h5>
                
                <?php if (empty($closures)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No pending closures found.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Line</th>
                                    <th>Shift</th>
                                    <th>Project</th>
                                    <th>Product</th>
                                    <th>Qty Finished</th>
                                    <th>Qty Waste</th>
                                    <th>Closed At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($closures as $closure): ?>
                                    <tr>
                                        <td><code><?= $closure->code ?></code></td>
                                        <td><?= $closure->line_code ?></td>
                                        <td><span class="badge bg-primary"><?= $closure->shift_code ?></span></td>
                                        <td><?= $closure->project_name ?? $closure->project_code ?></td>
                                        <td><?= $closure->product_name ?? $closure->product_code ?></td>
                                        <td><strong><?= number_format($closure->qty_finished) ?></strong></td>
                                        <td class="text-danger"><?= number_format($closure->qty_waste) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($closure->closed_at)) ?></td>
                                        <td>
                                            <span class="badge badge-pending"><?= $closure->status ?></span>
                                        </td>
                                        <td>
                                            <form method="POST" action="<?= base_url('qc/createSession') ?>" style="display:inline;">
                                                <input type="hidden" name="closure_id" value="<?= $closure->id ?>">
                                                <button type="submit" class="btn btn-qc btn-sm">
                                                    <i class="fas fa-microscope"></i> Inspect
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
