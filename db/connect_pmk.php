<?php
        // Config
        putenv("NLS_LANG=AMERICAN_AMERICA.UTF8");
        $oracle_ip='192.168.99.250';
        $oracle_port='1521';
        $oracle_service='HY';
        $oracle_username='admin';
        $oracle_password='admin';

        // Config connect		
        $oracle_server_name = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)
        (HOST = $oracle_ip)(PORT=$oracle_port))
        (CONNECT_DATA=(SERVICE_NAME=$oracle_service)))";

        // Connect to MSSQL
        putenv("NLS_LANG=AMERICAN_AMERICA.UTF8");
        $objConnect = oci_connect("$oracle_username","$oracle_password","$oracle_server_name", "AL32UTF8");
        // ini_set('oracle.charset', 'UTF-8');
        
        if (!$objConnect) {
                $m = oci_error();
                //    echo $m['message'], "\n";
        echo '<script type="text/javascript">
                        swal("", $m, "warning");
                </script>';
        // echo '<meta http-equiv="refresh" content="1;url=/db/connecthis.php" />';
        exit();
        }            
        ?>