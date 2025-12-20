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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .checklist-item {
            transition: all 0.3s ease;
            background-color: #fff;
            border: 1px solid #e0e0e0 !important;
        }
        .checklist-item:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .checklist-item.has-result {
            border-left: 4px solid #43A047 !important;
            background-color: #f1f8e9 !important;
        }
        .checklist-item.has-error {
            border-left: 4px solid #E53935 !important;
            background-color: #ffebee !important;
        }
        .item-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            min-width: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .defect-details {
            animation: slideDown 0.3s ease-out;
            border: 1px solid #fff3cd;
            padding: 10px;
            border-radius: 6px;
            background-color: #fffbf0;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 100px;
            }
        }
        .result-select.is-invalid {
            border-color: #e74c3c;
            background-color: #fadbd8;
        }
        .defect-details input.is-invalid {
            border-color: #e74c3c;
            background-color: #fadbd8;
        }
        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar {
            background: linear-gradient(90deg, #43A047 0%, #66BB6A 100%);
            transition: width 0.3s ease;
        }
        .suggestion-card {
            border-left: 4px solid #1A73E8;
            background: linear-gradient(195deg, rgba(26, 115, 232, 0.05) 0%, rgba(22, 98, 196, 0.05) 100%);
        }
        .suggestion-card.suggestion-approve {
            border-left-color: #43A047;
            background: linear-gradient(195deg, rgba(67, 160, 71, 0.05) 0%, rgba(56, 142, 60, 0.05) 100%);
        }
        .suggestion-card.suggestion-reject {
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
        .form-select-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.35rem;
        }
        .form-select-sm:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .gap-2 {
            gap: 0.5rem !important;
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
                        <a href="<?= site_url('login/logout'); ?>" class="nav-link text-body font-weight-bold px-0" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
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
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="php-upload-success">
            <span class="alert-icon"><i class="material-icons">check_circle</i></span>
            <span class="alert-text"><?= $this->session->flashdata('upload_success') ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: '<?= addslashes($this->session->flashdata('upload_success')) ?>',
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#17ad37',
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
                // Clear flashdata-based alert from DOM after showing
                setTimeout(() => {
                    const alert = document.getElementById('php-upload-success');
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        </script>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('upload_error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="php-upload-error">
            <span class="alert-icon"><i class="material-icons">error</i></span>
            <span class="alert-text"><?= $this->session->flashdata('upload_error') ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    const alert = document.getElementById('php-upload-error');
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        </script>
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
                                    <strong>Mã phiếu:</strong> <?= isset($closure->code) ? $closure->code : 'N/A' ?><br>
                                    <strong>Line:</strong> <?= isset($closure->line_code) ? $closure->line_code : 'N/A' ?> | 
                                    <strong>Ca:</strong> <?= isset($closure->shift_code) ? $closure->shift_code : 'N/A' ?><br>
                                    <strong>Dự án:</strong> <?= isset($closure->project_name) ? $closure->project_name : (isset($closure->project_code) ? $closure->project_code : 'N/A') ?><br>
                                    <strong>Sản phẩm:</strong> <?= isset($closure->product_name) ? $closure->product_name : (isset($closure->product_code) ? $closure->product_code : 'N/A') ?>
                                    <?php if (isset($closure->variant) && $closure->variant): ?>
                                        <span class="badge bg-gradient-info"><?= $closure->variant ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-lg-6 text-end">
                                <h6>Số lượng sản xuất (Use Case - Bước 4: Tải checklist theo sản phẩm)</h6>
                                <p class="text-sm mb-0">
                                    <span class="badge bg-gradient-success">TP: <?= number_format(isset($closure->qty_finished) ? $closure->qty_finished : 0) ?></span>
                                    <span class="badge bg-gradient-danger">PP: <?= number_format(isset($closure->qty_waste) ? $closure->qty_waste : 0) ?></span>
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
            <?php 
                $total_items = count($items ?? []); 
                $filled_items = 0; 
                foreach ($items as $item) {
                    if (isset($item->result) && !empty($item->result)) $filled_items++;
                }
                $is_step5_locked = ($session->status == 'DECIDED');
            ?>
            <!-- Checklist Panel (Use Case Bước 4, 5, 6) -->
            <div class="col-lg-8">
                <div class="card <?= $is_step5_locked ? 'session-locked' : '' ?>">
                    <div class="card-header pb-0">
                        <h6>Use Case Bước 5: Thực hiện kiểm định, nhập kết quả <span class="header-progress-badge">(<span class="header-filled"><?= $filled_items ?></span>/<span class="header-total"><?= $total_items ?></span>)</span></h6>
                        <?php if ($session->status == 'DECIDED'): ?>
                            <span class="badge bg-gradient-success"><i class="material-icons text-xs">lock</i> Phiên đã chốt - Khóa chỉnh sửa</span>
                        <?php elseif ($filled_items > 0): ?>
                            <span class="badge bg-gradient-info"><i class="material-icons text-xs">save</i> Đã lưu kết quả tạm thời</span>
                        <?php endif; ?>
                        <div class="progress mt-2">
                            <?php $percent = $total_items > 0 ? ($filled_items / $total_items * 100) : 0; ?>
                            <div class="progress-bar bg-gradient-success header-progress-bar" role="progressbar" 
                                 style="width: <?= $percent ?>%" 
                                 aria-valuenow="<?= $percent ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                                <span class="header-progress-percent"><?= round($percent, 1) ?>%</span>
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
                                <?php foreach ($items as $index => $item): ?>
                                    <?php 
                                        $item_code = isset($item->item_code) ? $item->item_code : (isset($item->code) ? $item->code : 'item_' . $index);
                                        $item_result = isset($item->result) ? $item->result : ''; 
                                        
                                        $item_name = isset($item->item_name) ? $item->item_name : (isset($item->criteria_name) ? $item->criteria_name : '');
                                        $item_description = isset($item->description) ? $item->description : (isset($item->criteria) ? $item->criteria : 'Kiểm tra chất lượng');
                                        $item_test_method = isset($item->test_method) ? $item->test_method : '';
                                    ?>
                                <div class="checklist-item mb-3 p-3 border rounded" data-item-index="<?= $index ?>">
                                    <!-- Hidden fields for auto-create functionality -->
                                    <input type="hidden" name="item_names[<?= $item_code ?>]" value="<?= htmlspecialchars($item_name) ?>">
                                    <input type="hidden" name="descriptions[<?= $item_code ?>]" value="<?= htmlspecialchars($item_description) ?>">
                                    <input type="hidden" name="test_methods[<?= $item_code ?>]" value="<?= htmlspecialchars($item_test_method) ?>">
                                    
                                    <div class="row align-items-start">
                                        <!-- Item Info -->
                                        <div class="col-lg-5">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="item-badge bg-light p-2 rounded text-center" style="min-width: 40px;">
                                                    <small class="fw-bold text-primary"><?= $index + 1 ?></small>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-2 fw-bold">
                                                        <?= $item_name ?>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-2 d-flex align-items-start gap-1">
                                                        <i class="material-icons" style="font-size: 16px; margin-top: 2px;">info</i>
                                                        <span><?= $item_description ?></span>
                                                    </p>
                                                    <?php if (!empty($item_test_method)): ?>
                                                    <p class="text-xs text-info mb-0 d-flex align-items-start gap-1">
                                                        <i class="material-icons" style="font-size: 16px; margin-top: 2px;">science</i>
                                                        <span>Phương pháp: <?= $item_test_method ?></span>
                                                    </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Result Selection -->
                                        <div class="col-lg-4">
                                            <label class="form-label text-xs fw-bold mb-2">Kết quả (pass/fail)</label>
                                            <select class="form-select form-select-sm result-select" 
                                                    name="results[<?= $item_code ?>]" 
                                                    data-item-id="<?= isset($item->id) ? $item->id : '' ?>"
                                                    data-item-index="<?= $index ?>"
                                                    required>
                                                <option value="">-- Chọn --</option>
                                                <option value="PASS" <?= ($item_result == 'PASS') ? 'selected' : '' ?>>
                                                    ✅ PASS
                                                </option>
                                                <option value="FAIL" <?= ($item_result == 'FAIL') ? 'selected' : '' ?>>
                                                    ❌ FAIL
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Defect Details (shown when FAIL) -->
                                        <div class="col-lg-3">
                                            <div class="defect-details" style="display: <?= ($item_result == 'FAIL') ? 'block' : 'none' ?>;">
                                                <label class="form-label text-xs fw-bold mb-2">Chi tiết lỗi</label>
                                                <input type="text" 
                                                       class="form-control form-control-sm" 
                                                       name="defects[<?= $item_code ?>]"
                                                       placeholder="Mô tả chi tiết lỗi..."
                                                       value="<?= isset($item->defect_details) ? htmlspecialchars($item->defect_details) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <!-- Progress Summary -->
                                <div class="mt-4 pt-3 border-top">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <p class="text-sm mb-0">
                                                <strong>Tiến độ:</strong> <span class="badge bg-info progress-badge"><?= $filled_items ?>/<?= $total_items ?></span>
                                                <span class="text-secondary">items đã hoàn thành</span>
                                                <span class="badge bg-primary ms-2 progress-percentage"><?= $total_items > 0 ? round(($filled_items / $total_items * 100), 1) : 0 ?>%</span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success summary-progress-bar" role="progressbar" 
                                                     style="width: <?= $total_items > 0 ? ($filled_items / $total_items * 100) : 0 ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php else: ?>
                                <div class="alert alert-warning text-center mt-3">
                                    <i class="material-icons">warning</i>
                                    <p class="mb-0">Không có checklist hoặc dữ liệu kiểm tra để nhập kết quả. Vui lòng kiểm tra lại cấu hình checklist và dữ liệu ca sản xuất!</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($session->status != 'DECIDED'): ?>
                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                    <i class="material-icons">arrow_back</i> Quay lại
                                </button>
                                <button type="submit" class="btn btn-primary" id="saveItemsBtn">
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
                        <div class="row" id="attachmentList">
                            <?php if (!empty($attachments)): ?>
                                <?php foreach ($attachments as $att): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <img src="<?= site_url($att->path) ?>" 
                                             class="card-img-top" alt="Attachment" style="height: 150px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <p class="text-xs mb-0 text-truncate"><?= $att->filename ?></p>
                                            <p class="text-xxs text-secondary mb-0"><?= $att->mime_type ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($session->status != 'DECIDED'): ?>
                        <div id="uploadPreviewContainer" class="mb-3" style="display: none;">
                            <p class="text-xs fw-bold mb-2">Xem trước:</p>
                            <div class="position-relative d-inline-block">
                                <img id="uploadPreview" src="#" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1" id="removePreview">
                                    <i class="material-icons text-xs">close</i>
                                </button>
                            </div>
                        </div>

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

            <!-- Card Gợi ý kết luận & Bảng quyết định -->
            <div class="col-lg-4">
                <!-- Card Gợi ý (Bước 6: Gợi ý kết luận Pass/Fail) -->
                <div class="card suggestion-card <?= isset($recommendation) ? 'suggestion-' . strtolower($recommendation['recommendation'] ?? '') : '' ?>">
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
                            
                            <p class="text-sm"><strong>Phân tích:</strong></p>
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
                                Hoàn thành checklist để nhận gợi ý
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
// ========================================
// CHECKLIST FORM MANAGEMENT
// ========================================

// Consolidated QC Session Scripts
const QC_SESSION = {
    updateProgress: function() {
        try {
            const selects = document.querySelectorAll('.result-select');
            let filledCount = 0;
            const totalCount = selects.length;
            
            selects.forEach(select => {
                const row = select.closest('.checklist-item');
                const val = select.value;
                if (val && val !== '') {
                    filledCount++;
                    if (row) {
                        row.classList.add('has-result');
                        if (val === 'FAIL') {
                            row.classList.add('has-error');
                        } else {
                            row.classList.remove('has-error');
                        }
                    }
                } else {
                    if (row) {
                        row.classList.remove('has-result', 'has-error');
                    }
                }
            });
            
            console.log(`QC Progress: ${filledCount}/${totalCount}`);
            
            // Update text elements
            document.querySelectorAll('.header-filled').forEach(el => el.innerText = filledCount);
            document.querySelectorAll('.header-total').forEach(el => el.innerText = totalCount);
            document.querySelectorAll('.progress-badge').forEach(el => el.innerText = filledCount + '/' + totalCount);
            
            if (totalCount > 0) {
                const percentage = (filledCount / totalCount) * 100;
                const percentText = Math.round(percentage) + '%';
                
                // Update progress bars
                document.querySelectorAll('.header-progress-bar, .summary-progress-bar').forEach(bar => {
                    bar.style.width = percentage + '%';
                    bar.setAttribute('aria-valuenow', percentage);
                });
                
                // Update percentage labels
                document.querySelectorAll('.header-progress-percent, .progress-percentage').forEach(el => {
                    el.innerText = percentText;
                });
            }
        } catch (err) {
            console.error('Error in updateProgress:', err);
        }
    },

    handleSelectChange: function(e) {
        const select = e.target;
        if (!select.classList.contains('result-select')) return;

        const row = select.closest('.checklist-item');
        const defectDetails = row ? row.querySelector('.defect-details') : null;
        
        console.log('Select changed:', select.name, select.value);
        
        if (select.value === 'FAIL') {
            if (defectDetails) {
                defectDetails.style.display = 'block';
                const defectInput = defectDetails.querySelector('input');
                if (defectInput) defectInput.required = true;
            }
        } else {
            if (defectDetails) {
                defectDetails.style.display = 'none';
                const defectInput = defectDetails.querySelector('input');
                if (defectInput) {
                    defectInput.required = false;
                    defectInput.value = '';
                }
            }
        }
        
        this.updateProgress();
    },

    init: function() {
        console.log('QC_SESSION.init() called');
        
        // Initial progress update
        this.updateProgress();
        
        // Event delegation for selects
        const form = document.getElementById('checklistForm');
        if (form) {
            form.addEventListener('change', (e) => this.handleSelectChange(e));
        }

        // Initial recommendation refresh
        if (typeof refreshRecommendationFromServer === 'function') {
            refreshRecommendationFromServer();
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    QC_SESSION.init();

    // Prevent flashdata alerts from showing again on reload
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_RELOAD) {
        const flashAlerts = document.querySelectorAll('#php-upload-success, #php-upload-error');
        flashAlerts.forEach(alert => alert.remove());
    }
});

// Validate checklist form before submission
document.getElementById('checklistForm')?.addEventListener('submit', function(e) {
    const selects = document.querySelectorAll('.result-select');
    let hasEmptyFields = false;
    let hasMissingDefects = false;
    
    selects.forEach(select => {
        if (!select.value || select.value === '') {
            hasEmptyFields = true;
            select.classList.add('is-invalid');
        } else {
            select.classList.remove('is-invalid');
        }
        
        // Check for required defect details
        const row = select.closest('.checklist-item');
        const defectDetails = row.querySelector('.defect-details');
        if (defectDetails && defectDetails.style.display !== 'none') {
            const defectInput = defectDetails.querySelector('input');
            if (!defectInput.value || defectInput.value.trim() === '') {
                hasMissingDefects = true;
                defectInput.classList.add('is-invalid');
            } else {
                defectInput.classList.remove('is-invalid');
            }
        }
    });
    
    if (hasEmptyFields) {
        e.preventDefault();
        Swal?.fire({
            icon: 'warning',
            title: 'Chưa hoàn thành',
            text: 'Vui lòng chọn kết quả cho tất cả các mục kiểm tra!',
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
    if (hasMissingDefects) {
        e.preventDefault();
        Swal?.fire({
            icon: 'warning',
            title: 'Thiếu chi tiết lỗi',
            text: 'Vui lòng nhập chi tiết cho tất cả các lỗi (FAIL)!',
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
    // Prevent default form submission - use AJAX instead
    e.preventDefault();
    
    // AJAX submit
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('#saveItemsBtn');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable entire form during save
    const allInputs = form.querySelectorAll('input, select, button, textarea');
    allInputs.forEach(el => el.disabled = true);
    
    submitBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> Đang lưu...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(json => {
        if (json.success) {
            // Update button to show success immediately
            submitBtn.innerHTML = '<i class="material-icons">check_circle</i> Đã lưu!';
            submitBtn.classList.add('btn-success');
            
            // Show success toast
            Swal?.fire({
                icon: 'success',
                title: 'Đã lưu!',
                timer: 800,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                position: 'top-end'
            });
            
            // Reload page after 0.8 seconds to refresh data from DB
            setTimeout(() => {
                location.reload();
            }, 800);
        } else {
            Swal?.fire({
                icon: 'error',
                title: 'Lỗi',
                text: json.error || 'Lưu thất bại'
            });
            // Re-enable form on error
            allInputs.forEach(el => el.disabled = false);
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        Swal?.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể kết nối tới server'
        });
        // Re-enable form on error
        allInputs.forEach(el => el.disabled = false);
        submitBtn.innerHTML = originalBtnText;
    });

// ========================================
// REJECT FORM VALIDATION (Alternative Flow 8.1)
// ========================================

document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
    const reason = this.querySelector('textarea[name="reason"]').value;
    const attachmentCount = <?= count($attachments ?? []) ?>;
    
    if (reason.length < 20) {
        e.preventDefault();
        Swal?.fire({
            icon: 'error',
            title: 'Lý do không đủ',
            text: 'Lý do từ chối phải có ít nhất 20 ký tự (Use Case Alternative Flow 8.1)!',
            confirmButtonColor: '#e74c3c',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
    if (attachmentCount === 0) {
        e.preventDefault();
        Swal?.fire({
            icon: 'error',
            title: 'Chưa có bằng chứng',
            text: 'Bắt buộc phải đính kèm ảnh/video khi từ chối (Use Case Alternative Flow 8.1)!',
            confirmButtonColor: '#e74c3c',
            confirmButtonText: 'OK'
        });
        return false;
    }
});

// ========================================
// FILE UPLOAD MANAGEMENT (AJAX & Preview)
// ========================================

(function() {
    const fileInput = document.getElementById('attachmentFile');
    const uploadForm = document.getElementById('uploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const previewContainer = document.getElementById('uploadPreviewContainer');
    const previewImg = document.getElementById('uploadPreview');
    const removePreviewBtn = document.getElementById('removePreview');
    const attachmentList = document.getElementById('attachmentList');

    if (!fileInput) return;

    // Preview logic
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Reset button state
            uploadBtn.innerHTML = '<i class="material-icons">upload</i> Tải lên';
            uploadBtn.disabled = false;

            // Show preview if image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    });

    // Remove preview
    removePreviewBtn?.addEventListener('click', function() {
        fileInput.value = '';
        previewContainer.style.display = 'none';
        uploadBtn.disabled = true;
    });

    // AJAX Upload
    uploadForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal?.fire({
                icon: 'warning',
                title: 'Chưa chọn file',
                text: 'Vui lòng chọn file để tải lên!',
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'OK'
            });
            return false;
        }

        const formData = new FormData(this);
        const originalBtnText = uploadBtn.innerHTML;
        
        // Show loading state
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang tải...';
        uploadBtn.disabled = true;

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add to list
                const att = data.attachment;
                const newCol = document.createElement('div');
                newCol.className = 'col-md-3 mb-3';
                newCol.innerHTML = `
                    <div class="card">
                        <img src="${att.path}" class="card-img-top" alt="Attachment" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <p class="text-xs mb-0 text-truncate">${att.filename}</p>
                            <p class="text-xxs text-secondary mb-0">${att.mime_type}</p>
                        </div>
                    </div>
                `;
                attachmentList.appendChild(newCol);

                // Reset form
                uploadForm.reset();
                previewContainer.style.display = 'none';
                
                Swal?.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Đã tải ảnh lên thành công!',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Update attachment count in header
                const headerTitle = document.querySelector('.card-header h6');
                if (headerTitle && headerTitle.innerText.includes('Đính kèm')) {
                    const currentCount = attachmentList.querySelectorAll('.col-md-3').length;
                    headerTitle.innerHTML = `Đính kèm ảnh/video (${currentCount})`;
                }
            } else {
                throw new Error(data.error || 'Lỗi không xác định');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            Swal?.fire({
                icon: 'error',
                title: 'Lỗi',
                text: error.message || 'Không thể tải file lên. Vui lòng thử lại.',
                confirmButtonColor: '#e74c3c'
            });
        })
        .finally(() => {
            uploadBtn.innerHTML = originalBtnText;
            uploadBtn.disabled = false;
        });
    });
})();

// ========================================
// AUTO-SAVE & NOTIFICATIONS
// ========================================

// Refresh recommendation from server on page load
function refreshRecommendationFromServer() {
    const sessionId = '<?= $session->id ?>';
    
    fetch(`<?= site_url('qc/getRecommendation/') ?>${sessionId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Recommendation from server:', data);
        
        if (data && data.recommendation) {
            // Update Step 6 recommendation card
            const suggestionCard = document.querySelector('.suggestion-card');
            if (suggestionCard) {
                const card = suggestionCard.closest('.card');
                card.className = 'card mt-4 suggestion-card suggestion-' + data.recommendation.toLowerCase();
                
                const badgeClass = data.recommendation === 'APPROVE' ? 'bg-gradient-success' : 
                                   data.recommendation === 'REJECT' ? 'bg-gradient-danger' :
                                   data.recommendation === 'REVIEW_NEEDED' ? 'bg-gradient-warning' : 
                                   data.recommendation === 'INCOMPLETE' ? 'bg-gradient-secondary' : 'bg-gradient-secondary';
                
                card.innerHTML = `
                    <div class="card-header pb-0">
                        <h6><i class="material-icons">psychology</i> Use Case Bước 6: Gợi ý kết luận</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge badge-lg ${badgeClass}">
                                ${data.recommendation}
                            </span>
                            <span class="badge badge-sm bg-gradient-info ms-2">
                                Độ tin cậy: ${data.confidence || 'N/A'}
                            </span>
                        </div>
                        <p class="text-sm"><strong>Phân tích:</strong></p>
                        <p class="text-xs">${data.analysis || 'Không có phân tích'}</p>
                        ${data.action ? `
                            <div class="alert alert-info p-2 mt-2">
                                <p class="text-xs mb-0">
                                    <i class="material-icons text-xs">lightbulb</i> 
                                    <strong>Hành động đề xuất:</strong> ${data.action}
                                </p>
                            </div>
                        ` : ''}
                    </div>
                `;
            }

            // Update Step 7 decision buttons
            const decisionCard = document.querySelector('.card-body button[data-bs-target="#approveModal"]')?.closest('.card');
            if (decisionCard && data.recommendation !== 'INCOMPLETE') {
                // Enable buttons
                const approveBtn = decisionCard.querySelector('button[data-bs-target="#approveModal"]');
                const rejectBtn = decisionCard.querySelector('button[data-bs-target="#rejectModal"]');
                const warningMsg = decisionCard.querySelector('.text-warning');
                
                if (approveBtn) approveBtn.disabled = false;
                if (rejectBtn) rejectBtn.disabled = false;
                if (warningMsg) warningMsg.remove();
            }
        }
    })
    .catch(error => console.log('Failed to refresh recommendation:', error));
}

// Auto-hide dismissible alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.click();
        }
    });
}, 5000);

// Auto-save checklist every 30 seconds (Only when session is OPEN)
<?php if ($session->status != 'DECIDED'): ?>
let autoSaveInterval = setInterval(() => {
    const form = document.getElementById('checklistForm');
    if (form) {
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .catch(error => console.log('Auto-save status:', error))
        .finally(() => {
            console.log('Last auto-saved: ' + new Date().toLocaleTimeString());
        });
    }
}, 30000);

// Clear auto-save interval when page unloads
window.addEventListener('beforeunload', () => {
    if (autoSaveInterval) clearInterval(autoSaveInterval);
});
<?php endif; ?>
</script>

</body>
</html>
