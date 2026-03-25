<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-Operation | Hys-Mest</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Niramit:wght@300;500;700&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Styles -->
    <style>
        body {
            font-family: 'Niramit', sans-serif;
            font-size: 18px;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Header */
        .header-container {
            background-color: #e3f2fd;
            border-bottom: 4px solid #64b5f6;
            padding: 20px 0;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .logo-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .logo-img:hover {
            transform: scale(1.1);
        }

        .brand-title {
            font-weight: 700;
            font-size: 1.8rem;
            color: #0d47a1;
        }

        .brand-sub {
            font-size: 1.1rem;
            font-style: italic;
            color: #546e7a;
        }

        .badge-innovation {
            background-color: #212121;
            color: #00e676;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 50px;
            margin-top: 8px;
            display: inline-block;
            box-shadow: 0 0 10px #00e676;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.5);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(0, 230, 118, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(0, 230, 118, 0);
            }
        }

        /* เมนูปุ่ม */
        .menu-bar {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 16px 0;
        }

        .nav-btn {
            font-size: 16px;
            font-weight: 600;
            padding: 12px 28px;
            min-width: 180px;
            border-radius: 50px;
            transition: 0.3s all ease-in-out;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        .btn-soft-blue {
            background-color: #2196f3;
            color: white;
        }

        .btn-soft-cyan {
            background-color: #00acc1;
            color: white;
        }

        .btn-soft-green {
            background-color: #43a047;
            color: white;
        }

        .btn-soft-orange {
            background-color: #fb8c00;
            color: white;
        }

        .btn-soft-gray {
            background-color: #607d8b;
            color: white;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #90a4ae;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header-container">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-1 text-center">
                    <a href="logout.php" onclick="confirmLogout(event)">
                        <img src="./img/hyh.png" class="logo-img" alt="Logo" />
                    </a>
                </div>
                <div class="col-sm-11">
                    <div class="brand-title">ระบบแสดงต้นทุน ราคาขาย ข้อบ่งชี้ คำเตือน ค่าใช้จ่ายทุกประเภท ทุกหน่วยบริการ</div>
                    <div class="brand-sub">(Your Safety Made Always)</div>
                    <div class="badge-innovation">🚀 AI-Powered Medical Platform</div>
                </div>
            </div>
        </div>
    </div>

    <!-- เมนูหลัก -->
    <div class="menu-bar">
        <div class="container-fluid d-flex flex-wrap justify-content-center gap-3">

            <a class="btn btn-outline-danger nav-btn" href="#home">
                <i class="fa fa-map-marker-alt"></i> [HATYAI]
            </a>

            <a href="sys_process_pmk_lab.php" class="btn btn-soft-blue nav-btn">
                <i class="fa fa-vial"></i> ค่า LAB
            </a>

            <a href="sys_process_pmk_xray.php" class="btn btn-soft-cyan nav-btn">
                <i class="fa fa-x-ray"></i> ค่าเอ็กเรย์
            </a>

            <a href="sys_process_pmk_oper.php" class="btn btn-soft-green nav-btn">
                <i class="fa fa-user-md"></i> ค่าผ่าตัด/ดมยา/วัสดุการแพทย์
            </a>

            <a href="sys_process_pmk_drug.php" class="btn btn-soft-orange nav-btn">
                <i class="fa fa-pills"></i> ค่ายา
            </a>

            <a href="sys_process_pmk_service.php" class="btn btn-soft-gray nav-btn">
                <i class="fa fa-hand-holding-heart"></i> ค่าบริการ
            </a>

            <a href="sys_process_pmk_misc.php" class="btn btn-dark nav-btn">
                <i class="fa fa-ellipsis-h"></i> ค่าอื่น ๆ
            </a>

            <a href="sys_process_pmk_update.php" class="btn btn-secondary nav-btn">
                <i class="fa fa-tools"></i> ปรับปรุงรายการ
            </a>

        </div>
    </div>

    <!-- Footer -->
    <footer>
        © 2025 Hys-Mest | Powered by Smart HealthTech & AI Innovation
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert Logout -->
    <script>
        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'ออกจากระบบ?',
                text: "คุณแน่ใจว่าต้องการออกจากระบบ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ออกจากระบบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        }
    </script>
</body>

</html>
