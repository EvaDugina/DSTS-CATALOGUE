<?php
require_once("utilities.php");
require_once("common.php");

$au = new auth_ssh();

checkAuLoggedIN($au);
// checkAuIsAdmin($au);

if (isset($_GET['article_id']))
    $article_id = $_GET['article_id'];
else
    exit;


$Article = new Article($article_id);
$imageUrl = $Article->getImageUrl();

show_head("СТРАНИЦА ИНФОРМАЦИИ О ТОВАРЕ");
?>

<body style="overflow-x: hidden;">
    <?php show_header("ИНФОРМАЦИЯ О ТОВАРЕ: " . $Article->name); ?>
    <main class="">

        <div class="pt-5 px-4">
            <div class="row">
                <div class="px-5 d-flex">
                    <div class="col-md-3 me-4">
                        <?php if ($imageUrl != "") { ?>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="embed-responsive embed-responsive-1by1 text-center">
                                        <div class="embed-responsive-item">
                                            <img class="w-100 h-100 p-0 m-0 rounded-circle" src="<?= $imageUrl ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <svg class="w-100 h-100" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                            </svg>
                        <?php } ?>

                        <?php if (false) { ?>
                            <form id="form-EditImage" name="image" action="profile_edit.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="set-image" value="true"></input>
                                <label class="btn btn-outline-primary py-2 px-4">
                                    <input id="input-image" type="file" name="image-file" style="display: none;">
                                    &nbsp; <?= ($Article->getImageUrl() != null) ? 'Изменить фотографию' : 'Добавить фотографию' ?>
                                </label>
                            </form>
                        <?php } ?>

                        <?php foreach ($ARRAY_CATALOGUES as $catalogue) {
                            if ($catalogue[1] && $Article->hasInfo()) { ?>
                                <button class="btn btn-primary mb-2 w-100" onclick="goToCataloguePage('<?= $Article->getLinkToCataloguePage() ?>', '<?= $catalogue[0] ?>')">
                                    ПЕРЕЙТИ НА САЙТ <?= $catalogue[0] ?>
                                </button>
                        <?php }
                        } ?>

                    </div>

                    <div clacc="col-4" style="width:inherit;">
                        <?php if ($Article->hasInfo()) {
                            $characteristics = $Article->getAllCharacteristics(); ?>
                            <table class="table border rounded mx-0" style="border-spacing: 0; border-collapse: separate;">
                                <thead class="px-0">
                                    <tr class="bg-primary text-white border">
                                        <th scope="col" class="middleInTable border fw-bold">
                                            <div class="d-inline-flex justify-content-between align-items-center">
                                                <span>ХАРАКТЕРИСТИКИ</span>
                                                <button class="badge badge-primary badge-pill border-0 ms-3" onclick="editCharacteristicList()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </th>
                                        <?php foreach ($Article->getMainInfo() as $info_by_catalogue) { ?>
                                            <th scope="col" class="middleInTable border fw-bold"><?= $info_by_catalogue['catalogue_name'] ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody id="tbody-article" role="button" class="px-0" style="cursor:auto; border: transparent;">
                                    <?php
                                    foreach ($characteristics as $key => $line) { ?>
                                        <tr class="border">
                                            <td scope="row" class="middleInTable border fw-bold"><?= $key ?></td>
                                            <?php foreach ($line as $characteristic_by_catalogue) { ?>
                                                <td class="middleInTable border"><?= $characteristic_by_catalogue ?></td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        <?php } else { ?>
                            <h6>Информация о характеристиках отсутствует</h6>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>


<div class="modal fade" id="dialogModalEditCharacteristics" tabindex="-1" aria-labelledby="dialogMarkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalEditCharacteristics-h5-title" class="modal-title">РЕДАКТИРОВАНИЕ НАЗВАНИЯ ХАРАКТЕРИСТИК</h5>
                <button type="button" class="btn-close me-2" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalEditCharacteristics-div-characteristics" class="d-flex-column">
                    <!-- <h6>Добавление артикула по каталогу:</h6> -->
                    <?php $index = 0;
                    foreach ($characteristics as $key => $line) { ?>
                        <div class="d-inline-flex align-items-center w-100 mb-2">
                            <input id="modalEditCharacteristics-input-realCharacteristicName-<?= $index ?>" type="text" value="<?= $key ?>" class="form-control w-100 my-0" readonly>
                            <div class="mx-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                                </svg>
                            </div>
                            <input id="modalEditCharacteristics-input-newCharacteristicName-<?= $index ?>" type="text" value="" class="form-control w-100 my-0" placeholder="Введите название характеристики">
                        </div>
                    <?php $index += 1;
                    } ?>
                    <!-- <p id="modalEditCharacteristics-p-inputError" class="text-danger d-none">
                        <small><strong>ВНИМАНИЕ! В строке не должно присутсвовать ничего, кроме слитно написанного названия артикула</strong></small>
                    </p> -->
                </div>
                <br />
            </div>
            <div class="modal-footer">
                <button id="modalEditCharacteristics-button-apply" type="button" class="btn btn-primary align-items-center">
                    ПРИМЕНИТЬ
                    <div id="modalEditCharacteristics-spinner-waiting" class="spinner-border spinner-border-sm text-white ms-2 float-end d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    function goToCataloguePage(link, catalogue_name) {
        window.open(link, '_blank');
    }

    function editCharacteristicList() {
        // let characteristics = [];
        // $('#tbody-article').each((index, tr) => {
        //     let characteristic = tr.children[0].innerText;
        //     characteristics.push(characteristic);
        // });
        $('#dialogModalEditCharacteristics').modal('show');
    }

    $('#modalEditCharacteristics-button-apply').on("click", function(event) {
        let characteristics = [];
        $('#modalEditCharacteristics-div-characteristics').children().each((index, div) => {
            let realName = div.children[0].value;
            let newName = div.children[2].value;
            if (newName != "") {
                characteristics.push({
                    "realName": realName,
                    "newName": newName
                });
            }
        });
        ajaxEditCharacteristics(characteristics);
        $('#dialogModalEditCharacteristics').hide();
    });

    function ajaxEditCharacteristics(characteristics) {
        var formData = new FormData();

        formData.append('editCharacteristics', true);
        formData.append('characteristics', JSON.stringify(characteristics));

        $.ajax({
            type: "POST",
            url: 'edit_action.php#content',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'html',
            success: function(response) {
                // response = JSON.parse(response);
                // console.log(response);
                location.reload();
            },
            complete: function() {}
        });
    }
</script>