<?php
    ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>E-Oper</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min.js"> </script>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 </script>

    <style>
    textarea {
        overflow: scroll;
        height: 100px;
        resize: none;
    }

    input:focus {
        background-color: #FFF9C4;
        border: 0;
        padding: 10px;
        color: #0084CA;
    }

    .button {
        background-color: #24135F;
        border: 1 solid;
        color: #24135F;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
    }
    </style>

</head>
<?php
require_once("./db/connection.php");
require_once('./db/connect_pmk.php');
?>

<?php 
    // include('main_script.php');
?>

<?php
?>

<body style="font-family:K2D;font-size:19px;color:black;">
    <?php
    require_once('main_top_panel_head.php');
?>
    <div style="margin: 2px 2px 2px;padding: 2px 2px 2px 2px;">
        <div class="table" style="width:100%;margin-top:0px;">
            <div class="card border-info"
                style="box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;">
                <div class="panel-heading" style="background-color: #4dbce9;font-size:25px;color:#2a044a;">
                    ตรวจสอบรหัสรายการ รายได้ของ รพ. หาดใหญ่
                </div>
                <div class="panel-body" style="background-color:#90CAF9; color:black;">
                    <input type="hidden" name=ddate id=ddate value="<?php echo $d_default;?>">
                    <input type="hidden" name="hcode" id="hcode" value="<?php echo $hcode ;?>">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="oth">เลือกกุล่มหมวดรายได้ของ รพ.</label>
                            <select class="form-control select2" name="icd101" id="icd101" style="width: 100%" required>
                                <option value="" selected readonly>(เลือกกุล่มหมวดรายได้ของ รพ. )</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- notify -->
        <!-- <script src="./assets/js/notify.js"></script> -->
        <script src="./assets/js/bootstrap-timepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
        <script>
        $(document).ready(function() {
            $('.select2').select2({
                // theme: "classic",
                // allowClear: true,
                closeOnSelect: true,
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });
        </script>

        <script type="text/javascript">
        $(document).ready(function() {
            var places = $("#places").val();
            var icd10a = $("#icd101").val();
            var gtype = $("#gtype").val();
        });

        $("#places").on("change", function() {
            var placeid = $(this).val();
            var hch = $('#hcode').val();
            if (placeid) {
                $.ajax({
                    url: "action.php",
                    type: "POST",
                    cache: false,
                    data: {
                        placeid: placeid,
                        hch: hch
                    },
                    success: function(data) {
                        $("#fhn").html(data);
                    }
                });
            }
        });

        $("#icd101").on('change', function() {
            var icd101 = this.value;

            $.ajax({
                url: 'js_search_hn.php',
                type: "POST",
                dataType: "json",
                data: {
                    icd101: icd101
                },
                cache: false,
                success: function(data) {
                    var gtype = gtype[0].gtype;
                    $("#q-view-drug").bootstrapTable('refresh', {
                        url: "drug_view.php?hn=" + gtype
                    });
                }
            });
        });

        $(document).ready(function() {
            $("#icd101").select2({
                ajax: {
                    url: "icd10a_ajax.php",
                    dataType: 'json',
                    data: function(params) {
                        var query = {
                            search: params.term,
                            type: 'icd10_search'
                        }

                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                cache: true,
                placeholder: 'Search รายการโรค ....'
            });
        });
        </script>
    </div>
</body>
<?php 
// include('main_hycall_footer.php');
oci_close($objConnect);
?>

</html>