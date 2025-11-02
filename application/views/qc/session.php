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
            padding: 20px;
        }
        
        .session-header {
            background: linear-gradient(135deg, var(--qc-primary), var(--qc-secondary));
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .checklist-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid var(--qc-secondary);
            transition: all 0.3s;
        }
        
        .checklist-item:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .checklist-item.fail {
            border-left-color: var(--qc-danger);
            background-color: #ffebee;
        }
        
        .checklist-item.pass {
            border-left-color: var(--qc-success);
            background-color: #e8f5e9;
        }
        
        .recommendation-box {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .recommendation-box.approve {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }
        
        .recommendation-box.reject {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .recommendation-box.review {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        .attachment-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
        }
        
        .btn-qc {
            background: var(--qc-secondary);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
        }
        
        .btn-qc:hover {
            background: #138f75;
            color: white;
        }
        
        .btn-reject {
            background: var(--qc-danger);
            color: white;
        }
        
        .btn-reject:hover {
            background: #c0392b;
            color: white;
        }
        
        .locked-session {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Session Header -->
    <div class="session-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-microscope"></i> <?= $session->code ?></h2>
                <p class="mb-0">
                    <i class="fas fa-calendar"></i> Started: <?= date('d/m/Y H:i', strtotime($session->started_at)) ?>
                    | Inspector: <?= $session->inspector_name ?>
                </p>
            </div>
            <div>
                <a href="<?= base_url('qc/pending') ?>" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Session Info -->
    <div class="row">
        <div class="col-md-6">
            <div class="info-card">
                <h5><i class="fas fa-info-circle"></i> Closure Information</h5>
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
            <div class="info-card">
                <h5><i class="fas fa-box"></i> Production Quantity</h5>
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
