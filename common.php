<?php
require_once("settings.php");
require_once("utilities.php");


function show_breadcrumbs(&$breadcrumbs)
{
  if (count($breadcrumbs) < 1)
    return;
?>
  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <div class="container-fluid ps-2">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <?php
          foreach ($breadcrumbs as $name => $link) { ?>
            <svg style="height: inherit;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-right-short" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z" />
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

    <title><?= $page_title ?></title>

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
    foreach ($js as $url) {
    ?>
      <script type="text/javascript" src="<?= $url ?>"></script>
    <?php
    }
    ?>
    <?php
    foreach ($css as $url) {
    ?>
      <link rel="stylesheet" href="<?= $url ?>" />
    <?php
    }
    ?>
  </head>
<?php
}

function show_header($title)
{
?>
  <header id="header" class="header">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
      <!-- Container wrapper -->
      <div class="container-fluid ms-3">
        <div class="navbar-brand">
          <img src="src/img/icon_dsts.jpg" class="img-fluid" style="height: 2rem;">
          <p class="text text-color mb-0 pb-0">
            <b>ДСТС – <?= $title ?></b>
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


function showSearchPopovers()
{
  global $ARRAY_CATALOGUES;
?>

  <div class="modal fade" id="dialogModalAddArticle" tabindex="-1" aria-labelledby="dialogMarkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="modalAddArticle-h5-title" class="modal-title">ДОБАВЛЕНИЕ АРТИКУЛА</h5>
          <button type="button" class="btn-close me-2" data-mdb-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div>
            <h6>Добавление артикула по каталогу:</h6>
            <div class="d-inline-flex align-items-center w-75">
              <input id="modalAddArticle-input-articleName" type="text" value="" class="form-control w-100 my-0" placeholder="Введите название артикула">
              <div class="mx-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                </svg>
              </div>
              <select id="modalAddArticle-select-catalogueName" class="form-select w-75 me-3">
                <!-- <option selected>ВСЕ</option> -->
                <?php foreach ($ARRAY_CATALOGUES as $catalogue) {
                  if ($catalogue[1]) { ?>
                    <option value="<?= $catalogue[0] ?>"><?= $catalogue[0] ?></option>
                <?php }
                } ?>
              </select>
            </div>
            <p id="modalAddArticle-p-inputError" class="text-danger d-none">
              <small><strong>ВНИМАНИЕ! В строке не должно присутсвовать ничего, кроме слитно написанного названия артикула</strong></small>
            </p>
            <p class="text-muted"><small>ПРИМЕР ВВОДА: P550777 | P 550777 | P-550777 | P.550777</small></p>
          </div>
          <br />
          <div id="modalAddArticle-div-result" class="w-100 d-none">
            <h6>Результат добавления:</h6>
            <textarea id="modalAddArticle-textarea-result" class="form-control w-100" rows="10" readonly></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-mdb-dismiss="modal">ОТМЕНА</button>
          <button id="modalAddArticle-button-apply" type="button" class="btn btn-primary align-items-center">
            ДОБАВИТЬ АРТИКУЛ
            <div id="modalAddArticle-spinner-waiting" class="spinner-border spinner-border-sm text-white ms-2 float-end d-none" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="dialogModalEdit" tabindex="-1" aria-labelledby="dialogMarkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="modalEdit-h5-title" class="modal-title">РЕДАКТИРОВАНИЕ АРТИКУЛА</h5>
          <button type="button" class="btn-close me-2" data-mdb-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div>
            <h6>Изменение название производителя в каталоге ДСТС:</h6>
            <div class="d-inline-flex align-items-center w-75">
              <input id="modalEdit-input-realProducerNameInDSTSCatalogue" type="text" value="" class="form-control w-50" readonly>
              <div class="mx-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                </svg>
              </div>
              <input id="modalEdit-input-newProducerNameInDSTSCatalogue" type="text" class="form-control w-50" name="new_producer_name_in_DSTS_catalogue" />
            </div>
          </div>
          <br />
          <div>
            <h6>Присвоение подобия производителей:</h6>
            <div class="d-inline-flex align-items-center w-75">
              <input id="modalEdit-input-realProducerName" type="text" value="" class="form-control w-50" readonly>
              <div class="mx-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                </svg>
              </div>
              <select id="modalEdit-select-newProducerName" class="form-select w-50">
                <option value="">(выберите производителя)</option>
                <?php foreach (getProducersNames() as $producer_name) { ?>
                  <option value="<?= $producer_name ?>"><?= $producer_name ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <br />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-mdb-dismiss="modal">Закрыть</button>
          <button id="modalEdit-button-apply" type="button" class="btn btn-primary align-items-center">
            ПРИМЕНИТЬ
            <div id="modalEdit-spinner-waiting" class="spinner-border spinner-border-sm text-white ms-2 float-end d-none" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="js/PopoverHandler.js"></script>

<?php }
?>