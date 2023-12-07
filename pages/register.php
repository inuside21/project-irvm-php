<?php

  include("../server/config.php");

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
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    
                  </span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2"><?php echo $contentPageTitle; ?></h4>
              <p class="mb-4">Please complete the fields to register</p>

              <!-- Notif -->
              <div class="alert alert-success d-none" role="alert" id="dnotiholderok">
                <b><span id="dnotiftitleok"></span></b> <span id="dnotifdescok"></span>
              </div>

              <div class="alert alert-danger d-none" role="alert" id="dnotiholdererror">
                <b><span id="dnotiftitleerror">sdfsf</span></b> <span id="dnotifdescerror"></span>
              </div>
              <!-- /Notif -->

              <form class="mb-3" id="uformm" >
                <div class="mb-3">
                  <label for="ufname" class="form-label">Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="ufname"
                    name="ufname"
                    placeholder="Full Name"
                    autofocus />
                </div>
                <div class="mb-3">
                  <label for="uuname" class="form-label">Email or Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="uuname"
                    name="uuname"
                    placeholder="Enter your email or username"
                    autofocus />
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label for="upword" class="form-label" >Password</label>
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="upword"
                      class="form-control"
                      name="upword"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password" />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" id="ubtnregister" type="button">Register</button>
                </div>
              </form>

              <p class="text-center">
                <span>Already have an account?</span>
                <a href="index.php">
                  <span>Login Here</span>
                </a>
              </p>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

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


          // Interaction
          // =================================
          $('#uLogout').click(function(e) 
          {
            localStorage.setItem("tokenId", "");
            window.location.href = "index.php";
          });

          $('#ubtnregister').click(function(e) 
          {
            SendDataRegister();
          });

          

          // Function
          // =================================
          // User - Register
          function SendDataRegister() 
          {
            //
            $('#ubtnregister').hide();

            // set
            const form = document.getElementById('uformm');
            const formData = new FormData(form);
            
            // req
            fetch('../server/api.php?mode=userregister', {
              method: 'POST',
              cache: 'no-store',
              body: formData
            })
            .then(response => {
              return response.json(); 
            })
            .then(data => {
              //
              $('#ubtnregister').show();

              // OK
              if (data.status == "ok")
              {
                ShowNotif(data.status, data.title, data.message);
              }

              // Err
              else
              {
                ShowNotif(data.status, data.title, data.message);
              }
            })
            .catch(error => {
              //
              $('#ubtnregister').show();

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
