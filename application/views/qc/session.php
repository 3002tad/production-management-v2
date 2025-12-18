<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Hệ thống QC</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700,900" />
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    
    <!-- Material Dashboard CSS -->
    <link href="<?= site_url('asset/backend/assets/css/material-dashboard.css?v=3.0.0'); ?>" rel="stylesheet" />
    
    <style>
        .checklist-item {
            transition: all 0.3s ease;
        }
        .checklist-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .checklist-item.fail {
            background-color: #ffebee;
            border-left: 4px solid #e74c3c;
        }
        .checklist-item.pass {
            background-color: #e8f5e9;
            border-left: 4px solid #27ae60;
        }
        .recommendation-box {
            border-left: 4px solid #1A73E8;
            background: linear-gradient(195deg, rgba(26, 115, 232, 0.05) 0%, rgba(22, 98, 196, 0.05) 100%);
        }
        .recommendation-box.approve {
            border-left-color: #43A047;
            background: linear-gradient(195deg, rgba(67, 160, 71, 0.05) 0%, rgba(56, 142, 60, 0.05) 100%);
        }
        .recommendation-box.reject {
            border-left-color: #E53935;
            background: linear-gradient(195deg, rgba(229, 57, 53, 0.05) 0%, rgba(211, 47, 47, 0.05) 100%);
        }
        .attachment-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-200">

