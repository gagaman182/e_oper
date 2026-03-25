<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <script langquage='javascript'>
        window.location = "dashboard.php";
    </script>
    <title>รพ หาดใหญ่</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/lib/font-awesome-4.6.3/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/metisMenu.min.css">
    <link rel="stylesheet" href="assets/css/fullcalendar.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap.css">
    <link rel="stylesheet" href="assets/css/uniform.default.min.css">
    <script src="assets/lib/html5shiv/dist/html5shiv.js"></script>
    <script src="assets/lib/respond/dest/respond.min.js"></script>
    <script>
        less = {
            relativeUrls: false,
            rootpath: "assets/"
        };
    </script>
    <link rel="stylesheet" href="assets/css/style-switcher.css">
    <link rel="stylesheet/less" type="text/css" href="assets/less/theme.less">
    <script src="assets/js/less.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery-ui.min.js"></script>
    <script src="assets/lib/moment/min/moment.min.js"></script>
    <script src="assets/lib/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="assets/js/jquery.sparkline.min.js"></script>
    <script src="assets/js/jquery.flot.min.js"></script>
    <script src="assets/js/jquery.flot.selection.min.js"></script>
    <script src="assets/js/jquery.flot.resize.min.js"></script>
    <script src="assets/lib/modernizr/modernizr.min.js"></script>
    <script src="assets/lib/highcharts/lib/highcharts.js"></script>
    <script src="assets/lib/highcharts/lib/modules/data.js"></script>
    <script src="assets/lib/highcharts/lib/modules/exporting.js"></script>
    <?php
    	include ("_chart/chart_main.php");
      ?>
        <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/dataTables.bootstrap.js"></script>
        <script src="assets/js/jquery.tablesorter.min.js"></script>
        <script src="assets/js/jquery.ui.touch-punch.min.js"></script>
        <script src="assets/lib/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="assets/js/metisMenu.min.js"></script>
        <script src="assets/js/screenfull.min.js"></script>
        <script src="assets/js/core.min.js"></script>
        <script src="assets/js/app.js"></script>
        <script>
            $(function() {
                Metis.dashboard();
            });
        </script>
        <script src="assets/js/style-switcher.min.js"></script>
</head>
<style type="text/css">
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #f7f7f7;
        /* change if the mask should be a color other than white */
        z-index: 99;
        /* makes sure it stays on top */
    }
    
    #status {
        width: 230px;
        height: 230px;
        position: absolute;
        left: 50%;
        /* centers the loading animation horizontally on the screen */
        top: 50%;
        /* centers the loading animation vertically on the screen */
        background-repeat: no-repeat;
        background-position: center;
        margin: -100px 0 0 -100px;
        /* is width and height divided by two */
    }
</style>

<body>
    <div id="preloader">
        <div id="status"><img src="img/logo.png" width="210" /><span style="padding-left: 60px; color: #999999;">กรุณารอสักครู่...</span></div>
    </div>
    <script>
        $("a").click(function(event) {
            event.preventDefault();
            var url = $(this).attr("href");
            $("div#pagecontainer").load(url);
        });
    </script>
</body>

</html>