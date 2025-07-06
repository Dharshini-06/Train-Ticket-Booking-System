<?php
  session_start();
  include('db.php');
  
?>
<!DOCTYPE html>
<html lang="en">
  <!--Head-->
  <?php include("assets/inc/head.php");?>
  <!--End Head-->
 

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
        body, .be-wrapper {
    background-image: url('assets/img/pexels-pixabay-163856.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    min-height: 100vh;
  }

  .welcome-container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    text-align: center;
    height: 100vh;
    padding-top: 160px;
    box-sizing: border-box;
  }

  .welcome-message {
    color: white;
    text-align: center;
    margin-bottom: 30px;
  }

  .welcome-message h1 {
    font-size: 4rem;
    text-shadow: 2px 2px 5px black;
  }

  .welcome-message p {
    font-size: 1.5rem;
    text-shadow: 1px 1px 3px black;
    color: #f1f1f1;
  }

  .be-left-sidebar {
    transition: transform 0.3s ease;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    height: 100vh !important;
    width: 240px;
    z-index: 1050 !important;
    background-color: #fff;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  }

  .be-left-sidebar.active {
    transform: translateX(0);
  }

  @media (max-width: 768px) {
    .be-left-sidebar {
      transform: translateX(-250px);
      position: absolute;
      z-index: 999;
      background-color: rgba(0, 0, 0, 0.85);
    }
  }

  .start-journey-btn {
    background: linear-gradient(135deg, #007bff, #00c6ff);
    color: white;
    padding: 14px 34px;
    font-size: 1.2rem;
    border: none;
    border-radius: 50px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    transition: all 0.35s ease;
    overflow: hidden;
  }

  .start-journey-btn .material-icons {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
  }

  .start-journey-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.6);
    background: linear-gradient(135deg, #00c6ff, #007bff);
    text-decoration: none;
  }

  .start-journey-btn .arrow-icon {
    transition: transform 0.3s ease;
  }

  .start-journey-btn:hover .arrow-icon {
    transform: translateX(6px);
  }
</style>
  <!--Log on to codeastro.com for more projects!-->
  <!--Log on to codeastro.com for more projects!-->
  <div class="be-content">
        <div class="main-content container-fluid">
          
          <div class="row">
            
            <div class="col-12 col-lg-6 col-xl-4">
              <a href="pass-my-booked-train.php">
                <div class="widget widget-tile">
                  <div class="chart sparkline"><i class="material-icons">add_shopping_cart</i></div>
                  <div class="data-info">
                    <div class="desc">Booked Train</div>
                  </div>
                </div>
              </a>
            </div>
            
			<!--Log on to codeastro.com for more projects!-->
            <div class="col-12 col-lg-6 col-xl-4">
              <a href="pass-print-ticket.php">
                <div class="widget widget-tile">
                  <div class="chart sparkline" ><i class ="material-icons">burst_mode</i></div>
                  <div class="data-info">
                    <div class="desc">Tickets</div>
                  </div>
                </div>
              </a>
            </div>
            
          </div>

  <body>


    <!--Navbar-->
    <?php include('assets/inc/sidebar.php');?>
     <?php include("assets/inc/navbar.php");?>
      <div class="menu-toggle d-block d-md-none p-3">
  <i class="material-icons" style="font-size: 30px; color: white; cursor: pointer;" onclick="toggleSidebar()">menu</i>
</div>

      <!--End Sidebar-->

		
          <!-- Centered Welcome Message Below Boxes -->
<div class="d-flex justify-content-center align-items-center flex-column mt-5">
  <div class="welcome-message text-center">
    <h1 style="color: white; text-shadow: 2px 2px 5px black;">Welcome to ORRS!</h1>
    <p style="font-size: 1.2rem; color: #f1f1f1; text-shadow: 1px 1px 3px black;">
      Experience the fastest, safest, and smartest way to book your journey.
    </p>
    <p style="font-size: 1rem; color: #ffffffcc; font-style: italic;">
      "Your destination is just a click away!"
    </p>
    <div class="d-flex justify-content-center mt-4">
  <a href="pass-all-available-trains.php" class="start-journey-btn">
    <span class="material-icons">train</span>
    Start Your Journey
    <span class="material-icons arrow-icon">arrow_forward</span>
  </a>
</div>

  </div>
</div>
  </div>
</div>

                
                    
                </div>
              </div>
            </div>
          </div>
         <!--Log on to codeastro.com for more projects!-->
        </div>
      </div>
     
    </div>

    <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="assets/js/app.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-flot/jquery.flot.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-flot/jquery.flot.pie.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-flot/jquery.flot.time.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-flot/jquery.flot.resize.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-flot/plugins/jquery.flot.orderBars.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-flot/plugins/curvedLines.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-flot/plugins/jquery.flot.tooltip.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
    <script src="assets/lib/countup/countUp.min.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/lib/jqvmap/jquery.vmap.min.js" type="text/javascript"></script>
    <script src="assets/lib/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net/js/jquery.dataTables.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-bs4/js/dataTables.bootstrap4.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.flash.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/jszip/jszip.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/pdfmake/pdfmake.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/pdfmake/vfs_fonts.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.colVis.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.print.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
    <script src="assets/lib/datatables/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js" type="text/javascript"></script>
    
    <script type="text/javascript">
      $(document).ready(function(){
      	//-initialize the javascript
      	App.init();
      	App.dashboard();
      
      });
  function toggleSidebar() {
    const sidebar = document.querySelector('.be-left-sidebar');
    sidebar.classList.toggle('active');
  }

    </script>
  </body>

</html>