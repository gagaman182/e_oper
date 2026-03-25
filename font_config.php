<?php
include('./db/connection.php');

$sql = "SELECT font_family, font_size, font_weight FROM settings_font LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $font = $result->fetch_assoc();
    $fontFamily = $font['font_family'];
    $fontSize = $font['font_size'];
    $fontWeight = $font['font_weight'];
} else {
    // ค่าเริ่มต้น
    $fontFamily = 'Niramit';
    $fontSize = 16;
    $fontWeight = 'normal';
}
?>

<!-- โหลด Google Font ถ้าจำเป็น -->
<link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($fontFamily); ?>&display=swap"
    rel="stylesheet">

<style>
:root {
    --font-family-main: '<?php echo $fontFamily; ?>';
    --font-size-main: <?php echo $fontSize;
    ?>px;
}

body, table, td, th, input, button, select, textarea {
    font-family: '<?php echo $fontFamily; ?>', sans-serif !important;
    font-size: <?php echo $fontSize; ?>px !important;
    font-weight: <?php echo $fontWeight; ?> !important;
}

</style>