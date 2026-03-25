<?php ini_set("memory_limit", "256M"); ?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Operation</title>

  <!-- Bootstrap & CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
  <link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
  <link rel="stylesheet" href="./assets/theme/theme.css">
  <link rel="stylesheet" href="./css/style.css">

  <!-- JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
  <script src="assets/js/echarts.min.js"></script>

  <!-- Custom CSS -->
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      width: 100%;
      font-family: 'K2D', sans-serif;
      font-size: 18px;
      color: #20272F;
      overflow-x: hidden;
    }

    body {
      background-color: #f4f4f4;
      text-shadow: 0 -1px 0 #555;
    }

    #navcolor {
      font-size: 30px;
      font-weight: bold;
      background: linear-gradient(120deg, #ec6a45, #901f3d);
      color: #64FFDA;
      padding: 20px;
    }

    canvas {
      background: #fff;
    }

    .nav-tabs {
      margin-top: 10px;
    }

    .nav-item .btn {
      margin-right: 5px;
    }
  </style>
</head>

<body class="container-fluid">
  <?php require_once("main_top_panel_head.php"); ?>
</body>

</html>
