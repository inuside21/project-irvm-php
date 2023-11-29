<?php

    // database
    // =========================================================
    // Declare
    //$connection = mysqli_connect("localhost","root","raspberrypi","vishay-oilbath-db");   
    $connection = mysqli_connect("localhost","root","","vishay-oilbath-db");  

    // Date
    date_default_timezone_set("Asia/Manila");
    $date = new DateTime();
    $dateResult = $date->format('Y-m-d H:i:s');
    $dateOnlyResult = $date->format('Y-m-d');
    $dateOnlyResultYearMonth = $date->format('Y-m');
    $dateOnlyResultYear = $date->format('Y');

    // Page URL
    $_SESSION['mainUrl'] = "";

    // Content
    $contentPageTitle = "EETech Oilbath WebApp";

    // Content Side Menu
    $contentPageMenu = '
        
    ';

    // Content Footer Menu
    $contentFooterMenu1 = '
        <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">
                ©
                <script>
                document.write(new Date().getFullYear());
                </script>
                <a href="https://eetechinnovation.asia/" target="_blank" class="footer-link fw-medium">EETech Innovation</a>
            </div>
            <div class="d-none d-lg-inline-block">
                <a href="https://eetech-sys.cloud/project-inventory/login.php" target="_blank" class="footer-link me-4">Inventory System</a>
                <a href="https://eetech-sys.cloud/project-sales/login.php" target="_blank" class="footer-link me-4">Sales System</a>
                <a href="https://eetechinnovation.asia/" target="_blank" class="footer-link me-4">Website</a>
                <a href="mailto:mart.orenzo@eetechinnovation.asia" target="_blank" class="footer-link">Support</a>
            </div>
            </div>
        </footer>
    ';

    // CSS
    $contentPageCSS = '
        <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
        <link rel="stylesheet" href="../assets/vendor/libs/select2/css/select2.min.css"  />
    ';

    // JS
    $contentPageJS = '
        <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
        <script src="../assets/vendor/libs/select2/js/select2.min.js"></script>
    ';
?>