<!-- Sidebar -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="<?= site_url('qc/'); ?>">
            <span class="ms-1 font-weight-bold text-white">PRODUCTION SYSTEM</span>
        </a>
    </div>
    
    <hr class="horizontal light mt-0 mb-2">
    
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">QC - KIỂM SOÁT CHẤT LƯỢNG</h6>
            </li>
            
            <!-- Pending Inspections -->
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">pending_actions</i>
                    </div>
                    <span class="nav-link-text ms-1">Phiếu chốt ca chờ QC</span>
                </a>
            </li>
            
            <!-- My Sessions -->
            <li class="nav-item">
                <a class="nav-link text-white active bg-gradient-primary" href="<?= site_url('qc/sessions'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <span class="nav-link-text ms-1">Phiên kiểm tra của tôi</span>
                </a>
            </li>
            
            <!-- Adjustment Requests -->
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/adjustments'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">build_circle</i>
                    </div>
                    <span class="nav-link-text ms-1">Yêu cầu điều chỉnh</span>
                </a>
            </li>
            
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">BÁO CÁO</h6>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/reports'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">analytics</i>
                    </div>
                    <span class="nav-link-text ms-1">Báo cáo QC</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('qc/'); ?>">QC</a></li>
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('qc/sessions'); ?>">Phiên kiểm tra</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= $session->code ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Kiểm định chất lượng</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <!-- User info -->
                </div>
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a href="<?= site_url('login/logout'); ?>" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none"><?= $user['full_name'] ?? 'QC Inspector' ?></span>
                            <i class="material-icons ms-2">logout</i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container-fluid py-4">

    <!-- Session Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Thông tin phiếu chốt ca</h6>
                </div>
                <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Closure Code:</th>
                        <td><code><?= $session->closure_code ?></code></td>
                    </tr>
                    <tr>
                        <th>Line:</th>
                        <td><?= $session->line_code ?></td>
                    </tr>
                    <tr>
                        <th>Shift:</th>
                        <td><span class="badge bg-primary"><?= $session->shift_code ?></span></td>
                    </tr>
                    <tr>
                        <th>Project:</th>
                        <td><?= $session->project_name ?? $session->project_code ?></td>
                    </tr>
                    <tr>
                        <th>Product:</th>
                        <td><?= $session->product_name ?? $session->product_code ?></td>
                    </tr>
                    <tr>
                        <th>Variant:</th>
                        <td><?= $session->variant ?? 'Standard' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Số lượng sản xuất</h6>
                </div>
                <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Finished Goods:</th>
                        <td><strong class="text-success"><?= number_format($session->qty_finished) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Waste:</th>
                        <td><strong class="text-danger"><?= number_format($session->qty_waste) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Total Output:</th>
                        <td><strong><?= number_format($session->qty_finished + $session->qty_waste) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Session Status:</th>
                        <td>
                            <?php if ($session->status === 'OPEN'): ?>
                                <span class="badge bg-warning">OPEN - In Progress</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">DECIDED - Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendation (if available) -->
    <?php if ($recommendation && $session->status === 'OPEN'): ?>
        <div class="recommendation-box <?= strtolower($recommendation['recommendation']) === 'approve' ? 'approve' : (strtolower($recommendation['recommendation']) === 'reject' ? 'reject' : 'review') ?>">
            <i class="fas fa-lightbulb"></i> <strong>AI Recommendation:</strong> 
            <?= $recommendation['recommendation'] ?>
            <br>
            <small><?= $recommendation['analysis'] ?></small>
            <br>
            Defect Rate: <?= number_format($recommendation['defect_rate'], 2) ?>% 
            | Critical: <?= $recommendation['stats']['critical_count'] ?>
            | Major: <?= $recommendation['stats']['major_count'] ?>
            | Minor: <?= $recommendation['stats']['minor_count'] ?>
        </div>
    <?php endif; ?>

    <!-- Decision (if made) -->
    <?php if ($decision): ?>
        <div class="alert alert-<?= $decision->result === 'APPROVE' ? 'success' : 'danger' ?>">
            <h5><i class="fas fa-gavel"></i> Decision: <?= $decision->result ?></h5>
            <p><strong>Decided at:</strong> <?= date('d/m/Y H:i', strtotime($decision->decided_at)) ?></p>
            <p><strong>Defect Rate:</strong> <?= number_format($decision->defect_rate, 2) ?>% (AQL: <?= $decision->aql ?>%)</p>
            <?php if ($decision->reason): ?>
                <p><strong>Reason:</strong> <?= nl2br(htmlspecialchars($decision->reason)) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Checklist Form -->
    <div class="info-card <?= $session->status === 'DECIDED' ? 'locked-session' : '' ?>">
        <h5><i class="fas fa-tasks"></i> QC Checklist</h5>
        
        <?php if (empty($checklist)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No checklist defined for this product/variant.
            </div>
        <?php else: ?>
            <form id="checklistForm">
                <?php foreach ($checklist as $item): ?>
                    <?php 
                        $existing = isset($qc_items[$item->code]) ? $qc_items[$item->code] : null;
                        $result_class = '';
                        if ($existing) {
                            $result_class = $existing->result === 'PASS' ? 'pass' : 'fail';
                        }
                    ?>
                    <div class="checklist-item <?= $result_class ?>" data-item-code="<?= $item->code ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><?= $item->item_name ?></h6>
                                <small class="text-muted"><?= $item->criteria ?></small>
                                <br>
                                <small><strong>AQL:</strong> <?= $item->aql ?>% | <strong>Sample Size:</strong> <?= $item->sample_size ?></small>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Result</label>
                                        <select class="form-select result-select" name="result_<?= $item->code ?>" 
                                                <?= $session->status === 'DECIDED' ? 'disabled' : '' ?>>
                                            <option value="">-- Select --</option>
                                            <option value="PASS" <?= ($existing && $existing->result === 'PASS') ? 'selected' : '' ?>>✓ PASS</option>
                                            <option value="FAIL" <?= ($existing && $existing->result === 'FAIL') ? 'selected' : '' ?>>✗ FAIL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Defect Count</label>
                                        <input type="number" class="form-control" name="defect_count_<?= $item->code ?>" 
                                               value="<?= $existing ? $existing->defect_count : 0 ?>" min="0"
                                               <?= $session->status === 'DECIDED' ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Severity</label>
                                        <select class="form-select" name="severity_<?= $item->code ?>"
                                                <?= $session->status === 'DECIDED' ? 'disabled' : '' ?>>
                                            <option value="">-- Select --</option>
                                            <option value="MINOR" <?= ($existing && $existing->severity === 'MINOR') ? 'selected' : '' ?>>Minor</option>
                                            <option value="MAJOR" <?= ($existing && $existing->severity === 'MAJOR') ? 'selected' : '' ?>>Major</option>
                                            <option value="CRITICAL" <?= ($existing && $existing->severity === 'CRITICAL') ? 'selected' : '' ?>>Critical</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Note</label>
                                        <input type="text" class="form-control" name="note_<?= $item->code ?>" 
                                               value="<?= $existing ? htmlspecialchars($existing->note) : '' ?>"
                                               placeholder="Additional notes..."
                                               <?= $session->status === 'DECIDED' ? 'disabled' : '' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if ($session->status === 'OPEN'): ?>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-qc" onclick="saveChecklist()">
                            <i class="fas fa-save"></i> Save Checklist
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>

    <!-- Attachments -->
    <div class="info-card <?= $session->status === 'DECIDED' ? 'locked-session' : '' ?>">
        <h5><i class="fas fa-paperclip"></i> Attachments (<?= count($attachments) ?>)</h5>
        
        <div class="mb-3">
            <?php if (!empty($attachments)): ?>
                <?php foreach ($attachments as $att): ?>
                    <img src="<?= base_url($att->path) ?>" class="attachment-preview" 
                         alt="<?= $att->filename ?>" 
                         onclick="window.open('<?= base_url($att->path) ?>', '_blank')">
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No attachments uploaded.</p>
            <?php endif; ?>
        </div>
        
        <?php if ($session->status === 'OPEN'): ?>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="file" class="form-control" id="fileInput" accept="image/*,video/*">
                    <button type="button" class="btn btn-qc" onclick="uploadFile()">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
                <small class="text-muted">Supported: JPG, PNG, GIF, MP4, MOV (max 10MB)</small>
            </form>
        <?php endif; ?>
    </div>

    <!-- Decision Buttons -->
    <?php if ($session->status === 'OPEN'): ?>
        <div class="info-card">
            <h5><i class="fas fa-gavel"></i> Make Decision</h5>
            <div class="d-flex gap-3">
                <button type="button" class="btn btn-qc btn-lg" onclick="makeDecision('APPROVE')">
                    <i class="fas fa-check-circle"></i> APPROVE
                </button>
                <button type="button" class="btn btn-reject btn-lg" onclick="showRejectModal()">
                    <i class="fas fa-times-circle"></i> REJECT
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle"></i> Reject Decision</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Note:</strong> Rejecting requires a reason and at least one attachment.
                    </div>
                    <label class="form-label">Reason for Rejection *</label>
                    <textarea class="form-control" id="rejectReason" rows="4" 
                              placeholder="Explain why this batch is rejected..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="makeDecision('REJECT')">
                        Confirm Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sessionId = <?= $session->id ?>;
        const baseUrl = '<?= base_url() ?>';
        
        // Save checklist
        function saveChecklist() {
            const form = document.getElementById('checklistForm');
            const items = [];
            
            document.querySelectorAll('.checklist-item').forEach(div => {
                const code = div.dataset.itemCode;
                const result = form.querySelector(`select[name="result_${code}"]`).value;
                
                if (!result) return; // Skip if no result selected
                
                items.push({
                    checklist_item_code: code,
                    checklist_item_name: div.querySelector('h6').textContent,
                    result: result,
                    defect_count: form.querySelector(`input[name="defect_count_${code}"]`).value || 0,
                    severity: form.querySelector(`select[name="severity_${code}"]`).value || null,
                    note: form.querySelector(`input[name="note_${code}"]`).value || null
                });
            });
            
            if (items.length === 0) {
                alert('Please select at least one result.');
                return;
            }
            
            const formData = new FormData();
            formData.append('items', JSON.stringify(items));
            
            fetch(`${baseUrl}qc/saveItems/${sessionId}`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Checklist saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(err => {
                alert('Error saving checklist: ' + err.message);
            });
        }
        
        // Upload file
        function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Please select a file.');
                return;
            }
            
            const formData = new FormData();
            formData.append('file', file);
            
            fetch(`${baseUrl}qc/uploadAttachment/${sessionId}`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('File uploaded successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
    </div>
</main>

<!-- Scripts -->
<script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js?v=3.0.0'); ?>"></script>

<script>
    const sessionId = <?= $session->id ?>;
    const baseUrl = '<?= site_url() ?>';
    
    // Save checklist
    function saveChecklist() {
        const form = document.getElementById('checklistForm');
        const items = [];
        
        document.querySelectorAll('.checklist-item').forEach(div => {
            const code = div.dataset.itemCode;
            const result = form.querySelector(`select[name="result_${code}"]`).value;
            
            if (!result) return; // Skip if no result selected
            
            items.push({
                checklist_item_code: code,
                checklist_item_name: div.querySelector('h6').textContent,
                result: result,
                defect_count: form.querySelector(`input[name="defect_count_${code}"]`).value || 0,
                severity: form.querySelector(`select[name="severity_${code}"]`).value || null,
                note: form.querySelector(`input[name="note_${code}"]`).value || null
            });
        });
        
        if (items.length === 0) {
            alert('Please select at least one result.');
            return;
        }
        
        const formData = new FormData();
        formData.append('items', JSON.stringify(items));
        
        fetch(`${baseUrl}qc/saveItems/${sessionId}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Checklist saved successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            alert('Error saving checklist: ' + err.message);
        });
    }
    
    // Upload file
    function uploadFile() {
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        
        if (!file) {
            alert('Please select a file.');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        
        fetch(`${baseUrl}qc/uploadAttachment/${sessionId}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('File uploaded successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            alert('Error uploading file: ' + err.message);
        });
    }
    
    // Show reject modal
    function showRejectModal() {
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }
    
    // Make decision
    function makeDecision(result) {
        let reason = null;
        
        if (result === 'REJECT') {
            reason = document.getElementById('rejectReason').value.trim();
            if (!reason) {
                alert('Reason is required for REJECT decision.');
                return;
            }
        }
        
        if (!confirm(`Are you sure you want to ${result} this inspection?`)) {
            return;
        }
        
        const formData = new FormData();
        formData.append('result', result);
        if (reason) formData.append('reason', reason);
        
        fetch(`${baseUrl}qc/makeDecision/${sessionId}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Decision ${result} recorded successfully!`);
                location.reload();
            } else if (data.code === 'NEAR_THRESHOLD') {
                alert(`Warning: ${data.message}\n\nPlease increase sample size and re-inspect.`);
            } else {
                alert('Error: ' + (data.error || 'Unknown error') + '\n' + (data.errors ? data.errors.join('\n') : ''));
            }
        })
        .catch(err => {
            alert('Error making decision: ' + err.message);
        });
    }
    
    // Result select change handler
    document.querySelectorAll('.result-select').forEach(select => {
        select.addEventListener('change', function() {
            const item = this.closest('.checklist-item');
            item.classList.remove('pass', 'fail');
            if (this.value === 'PASS') {
                item.classList.add('pass');
            } else if (this.value === 'FAIL') {
                item.classList.add('fail');
            }
        });
    });
</script>
</body>
</html>
