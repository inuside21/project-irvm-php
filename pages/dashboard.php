<?php

  include("../server/config.php");

  /*
    <li class="menu-item">
              <a href="claim.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-party"></i>
                <div data-i18n="Basic">Claim Reward</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="claimlogs.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-clipboard"></i>
                <div data-i18n="Basic">Logs</div>
              </a>
            </li>
  */

?>

<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?php echo $contentPageTitle; ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <?php echo $contentPageCSS; ?>

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo" style="padding-left: 6rem; margin-top: 2px;">
            <a href="index.html" class="app-brand-link">
              <span class="app-brand-logo demo">
                <img src="../assets/img/items/logo.png" height="70px">
              </span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboards -->
            <li class="menu-item active">
              <a href="dashboard.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Dashboard</div>
              </a>
            </li>
            
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <div><b>IOT Reverse Vending Machine</b></div>
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="../assets/img/items/user.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="../assets/img/items/user.png" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-medium d-block" id="upfname">asdasda</span>
                            <small class="text-muted" id="upadmin">adasds</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="javascript:void(0);" id="uLogout">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">

              <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                  <h4 class="mb-1 mt-3"><b>Hello, <span id="ufname">Mart Orenzo</span>!</b></h4>
                  <p class="text-muted">Dashboard / Point Balances</p>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                </div>
              </div>

              <!-- Notif -->
              <div class="alert alert-success d-none" role="alert" id="dnotiholderok">
                <b><span id="dnotiftitleok"></span></b> <span id="dnotifdescok"></span>
              </div>

              <div class="alert alert-danger d-none" role="alert" id="dnotiholdererror">
                <b><span id="dnotiftitleerror">sdfsf</span></b> <span id="dnotifdescerror"></span>
              </div>
              <!-- /Notif -->

              <div class="row mb-5">

                <div class="col-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <img src="../assets/img/icons/unicons/wallet-info.png" alt="chart success" class="rounded">
                        </div>
                      </div>
                      <span class="fw-medium d-block mb-1">Remaining Points</span>
                      <h3 class="card-title mb-2"><b><span id="upoints">12,628</span></b></h3>
                    </div>
                  </div>
                </div>

                <div class="col-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <img src="../assets/img/icons/unicons/wallet.png" alt="chart success" class="rounded">
                        </div>
                      </div>
                      <span class="fw-medium d-block mb-1">Total Points</span>
                      <h3 class="card-title mb-2"><span id="upointstotal">12,628</span></h3>
                    </div>
                  </div>
                </div>

                <div class="col-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded">
                        </div>
                      </div>
                      <span class="fw-medium d-block mb-1">Total Claimed</span>
                      <h3 class="card-title mb-2"><span id="uclaimtotal">12,628</span></h3>
                    </div>
                  </div>
                </div>

                <div class="col-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <img src="../assets/img/icons/unicons/chart.png" alt="chart success" class="rounded">
                        </div>
                      </div>
                      <span class="fw-medium d-block mb-1">Total Codes</span>
                      <h3 class="card-title mb-2"><span id="ucodestotal">12,628</span></h3>
                    </div>
                  </div>
                </div>

                <div class="col-md-12 col-lg-12 mb-3 mt-4">
                  <div class="card">
                    <div class="row g-0">
                      <div class="col-md-12">
                        <div class="card-body">
                          <div class="row" style="--bs-gutter-x: 0rem;">

                            <div class="d-grid gap-2 col-lg-6 mx-auto">
                              <button class="btn btn-primary btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#modalCenter1"><span class="tf-icons bx bx-coin me-1"></span> Add Code / Points</button>
                              <button class="btn btn-secondary btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#modalCenter2"><span class="tf-icons bx bx-party me-1"></span> Claim Reward</button>
                            </div>

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>



              <!-- Modal - 1 -->
              <div class="modal fade" id="modalCenter1" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalCenterTitle"><b>Add Code / Points</b></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form id="cform">
                        <div class="row">
                          <div class="col mb-3">
                            <label for="ccode" class="form-label">Code</label>
                            <input type="text" id="ccode" name="ccode" class="form-control" placeholder="Enter Code">
                          </div>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                      </button>
                      <button type="button" class="btn btn-primary" id="cbtnsubmit">Add</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Modal - 2 -->
              <div class="modal fade" id="modalCenter2" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalCenterTitle"><b>Claim Reward</b></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form id="wform">
                        <div class="row">
                          <div class="col mb-3">
                            <label for="wpoints" class="form-label">How many Candy? (1 Candy = 200 Points)</label>
                            <input type="number" id="wpoints" name="wpoints" class="form-control" placeholder="0 Points">
                          </div>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                      </button>
                      <button type="button" class="btn btn-primary" id="wbtnsubmit">Claim</button>
                    </div>
                  </div>
                </div>
              </div>


            </div>
            <!-- / Content -->

            <!-- Footer -->
            <?php echo $contentFooterMenu1; ?>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <?php echo $contentPageJS; ?>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <script>
        $(document).ready(function() 
        {
          $(document).ready(function() {
              $("body").on("contextmenu", function(e) {
                  //return false;
              });
          });

          $(document).ready(function() {
              $('body').bind('cut copy', function(e) {
                  //e.preventDefault();
              });
          });


          // Start
          // =================================
          const userToken = localStorage.getItem("tokenId");
          var userData;
          LoadUserData(userToken);


          // Interaction
          // =================================
          $('#uLogout').click(function(e) 
          {
            localStorage.setItem("tokenId", "");
            window.location.href = "index.php";
          });

          $('#cbtnsubmit').click(function(e) 
          {
            SendDataUserCodeAdd();
          });

          $('#wbtnsubmit').click(function(e) 
          {
            SendDataUserWithdrawAdd();
          });

          
          // Function
          // =================================
          // User - Load
          function LoadUserData(userToken) 
          {
            // data
            const data = {
              utoken: userToken,
            };

            // set
            const formData = new URLSearchParams();
            for (const key in data) {
              formData.append(key, data[key]);
            }
            
            // req
            fetch('../server/api.php?mode=userview', {
              method: 'POST',
              cache: 'no-store',
              body: formData
            })
            .then(response => {
              return response.json(); 
            })
            .then(data => {
              // OK
              if (data.status == "ok")
              {
                //
                userData = data.data;

                // Profile
                $("#upfname").text(data.data.user_fname);
                $("#upadmin").text(data.data.user_admintext);

                // Dash
                $("#ufname").text(data.data.user_fname);
                $("#upoints").text(data.data.user_points);
                $("#upointstotal").text(data.data.user_pointstotal);
                $("#uclaimtotal").text(data.data.user_claimtotal);
                $("#ucodestotal").text(data.data.user_codetotal);
              }

              // Err
              else
              {
                window.location.href = "index.php";
              }
            })
            .catch(error => {
              //
              ShowNotif("error", "Transaction Failed", "Please reload this app");
              console.error('Error:', error);

              //
              window.location.href = "index.php";
            });
          }

          // User - Code Add
          function SendDataUserCodeAdd() 
          {
            //
            $('#cbtnsubmit').hide();
            $('#modalCenter1').modal("hide");

            // data
            const data = {
              uid: userData.id,
            };

            // set
            const form = document.getElementById('cform');
            const formData = new FormData(form);
            for (const key in data) {
              formData.append(key, data[key]);
            }

            // req
            fetch('../server/api.php?mode=usercodeadd', {
              method: 'POST',
              cache: 'no-store',
              body: formData
            })
            .then(response => {
              return response.json(); 
            })
            .then(data => {
              //
              $('#cbtnsubmit').show();
              $('#ccode').val("");

              // OK
              if (data.status == "ok")
              {
                ShowNotif(data.status, data.title, data.message);

                LoadUserData(userToken);
              }

              // Err
              else
              {
                ShowNotif(data.status, data.title, data.message);
              }
            })
            .catch(error => {
              //
              $('#cbtnsubmit').show();
              $('#ccode').val("");

              //
              ShowNotif("error", "Transaction Failed", "Please reload this app");
              console.error('Error:', error);
            });
          }

          // User - Withdraw Add
          function SendDataUserWithdrawAdd() 
          {
            //
            $('#wbtnsubmit').hide();
            $('#modalCenter2').modal("hide");

            // data
            const data = {
              uid: userData.id,
            };

            // set
            const form = document.getElementById('wform');
            const formData = new FormData(form);
            for (const key in data) {
              formData.append(key, data[key]);
            }

            // req
            fetch('../server/api.php?mode=userwithdrawadd', {
              method: 'POST',
              cache: 'no-store',
              body: formData
            })
            .then(response => {
              return response.json(); 
            })
            .then(data => {
              //
              $('#wbtnsubmit').show();
              $('#wpoints').val("");

              // OK
              if (data.status == "ok")
              {
                ShowNotif(data.status, data.title, data.message);

                LoadUserData(userToken);
              }

              // Err
              else
              {
                ShowNotif(data.status, data.title, data.message);
              }
            })
            .catch(error => {
              //
              $('#wbtnsubmit').show();
              $('#wpoints').val("");

              //
              ShowNotif("error", "Transaction Failed", "Please reload this app");
              console.error('Error:', error);
            });
          }
          

          // Notif
          function ShowNotif(status, title, message)
          {
            var elements = $('[id^="dnotiholder"]');
            elements.each(function() {
              $(this).addClass("d-none");
            });

            $("#dnotiholder" + status).removeClass("d-none");
            $("#dnotiftitle" + status).text(title);
            $("#dnotifdesc" + status).text(message);
          }
        });
    </script>
  </body>
</html>
