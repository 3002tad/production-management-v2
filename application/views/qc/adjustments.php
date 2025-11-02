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
        }
        
        .sidebar .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
        }
        
        .sidebar .nav-link.active {
            background: rgba(22,160,133,0.2);
            color: white;
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
        }
        
        .card-custom {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-check-circle"></i> QC Module</h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('qc/') ?>">
                    <i class="fas fa-clipboard-list"></i> Pending Inspections
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= base_url('qc/adjustments') ?>">
                    <i class="fas fa-exclamation-triangle"></i> Adjustment Requests
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h4><?= $title ?></h4>
        </div>

        <div class="card-custom">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Closure</th>
                                <th>Line</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No adjustment requests found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><code><?= $req->code ?></code></td>
                                        <td><?= $req->closure_code ?></td>
                                        <td><?= $req->line_code ?></td>
                                        <td><?= substr($req->reason, 0, 100) ?>...</td>
                                        <td><span class="badge bg-<?= $req->status === 'OPEN' ? 'danger' : ($req->status === 'ACKED' ? 'warning' : 'success') ?>"><?= $req->status ?></span></td>
                                        <td><?= date('d/m/Y H:i', strtotime($req->created_at)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
