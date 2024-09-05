<?php
require_once("utilities.php");

$au = new auth_ssh();

checkAuLoggedIN($au);

if (isset($_POST['flag']))
    $flag = $_POST['flag'];
else
    exit;

// article = {
//     "article_id": "",
//     "article_name": "",
//     "producer_name": "",
//     "catalogue_name": "",
//     "main_article": ""
// }

if ($flag == "generateSearchLogResultArticleElements") {
    $data = json_decode($_POST['data']);
    if (count($data) < 1)
        exit;
?>
    <table id="table-analogs" class="table border rounded mx-0" style="border-spacing: 0; border-collapse: separate;">
        <thead class="px-0">
            <tr class="table-active">
                <th class="middleInTable col-2"><strong>АРТИКУЛ</strong></th>
                <th class="middleInTable col-3" style="white-space: nowrap;"><strong>НАЗВАНИЕ КАТАЛОГА</strong></th>
                <th class="middleInTable col-4"><strong>ПРОИЗВОДИТЕЛЬ</strong></th>

            </tr>
        </thead>
        <tbody id="tbody-analogs" role="button" class="px-0" style="border: transparent;">
            <?php
            foreach ($data as $analog) {
                if ($analog->type == "text") {
                    continue;
                } ?>
                <tr class="border">
                    <td class="middleInTable col-2 cursor-auto">
                        <button class="btn btn-link <?= ($analog->type == "main_article") ? 'text-success' : '' ?>" onclick="goToArticleDetails(<?= $analog->article_id ?>)" style="font-size:inherit;"><?= $analog->article_name ?></button>
                    </td>
                    <td class="middleInTable col-3 cursor-auto">
                        <?= $analog->catalogue_name ?>
                    </td>
                    <td class="middleInTable col-4 cursor-auto">
                        <span>
                            <?= $analog->producer_name ?> (<strong style="font-weight:bold;"><?= $analog->producer_name ?></strong>)
                        </span>
                    </td>

                </tr>
            <?php
            } ?>
        </tbody>
    </table>

<?php
    exit();
}
