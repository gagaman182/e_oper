<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

$host = "192.168.100.206";
$user = "ckd";
$pass = "admin";
$dbname = "e_oper";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("<div class='text-center'>
            <li>
                <a class='label label-danger' href='#hisModalCenter' data-toggle='tab'>
                    การเชื่อมต่อกับ REFER SERVER Database ไม่สำเร็จ
                </a>
            </li>
        </div>");
}

// ตั้งค่า charset แบบ procedural
mysqli_set_charset($conn, "utf8");

// ตั้งค่า Timezone และอื่น ๆ
set_time_limit(0);
date_default_timezone_set('Asia/Bangkok');
?>
