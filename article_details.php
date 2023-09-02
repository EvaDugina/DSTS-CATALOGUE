<?php
require_once("utilities.php");
require_once("common.php");

$au = new auth_ssh();

checkAuLoggedIN($au);
checkAuIsAdmin($au);

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
                        <?php
                        if ($Article->hasInfo()) {
                            foreach ($Article->getInfo() as $info_by_catalogue) { ?>
                                <h6><?= $info_by_catalogue['catalogue_name'] ?></h6>
                                <table class="table border rounded w-auto" style="font-size:small">
                                    <tbody role="button" style="cursor:auto;">
                                        <?php foreach ($info_by_catalogue['json'] as $key => $characteristic) { ?>
                                            <tr>
                                                <th scope="row" class="font-weight-bold"><?= $key ?></th>
                                                <td><?= $characteristic ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php }
                        } else { ?>
                            <h6>Информация о характеристиках отсутствует</h6>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>


<script type="text/javascript">
    function goToCataloguePage(link, catalogue_name) {
        window.open(link, '_blank');
    }
</script>