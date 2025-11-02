<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - QC Module</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700,900" />
    
    <!-- Material Dashboard CSS -->
    <link href="<?= site_url('asset/backend/assets/css/material-dashboard.css?v=3.0.0'); ?>" rel="stylesheet" />
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    
    <style>
        :root {
            --qc-primary: #344767;
            --qc-secondary: #1A73E8;
            --qc-success: #4CAF50;
            --qc-warning: #fb8c00;
            --qc-danger: #F44335;
            --qc-info: #00bcd4;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .stat-card {
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 26px -4px rgba(20, 20, 20, 0.15), 0 8px 9px -5px rgba(20, 20, 20, 0.06);
        }
        
        .stat-card .icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(64, 64, 64, 0.4);
        }
        
        .stat-card .icon-wrapper i {
            font-size: 32px;
            color: white;
        }
        
        .stat-number {
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.375;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #7b809a;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.0625rem;
        }
        
        .stat-sublabel {
            font-size: 0.875rem;
            color: #67748e;
            margin-top: 0.5rem;
        }
        
        .table-container {
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(195deg, #42424a 0%, #191919 100%);
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.0625rem;
            padding: 1rem;
            border: none;
        }
        
        .table tbody tr {
            transition: background-color 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        .badge-status {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        
        .btn-inspect {
            background: linear-gradient(195deg, var(--qc-secondary) 0%, #1662C4 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.15s ease-in;
        }
        
        .btn-inspect:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.12), 0 7px 10px -5px rgba(26, 115, 232, 0.4);
            color: white;
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
