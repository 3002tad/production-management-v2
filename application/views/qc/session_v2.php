<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Hệ thống QC</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700,900" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
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
        .ai-recommendation {
            border-left: 4px solid #1A73E8;
            background: linear-gradient(195deg, rgba(26, 115, 232, 0.05) 0%, rgba(22, 98, 196, 0.05) 100%);
        }
        .ai-recommendation.recommend-approve {
            border-left-color: #43A047;
            background: linear-gradient(195deg, rgba(67, 160, 71, 0.05) 0%, rgba(56, 142, 60, 0.05) 100%);
        }
        .ai-recommendation.recommend-reject {
            border-left-color: #E53935;
            background: linear-gradient(195deg, rgba(229, 57, 53, 0.05) 0%, rgba(211, 47, 47, 0.05) 100%);
        }
        .session-locked {
            opacity: 0.6;
            pointer-events: none;
        }
        .near-threshold-warning {
            background: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-200">

<!-- Sidebar -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
        <a class="navbar-brand m-0" href="<?= site_url('qc/'); ?>">
            <span class="ms-1 font-weight-bold text-white">PRODUCTION SYSTEM</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto">
        <ul class="navbar-nav">
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">QC - KIỂM SOÁT CHẤT LƯỢNG</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">pending_actions</i>
                    </div>
                    <span class="nav-link-text ms-1">Phiếu chốt ca chờ QC</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white active bg-gradient-primary" href="<?= site_url('qc/sessions'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <span class="nav-link-text ms-1">Phiên kiểm tra của tôi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/adjustments'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">build_circle</i>
                    </div>
                    <span class="nav-link-text ms-1">Yêu cầu điều chỉnh</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('qc/'); ?>">QC</a></li>
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('qc/sessions'); ?>">Phiên kiểm tra</a></li>
                    <li class="breadcrumb-item text-sm text-dark active"><?= $session->code ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Kiểm định chất lượng - Use Case 19</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
                <ul class="navbar-nav justify-content-end">
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

    <div class="container-fluid py-4">
        <!-- Upload Messages -->
        <?php if ($this->session->flashdata('upload_success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">check_circle</i></span>
            <span class="alert-text"><?= $this->session->flashdata('upload_success') ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('upload_error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">error</i></span>
            <span class="alert-text"><?= $this->session->flashdata('upload_error') ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Session Info Header (Use Case - Bước 3: Xem chi tiết) -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>Thông tin phiếu chốt ca</h6>
                                <p class="text-sm mb-0">
                                    <strong>Mã phiếu:</strong> <?= $closure->code ?><br>
                                    <strong>Line:</strong> <?= $closure->line_code ?> | 
                                    <strong>Ca:</strong> <?= $closure->shift_code ?><br>
                                    <strong>Dự án:</strong> <?= $closure->project_name ?? $closure->project_code ?><br>
                                    <strong>Sản phẩm:</strong> <?= $closure->product_name ?? $closure->product_code ?>
                                    <?php if ($closure->variant): ?>
                                        <span class="badge bg-gradient-info"><?= $closure->variant ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-lg-6 text-end">
                                <h6>Số lượng sản xuất (Use Case - Bước 4: Tải checklist theo sản phẩm)</h6>
                                <p class="text-sm mb-0">
                                    <span class="badge bg-gradient-success">TP: <?= number_format($closure->qty_finished) ?></span>
                                    <span class="badge bg-gradient-danger">PP: <?= number_format($closure->qty_waste) ?></span>
                                </p>
                                <p class="text-xs text-secondary mb-0">
                                    AQL: <strong><?= $session->aql_threshold ?>%</strong> | 
                                    Cỡ mẫu: <strong><?= $session->sample_size ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($session->status == 'DECIDED'): ?>
        <!-- Session Locked (Use Case - Bước 8: Khóa chỉnh sửa) -->
        <div class="alert alert-info">
            <span class="alert-icon"><i class="material-icons">lock</i></span>
            <span class="alert-text">
                <strong>Phiên đã kết thúc!</strong> Đã xác minh vào <?= date('d/m/Y H:i', strtotime($session->updated_at)) ?> 
                với kết quả: <strong><?= $session->result ?></strong>
            </span>
        </div>
        <?php endif; ?>

        <!-- Use Case Alternative Flow 6.1: Near Threshold Warning -->
        <?php if (isset($near_threshold_warning) && $near_threshold_warning): ?>
        <div class="near-threshold-warning">
            <div class="d-flex align-items-center">
                <i class="material-icons me-2" style="font-size: 36px;">warning</i>
                <div>
                    <h6 class="mb-0 text-white">⚠️ Alternative Flow 6.1: Kết quả tiệm cận ngưỡng AQL!</h6>
                    <p class="mb-0 text-sm text-white"><?= $near_threshold_warning['message'] ?></p>
                    <p class="mb-0 text-xs text-white mt-1">
                        <strong>Khuyến nghị:</strong> <?= $near_threshold_warning['recommendation'] ?><br>
                        <strong>Hành động:</strong> Tăng cỡ mẫu hoặc Force Approve (nếu có quyền)
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Checklist Panel (Use Case Bước 4, 5, 6) -->
            <div class="col-lg-8">
                <div class="card <?= $session->status == 'DECIDED' ? 'session-locked' : '' ?>">
                    <div class="card-header pb-0">
                        <h6>Use Case Bước 5: Thực hiện kiểm định, nhập kết quả (<?= $checklist_status['filled'] ?? 0 ?>/<?= $checklist_status['total'] ?? 0 ?>)</h6>
                        <div class="progress">
                            <div class="progress-bar bg-gradient-success" role="progressbar" 
                                 style="width: <?= $checklist_status['completion_rate'] ?? 0 ?>%" 
                                 aria-valuenow="<?= $checklist_status['completion_rate'] ?? 0 ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                                <?= number_format($checklist_status['completion_rate'] ?? 0, 1) ?>%
                            </div>
                        </div>
                        <p class="text-xs text-secondary mt-2 mb-0">
                            <i class="material-icons text-xs">info</i> 
                            Use Case Bước 6: Hệ thống kiểm tra tính đầy đủ và gợi ý kết luận
                        </p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= site_url('qc/saveItems/' . $session->id); ?>" id="checklistForm">
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                <div class="checklist-item mb-3 p-3 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-1"><?= $item->criteria_name ?></h6>
                                            <p class="text-xs text-secondary mb-0">
                                                <i class="material-icons text-xs">info</i> 
                                                <?= $item->description ?? 'Kiểm tra chất lượng' ?>
                                            </p>
                                            <?php if ($item->test_method): ?>
                                            <p class="text-xs text-info mb-0">
                                                <i class="material-icons text-xs">science</i> 
                                                Phương pháp: <?= $item->test_method ?>
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-xs">Kết quả (pass/fail)</label>
                                            <select class="form-select form-select-sm result-select" 
                                                    name="results[<?= $item->item_code ?>]" 
                                                    data-item-id="<?= $item->id ?>"
                                                    required>
                                                <option value="">-- Chọn --</option>
                                                <option value="PASS" <?= $item->result == 'PASS' ? 'selected' : '' ?>>
                                                    ✅ PASS
                                                </option>
                                                <option value="FAIL" <?= $item->result == 'FAIL' ? 'selected' : '' ?>>
                                                    ❌ FAIL
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-xs">Số lỗi (theo loại)</label>
                                            <input type="number" class="form-control form-control-sm" 
                                                   name="defects[<?= $item->item_code ?>]" 
                                                   value="<?= $item->defect_count ?? 0 ?>" 
                                                   min="0" placeholder="0">
                                        </div>
                                    </div>
                                    
                                    <!-- Defect Severity -->
                                    <div class="row mt-2 defect-details" style="display: <?= $item->result == 'FAIL' ? 'block' : 'none' ?>;">
                                        <div class="col-md-6">
                                            <label class="form-label text-xs">Mức độ nghiêm trọng</label>
                                            <select class="form-select form-select-sm" name="severity[<?= $item->item_code ?>]">
                                                <option value="">-- Chọn --</option>
                                                <option value="CRITICAL" <?= $item->severity == 'CRITICAL' ? 'selected' : '' ?>>
                                                    🔴 Critical (Nghiêm trọng)
                                                </option>
                                                <option value="MAJOR" <?= $item->severity == 'MAJOR' ? 'selected' : '' ?>>
                                                    🟠 Major (Quan trọng)
                                                </option>
                                                <option value="MINOR" <?= $item->severity == 'MINOR' ? 'selected' : '' ?>>
                                                    🟡 Minor (Nhỏ)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-xs">Ghi chú</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="notes[<?= $item->item_code ?>]" 
                                                   value="<?= $item->notes ?? '' ?>" 
                                                   placeholder="Mô tả lỗi...">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="material-icons text-secondary" style="font-size: 48px;">assignment</i>
                                    <p class="text-secondary">Checklist chưa được tải</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($session->status != 'DECIDED'): ?>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons">save</i> Lưu kết quả
                                </button>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Attachments Panel (Use Case Alternative Flow 8.1: Bắt buộc ảnh/video khi Reject) -->
                <div class="card mt-4 <?= $session->status == 'DECIDED' ? 'session-locked' : '' ?>">
                    <div class="card-header pb-0">
                        <h6>Đính kèm ảnh/video (<?= count($attachments ?? []) ?>)</h6>
                        <p class="text-xs text-danger mb-0">
                            <i class="material-icons text-xs">warning</i> 
                            <strong>Alternative Flow 8.1:</strong> Bắt buộc khi chọn Reject
                        </p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($attachments)): ?>
                        <div class="row">
                            <?php foreach ($attachments as $att): ?>
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <img src="<?= site_url('uploads/qc/' . $att->path) ?>" 
                                         class="card-img-top" alt="Attachment">
                                    <div class="card-body p-2">
                                        <p class="text-xs mb-0"><?= $att->mime_type ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($session->status != 'DECIDED'): ?>
                        <form method="POST" action="<?= site_url('qc/uploadAttachment/' . $session->id); ?>" 
                              enctype="multipart/form-data" id="uploadForm">
                            <div class="input-group">
                                <input type="file" class="form-control" name="attachment" id="attachmentFile"
                                       accept="image/*,video/*,.pdf,.doc,.docx" required>
                                <button type="submit" class="btn btn-primary mb-0" id="uploadBtn">
                                    <i class="material-icons">upload</i> Tải lên
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="material-icons text-xs">info</i> 
                                Chấp nhận: Ảnh (jpg, png, gif), Video (mp4, avi), Tài liệu (pdf, doc)
                            </small>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- AI Recommendation & Decision Panel -->
            <div class="col-lg-4">
                <!-- AI Recommendation Card (Use Case Bước 6: Gợi ý kết luận Pass/Fail) -->
                <div class="card ai-recommendation <?= isset($recommendation) ? 'recommend-' . strtolower($recommendation['recommendation'] ?? '') : '' ?>">
                    <div class="card-header pb-0">
                        <h6><i class="material-icons">psychology</i> Use Case Bước 6: Gợi ý kết luận</h6>
                    </div>
                    <div class="card-body">
                        <?php if (isset($recommendation)): ?>
                            <div class="mb-3">
                                <span class="badge badge-lg 
                                    <?= $recommendation['recommendation'] == 'APPROVE' ? 'bg-gradient-success' : '' ?>
                                    <?= $recommendation['recommendation'] == 'REJECT' ? 'bg-gradient-danger' : '' ?>
                                    <?= $recommendation['recommendation'] == 'REVIEW_NEEDED' ? 'bg-gradient-warning' : '' ?>
                                    <?= $recommendation['recommendation'] == 'INCOMPLETE' ? 'bg-gradient-secondary' : '' ?>">
                                    <?= $recommendation['recommendation'] ?>
                                </span>
                                <span class="badge badge-sm bg-gradient-info ms-2">
                                    Độ tin cậy: <?= $recommendation['confidence'] ?>
                                </span>
                            </div>
                            
                            <p class="text-sm"><strong>Phân tích AI:</strong></p>
                            <p class="text-xs"><?= $recommendation['analysis'] ?></p>
                            
                            <?php if (!empty($recommendation['action'])): ?>
                            <div class="alert alert-info p-2 mt-2">
                                <p class="text-xs mb-0">
                                    <i class="material-icons text-xs">lightbulb</i> 
                                    <strong>Hành động đề xuất:</strong> <?= $recommendation['action'] ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-xs text-secondary">
                                <i class="material-icons text-xs">info</i> 
                                Hoàn thành checklist để nhận gợi ý từ AI
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Decision Buttons (Use Case Bước 7, 8) -->
                <?php if ($session->status != 'DECIDED'): ?>
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Use Case Bước 7: Xác minh hoặc Từ chối</h6>
                    </div>
                    <div class="card-body">
                        <!-- APPROVE Button (Bước 8: Approve) -->
                        <button type="button" class="btn btn-success w-100 mb-2" 
                                data-bs-toggle="modal" data-bs-target="#approveModal"
                                <?= !$checklist_status['complete'] ? 'disabled' : '' ?>>
                            <i class="material-icons">check_circle</i> Xác minh (APPROVE)
                        </button>
                        
                        <!-- REJECT Button (Alternative Flow 8.1) -->
                        <button type="button" class="btn btn-danger w-100" 
                                data-bs-toggle="modal" data-bs-target="#rejectModal"
                                <?= !$checklist_status['complete'] ? 'disabled' : '' ?>>
                            <i class="material-icons">cancel</i> Từ chối (REJECT)
                        </button>
                        
                        <?php if (!$checklist_status['complete']): ?>
                        <p class="text-xs text-warning mt-2 mb-0">
                            <i class="material-icons text-xs">warning</i> 
                            Hoàn thành checklist trước khi quyết định
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Session Summary -->
                <div class="card mt-4">
                    <div class="card-header pb-0">
                        <h6>Thông tin phiên kiểm tra</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-xs mb-1"><strong>Mã phiên:</strong> <?= $session->code ?></p>
                        <p class="text-xs mb-1"><strong>Người kiểm tra:</strong> <?= $session->inspector_name ?></p>
                        <p class="text-xs mb-1"><strong>Bắt đầu:</strong> <?= date('d/m/Y H:i', strtotime($session->created_at)) ?></p>
                        <?php if ($session->status == 'DECIDED'): ?>
                        <p class="text-xs mb-1"><strong>Quyết định:</strong> <?= date('d/m/Y H:i', strtotime($session->updated_at)) ?></p>
                        <p class="text-xs mb-0">
                            <strong>Kết quả:</strong> 
                            <span class="badge <?= $session->result == 'APPROVED' ? 'bg-gradient-success' : 'bg-gradient-danger' ?>">
                                <?= $session->result ?>
                            </span>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- APPROVE Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title text-white">Use Case Bước 8: Xác minh (APPROVE)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= site_url('qc/makeDecision/' . $session->id); ?>">
                <div class="modal-body">
                    <p><i class="material-icons text-success">check_circle</i> 
                       Xác nhận lô hàng <strong>ĐẠT CHẤT LƯỢNG</strong> và cho phép nhập kho thành phẩm?</p>
                    
                    <input type="hidden" name="result" value="APPROVE">
                    <input type="hidden" name="force" value="0" id="forceApproveInput">
                    
                    <div class="form-group">
                        <label>Ghi chú (tùy chọn)</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons">check</i> Xác nhận APPROVE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REJECT Modal (Alternative Flow 8.1) -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-danger">
                <h5 class="modal-title text-white">Alternative Flow 8.1: Từ chối (REJECT)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= site_url('qc/makeDecision/' . $session->id); ?>" id="rejectForm">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="material-icons">warning</i> 
                        <strong>Alternative Flow 8.1:</strong> Bắt buộc nhập lý do (≥20 ký tự) và đính kèm ảnh/video
                    </div>
                    
                    <input type="hidden" name="result" value="REJECT">
                    
                    <div class="form-group">
                        <label>Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="4" 
                                  required minlength="20"
                                  placeholder="Nhập lý do từ chối chi tiết (tối thiểu 20 ký tự)..."></textarea>
                        <small class="text-muted">Tối thiểu 20 ký tự (Use Case yêu cầu)</small>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <p class="text-xs mb-0">
                            <i class="material-icons text-xs">info</i> 
                            Đã đính kèm: <strong><?= count($attachments ?? []) ?> file</strong>
                        </p>
                        <?php if (count($attachments ?? []) == 0): ?>
                        <p class="text-xs text-danger mb-0 mt-1">
                            ⚠️ Chưa có ảnh/video. Vui lòng tải lên trước khi từ chối (Alternative Flow 8.1)
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger" id="confirmRejectBtn">
                        <i class="material-icons">cancel</i> Xác nhận REJECT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js'); ?>"></script>

<script>
// Show/hide defect details based on result
document.querySelectorAll('.result-select').forEach(select => {
    select.addEventListener('change', function() {
        const row = this.closest('.checklist-item');
        const defectDetails = row.querySelector('.defect-details');
        if (this.value === 'FAIL') {
            defectDetails.style.display = 'block';
        } else {
            defectDetails.style.display = 'none';
        }
    });
});

// Validate reject form (Alternative Flow 8.1)
document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
    const reason = this.querySelector('textarea[name="reason"]').value;
    const attachmentCount = <?= count($attachments ?? []) ?>;
    
    if (reason.length < 20) {
        e.preventDefault();
        alert('⚠️ Alternative Flow 8.1: Lý do từ chối phải có ít nhất 20 ký tự!');
        return false;
    }
    
    if (attachmentCount === 0) {
        e.preventDefault();
        alert('⚠️ Alternative Flow 8.1: Bắt buộc phải đính kèm ảnh/video khi từ chối!');
        return false;
    }
});

// Validate upload form and show loading
document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
    const fileInput = document.getElementById('attachmentFile');
    const uploadBtn = document.getElementById('uploadBtn');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        e.preventDefault();
        alert('⚠️ Vui lòng chọn file để tải lên!');
        return false;
    }
    
    // Show loading state
    uploadBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> Đang tải...';
    uploadBtn.disabled = true;
    
    // Note: Form will submit normally and redirect after upload
});

// Reset upload button on file change
document.getElementById('attachmentFile')?.addEventListener('change', function() {
    const uploadBtn = document.getElementById('uploadBtn');
    uploadBtn.innerHTML = '<i class="material-icons">upload</i> Tải lên';
    uploadBtn.disabled = false;
});

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.click();
        }
    });
}, 5000);

// Auto-save checklist every 30 seconds
<?php if ($session->status != 'DECIDED'): ?>
let autoSaveInterval = setInterval(() => {
    const form = document.getElementById('checklistForm');
    if (form) {
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData
        }).then(response => {
            console.log('Auto-saved at ' + new Date().toLocaleTimeString());
        });
    }
}, 30000);
<?php endif; ?>
</script>

</body>
</html>
