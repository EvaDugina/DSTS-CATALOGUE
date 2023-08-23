<?php
require_once("settings.php");


function show_breadcrumbs(&$breadcrumbs) {
  if (count($breadcrumbs) < 1)
    return; 
?>
  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <div class="container-fluid ps-2">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <?php
          foreach($breadcrumbs as $name => $link) {?>
            <svg style="height: inherit;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-right-short" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
            </svg>
            <li class="px-2" style="font-size: 1.10rem;">
              <a class="text-reset" href="<?php echo $link; ?>"><?php echo $name ?></a>
            </li>
          <?php
          } ?>
        </ol>
      </nav>
    </div>
  </ul>
<?php
} 
function show_head($page_title = '', $js = array(), $css = array())
{ 
?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />

    <title><?=$page_title?></title>

    <!-- Extra -->
    <link rel="stylesheet" href="css/site_styles.css" />
    
    <!-- POPPER -->
    <!-- <script type="text/javascript" src="src/popper/popper.min.js"></script> -->
    
    <!-- MDB -->
    <link rel="stylesheet" href="css/mdb/mdb.min.css" />
    <script type="text/javascript" src="js/mdb.min.js"></script>


    <!-- jQuery -->
    <script type="text/javascript" src="js/jquery/jquery-3.5.1.min.js"></script>

	<!-- Page-specific JS/CSS -->
<?php 
    foreach($js as $url) {
?>
    <script type="text/javascript" src="<?=$url?>"></script>
<?php 
    } 
?>
<?php 
    foreach($css as $url) {
?>
    <link rel="stylesheet" href="<?=$url?>"/>
<?php 
    } 
?>
  </head>
<?php 
} 

function show_header() {
?>
  <header id="header" class="header">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
      <!-- Container wrapper -->
      <div class="container-fluid ms-3">
        <div class="navbar-brand">
          <img src="src/img/dsts_icon.jpg" class="img-fluid" style="height: 2rem;">
          <p class="text text-color mb-0 pb-0">
            <b>ДСТС – ПОИСК АНАЛОГОВ</b>
          </p>
        </div>
      </div>

    </nav>
  </header>

<?php
} 

function show_footer() 
{ 
?> 
  <!-- MDB -->
  <script type="text/javascript" src="js/mdb.min.js"></script>

  <!-- Custom scripts -->
  <script type="text/javascript"></script>

    </body>
  </html>
<?php
} 
?>