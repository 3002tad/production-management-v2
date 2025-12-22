<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('asset/backend/assets/img/apple-icon.png'); ?>">
    <link rel="icon" type="image/png" href="<?= base_url('asset/backend/assets/img/favicon.png'); ?>">
    <title>
        Production System 
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700,900" />
    <!-- Nucleo Icons -->
    <link href="<?= site_url('asset/backend/assets/css/nucleo-icons.css'); ?>" rel="stylesheet" />
    <link href="<?= site_url('asset/backend/assets/css/nucleo-svg.css'); ?>" rel="stylesheet" />
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="<?= site_url('asset/backend/assets/css/material-dashboard.css?v=3.0.0'); ?>" rel="stylesheet" />
        <style>
            :root{
                --md3-primary: #6750A4;
                --md3-primary-container: #EADDFF;
                --md3-secondary: #625B71;
                --md3-tertiary: #7D5260;
                --md3-surface: #FFFBFE;
                --md3-surface-variant: #E7E0EC;
                --md3-outline: #79747E;
            }
            /* Gradient badges (status) */
            .badge.badge-success{background: linear-gradient(135deg,#43a047,#66bb6a); color:#fff;}
            .badge.badge-warning{background: linear-gradient(135deg,#fbc02d,#ffd54f); color:#5f4300;}
            .badge.badge-danger{background: linear-gradient(135deg,#e53935,#ef5350); color:#fff;}
            .badge.badge-info{background: linear-gradient(135deg,#1e88e5,#42a5f5); color:#fff;}
            .badge.badge-primary{background: linear-gradient(135deg,#5e72e4,#825ee4); color:#fff;}

            /* Card header gradients to match Material Design 3 tone */
            .card-header-primary{background: linear-gradient(135deg,#5e72e4,#825ee4); color:#fff;}
            .card-header-info{background: linear-gradient(135deg,#1e88e5,#42a5f5); color:#fff;}
            .card-header-success{background: linear-gradient(135deg,#43a047,#66bb6a); color:#fff;}
            .card-header-warning{background: linear-gradient(135deg,#fbc02d,#ffd54f); color:#5f4300;}
            .card-header-danger{background: linear-gradient(135deg,#e53935,#ef5350); color:#fff;}
            .card-header-rose{background: linear-gradient(135deg,#e91e63,#ff4081); color:#fff;}
            .card-header.card-header-primary .card-title,
            .card-header.card-header-info .card-title,
            .card-header.card-header-success .card-title,
            .card-header.card-header-warning .card-title,
            .card-header.card-header-danger .card-title,
            .card-header.card-header-rose .card-title{ color:#fff; }
            .card-header .card-category{ opacity:.9; }

            /* Buttons gradient helpers (fallback if theme missing) */
            .bg-gradient-primary{background: linear-gradient(135deg,#5e72e4,#825ee4) !important; color:#fff;}
            .bg-gradient-info{background: linear-gradient(135deg,#1e88e5,#42a5f5) !important; color:#fff;}
            .bg-gradient-success{background: linear-gradient(135deg,#43a047,#66bb6a) !important; color:#fff;}
            .bg-gradient-warning{background: linear-gradient(135deg,#fbc02d,#ffd54f) !important; color:#5f4300;}
            .bg-gradient-danger{background: linear-gradient(135deg,#e53935,#ef5350) !important; color:#fff;}

            /* Inputs - MD3 like */
            .form-control, .form-select{
                border-radius: 12px;
                border: 1px solid var(--md3-outline);
            }
            .form-control:focus, .form-select:focus{
                box-shadow: 0 0 0 3px rgba(103,80,164,.15);
                border-color: var(--md3-primary);
            }

            /* File inputs: no rounded corners */
            .form-control[type="file"], input[type="file"], .form-control-file{
                border-radius: 0 !important;
            }

            /* Modal MD3 surface */
            .modal .modal-content{ border-radius:16px; box-shadow: 0 8px 24px rgba(0,0,0,.12); }

            /* Table center utility (opt-in via class) */
            .table-center th, .table-center td{ text-align:center; vertical-align:middle; }
        </style>
</head>

<body class="g-sidenav-show bg-gray-200">

    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times  cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand ml-3" href="index">
                <span class="font-weight-bold text-white">PRODUCTION SYSTEM</span>
            </a>
        </div>

                <hr class="horizontal light mt-0 mb-2">
                        <div class="col-14">
                                <ul class="navbar-nav">
                                        <li class="navbar-vertical">
                                        <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
                                        <span class="nav-link-text ms-1 p-2">NGUYÊN LIỆU</span>
                                        </div>
                                        </li>
                                        <hr class="horizontal light mt-0 mb-2">
                                        <li class="nav-item">
                                                <a class="nav-link text-white<?= ($navlink === 'warehouse') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse'); ?>">
                                                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10">room_preferences</i>
                                                        </div>
                                                        <span class="nav-link-text ms-1">Kho nguyên liệu</span>
                                                </a>
                                        </li>
                                        <li class="nav-item navbar-expand-xs">
                                                <a class="nav-link text-white<?= ($navlink === 'project') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse/project'); ?>">
                                                        <div class="text-white me-2 d-flex align-items justify-content">
                                                        <i class="material-icons opacity-10">task</i>
                                                        </div>
                                                        <span class="nav-link-text ms-1">Tiến độ & Kế hoạch</span>
                                                </a>
                                        </li>
                                        <li class="nav-item navbar-expand-xs">
                                                <a class="nav-link text-white<?= ($navlink === 'material') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse/material'); ?>">
                                                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10">view_in_ar</i>
                                                        </div>
                                                        <span class="nav-link-text ms-1">Quản lý nguyên vật liệu</span>
                                                </a>
                                        </li>

                                        <li class="navbar-vertical">
                                        <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
                                        <span class="nav-link-text ms-1 p-2">THÀNH PHẨM</span>
                                        </div>
                                        </li>
                                        <hr class="horizontal light mt-0 mb-2">
                                        <li class="nav-item navbar-expand-xs">
                                                <a class="nav-link text-white<?= ($navlink === 'finished') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse/finished'); ?>">
                                                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10">room_preferences</i>
                                                        </div>
                                                        <span class="nav-link-text ms-1"><?= lang('menu_warehousing'); ?></span>
                                                </a>
                                        </li>
                                        <li class="nav-item navbar-expand-xs">
                                                <a class="nav-link text-white<?= ($navlink === 'finished_inventory') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse/finished_inventory'); ?>">
                                                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10">inventory</i>
                                                        </div>
                                                        <span class="nav-link-text ms-1">Tồn kho thành phẩm</span>
                                                </a>
                                        </li>
                                        <hr class="horizontal light mt-3 mb-2">
                                        <li class="nav-item">
                                                <a class="nav-link text-white" href="<?= site_url('login/logout'); ?>">
                                                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10">logout</i>
                                                        </div>
                                                        <span class="nav-link-text ms-1">Đăng xuất</span>
                                                </a>
                                        </li>
                                </ul>
                        </div>
        </aside>

        <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
                <!-- Main Data -->
                <?php $this->load->view($content); ?>
                <!-- End of Main Data -->
        </main>

        <footer class="footer">
                <div class="container-fluid">
                        <div class="copyright float-right pt-5 text-sm">
                                © Production Management System
                        </div>
                </div>
        </footer>
    <!--   Core JS Files   -->
      

        <script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
        <script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
        <script src="<?= site_url('asset/backend/assets/js/plugins/perfect-scrollbar.min.js'); ?>"></script>
        <script src="<?= site_url('asset/backend/assets/js/plugins/smooth-scrollbar.min.js'); ?>"></script>
        <script src="<?= site_url('asset/backend/assets/js/plugins/chartjs.min.js'); ?>"></script>
        <!-- Forms Validations Plugin -->
        <script src="<?= site_url('asset/backend/assets/js/plugins/jquery.validate.min.js'); ?>"></script>
        <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
        <script src="<?= site_url('asset/backend/assets/js/plugins/jquery.bootstrap-wizard.js'); ?>"></script>
        <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
        <script src="<?= site_url('asset/backend/assets/js/plugins/bootstrap-selectpicker.js'); ?>"></script>
        <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  --><script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
        <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
        <script src="<?= site_url('asset/backend/assets/js/plugins/bootstrap-tagsinput.js'); ?>"></script>
        <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
        <script src="<?= site_url('asset/backend/assets/js/plugins/jasny-bootstrap.min.js'); ?>"></script>
        <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
        <script src="<?= site_url('asset/backend/assets/js/plugins/nouislider.min.js'); ?>"></script>
        <!-- Library for adding dinamically elements -->
        <script src="<?= site_url('asset/backend/assets/js/plugins/arrive.min.js'); ?>"></script>
        <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
        <script src="<?= site_url('asset/backend/assets/js/material-dashboard.js?v=2.1.2'); ?>" type="text/javascript"></script>
        <!-- Material Dashboard DEMO methods, don't include it in your project! -->
        <script src="<?= site_url('asset/backend/assets/demo/demo.js'); ?>"></script>
        <script src="<?= site_url('asset/backend/assets/js/script.js'); ?>"></script>

    <script>
        var chartElement = document.getElementById("chart-bars");
        if (chartElement) {
            var ctx = chartElement.getContext("2d");

            new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["M", "T", "W", "T", "F", "S", "S"],
                datasets: [{
                    label: "Sales",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "rgba(255, 255, 255, .8)",
                    data: [50, 20, 10, 22, 50, 10, 40],
                    maxBarThickness: 6
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: 'rgba(255, 255, 255, .2)'
                        },
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 500,
                            beginAtZero: true,
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                            color: "#fff"
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: 'rgba(255, 255, 255, .2)'
                        },
                        ticks: {
                            display: true,
                            color: '#f8f9fa',
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });


        var ctx2 = document.getElementById("chart-line").getContext("2d");

        new Chart(ctx2, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Mobile apps",
                    tension: 0,
                    borderWidth: 0,
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255, 255, 255, .8)",
                    pointBorderColor: "transparent",
                    borderColor: "rgba(255, 255, 255, .8)",
                    borderColor: "rgba(255, 255, 255, .8)",
                    borderWidth: 4,
                    backgroundColor: "transparent",
                    fill: true,
                    data: [50, 40, 300, 320, 500, 350, 200, 230, 500],
                    maxBarThickness: 6

                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: 'rgba(255, 255, 255, .2)'
                        },
                        ticks: {
                            display: true,
                            color: '#f8f9fa',
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#f8f9fa',
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });

        var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

        new Chart(ctx3, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Mobile apps",
                    tension: 0,
                    borderWidth: 0,
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255, 255, 255, .8)",
                    pointBorderColor: "transparent",
                    borderColor: "rgba(255, 255, 255, .8)",
                    borderWidth: 4,
                    backgroundColor: "transparent",
                    fill: true,
                    data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                    maxBarThickness: 6

                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: 'rgba(255, 255, 255, .2)'
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#f8f9fa',
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#f8f9fa',
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
        }
    </script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
        

    <?php
        // Hiển thị thông báo alert lỗi (không hiện thành công) cho khu vực NVL
        $CI =& get_instance();
        $alert = $CI->session->flashdata('material_alert');
        $alertLevel = $CI->session->flashdata('material_alert_level');
        $isMaterialPage = isset($navlink) && $navlink === 'material';
        if ($isMaterialPage && !empty($alert) && strtolower((string)$alertLevel) !== 'success'):
    ?>
        <script>
            alert(<?php echo json_encode($alert); ?>);
        </script>
    <?php endif; ?>

    <!-- Success Toast for Login - Only on main warehouse page and only once -->
    <?php
        $success_msg = $CI->session->flashdata('success');
        // Only show success toast on the main warehouse page after login and only once per session
        $isMainWarehouse = isset($navlink) && $navlink === 'warehouse';
        $has_shown_login_toast = $CI->session->userdata('has_shown_login_toast');
        
        if (!empty($success_msg) && $isMainWarehouse && !$has_shown_login_toast):
            // Mark that we've shown the login toast
            $CI->session->set_userdata('has_shown_login_toast', true);
    ?>
        <div id="successToast" class="alert alert-success alert-dismissible fade show" style="position: fixed; top: 16px; right: 16px; z-index: 1080; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.15); display: block; min-width: 300px;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Thành công!</strong> <?= htmlspecialchars($success_msg, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var toast = document.getElementById('successToast');
                if (toast) {
                    setTimeout(function() {
                        toast.style.display = 'none';
                    }, 3000);
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>
