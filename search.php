<?php
require_once("utilities.php");
require_once("common.php");

$au = new auth_ssh();

checkAuLoggedIN($au);
// checkAuIsAdmin($au);

show_head("СТРАНИЦА ПОИСКА АНАЛОГОВ");

if ($au->isAdmin())
    echo "<script>var is_admin=1;</script>";
else
    echo "<script>var is_admin=0;</script>";

?>

<body style="overflow-x: hidden;">
    <?php show_header("ПОИСК АНАЛОГОВ"); ?>
    <main class="">

        <div class="container">

            <div class="row mt-5">
                <div class="col-8 px-0">
                    <div class="d-flex w-100">
                        <div class="flex-grow-1 form-outline me-2">
                            <!-- <i class="fas fa-search trailing" aria-hidden="true"></i> -->
                            <input id="input-article" name="input-article" type="text" class="form-control floating-label form-icon-trailing mb-0 active" style="text-transform:uppercase;" placeholder="Поиск аналогов по артикулу">
                            <label id="label-search" for="input-article" class="form-label">Поиск аналогов по артикулу</label>

                            <div class="form-notch">
                                <div class="form-notch-leading" style="width: 9px;"></div>
                                <div class="form-notch-middle" style="width: 114.4px;"></div>
                                <div class="form-notch-trailing"></div>
                            </div>
                        </div>
                        <button id="btn-search" class="btn btn-primary me-2 align-items-center" onclick="search()">
                            <strong>ПОИСК</strong>
                            <div id="spinner-waiting-search" class="spinner-border spinner-border-sm text-white ms-2 float-end d-none" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                    <div id="div-autocomplete" class="list-group z-3 w-25 ps-0 border-0 rounded-0 d-none" style="position: absolute; font-size: x-small;"></div>
                </div>
                <?php if ($au->isAdmin()) { ?>
                    <div class="col-2 px-0">
                        <button id="btn-add-article" class="btn btn-info text-white w-100">
                            ДОБАВИТЬ АРТИКУЛ</button>
                    </div>
                    <div class="col-2">
                        <div class="w-100 d-flex">
                            <button id="btn-parse-result" class="btn btn-warning text-white disabled" onclick="parseResult()">
                                ОБРАБОТАТЬ РЕЗУЛЬТАТ</button>
                        </div>
                    </div>
                <?php } else { ?>
                    <button class="btn btn-warning col-3" onclick="sendRequestToAddArticle()">
                        ЗАПРОСИТЬ ДОБАВЛЕНИЕ ТОВАРА
                    </button>
                <?php } ?>
            </div>
            <p id="p-errorSearchField" class="text-danger d-none mb-0 pb-0"><small>Некорректный поисковой запрос. См. пример!</small></p>
            <p class="text-muted"><small>ПРИМЕР ВВОДА: P550777 или DONALDSON P550777 или MANN W94035</small></p>

            <div class="row">
                <div class="col-10 px-0">

                    <div id="div-miidle-row" class="d-none">
                        <table class="table border rounded mx-0" style="border-spacing: 0; border-collapse: separate;">
                            <thead class="px-0">
                                <tr class="bg-primary text-white">
                                    <th class="middleInTable col-2"><strong>АРТИКУЛ</strong></th>
                                    <th class="middleInTable col-2" style="white-space: nowrap;"><strong>ПРОИЗВОДИТЕЛЬ ПО DSTS</strong></th>
                                    <?php if ($au->isAdmin()) { ?>
                                        <!-- <th class="middleInTable col-3 d-none" style="white-space: nowrap;"><strong>НАЗВАНИЕ КАТАЛОГА</strong></th>
                                        <th class="middleInTable col-4 d-none"><strong>ПРОИЗВОДИТЕЛЬ</strong></th> -->
                                        <th class="middleInTable col-6"><strong>ОПИСАНИЕ</strong></th>
                                        <th class="middleInTable col-1"></th>
                                    <?php } else { ?>
                                        <th class="middleInTable col-6"><strong>ОПИСАНИЕ</strong></th>
                                    <?php } ?>
                                    <th id="th-choose" class="middleInTable d-none"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-article" role="button" class="px-0" style="cursor:auto; border: transparent;">
                            </tbody>
                        </table>
                    </div>

                    </br>

                    <div id="div-analogResults" class="d-none mb-5">
                        <div class="d-inline-flex justify-content-between align-items-center w-100 mb-1">
                            <div class="w-100">
                                <h5 class="my-0"><strong>СПИСОК АНАЛОГОВ</strong></h5>
                            </div>
                        </div>
                        <h6 id="h6-error-search" class="text-danger d-none"></h6>

                        <div id="div-main-analogs" class="mb-2">
                            <table id="table-main-analogs" class="table border rounded d-none mx-0" style="border-spacing: 0; border-collapse: separate;">
                                <thead class="px-0">
                                    <tr class="bg-info text-white">
                                        <th class="middleInTable col-2"><strong>АРТИКУЛ</strong></th>
                                        <th class="middleInTable col-2" style="white-space: nowrap;"><strong>ПРОИЗВОДИТЕЛЬ ПО DSTS</strong></th>
                                        <?php if ($au->isAdmin()) { ?>
                                            <!-- <th class="middleInTable col-3" style="white-space: nowrap;"><strong>НАЗВАНИЕ КАТАЛОГА</strong></th>
                                            <th class="middleInTable col-4"><strong>ПРОИЗВОДИТЕЛЬ</strong></th> -->
                                            <th class="middleInTable col-6"><strong>ОПИСАНИЕ</strong></th>
                                            <th class="middleInTable col-1"></th>
                                        <?php } else { ?>
                                            <th class="middleInTable col-6"><strong>ОПИСАНИЕ</strong></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody id="tbody-main-analogs" role="button" class="px-0" style="border: transparent;">

                                </tbody>
                            </table>
                            </br>
                        </div>


                        <!-- <div id="div-all-analogs" class="">
                            <table id="table-analogs" class="table border rounded d-none mx-0" style="border-spacing: 0; border-collapse: separate;">
                                <thead class="px-0">
                                    <tr class="table-active">
                                        <th class="middleInTable col-2"><strong>АРТИКУЛ</strong></th>
                                        <th class="middleInTable col-2" style="white-space: nowrap;"><strong>ПРОИЗВОДИТЕЛЬ ПО DSTS</strong></th>
                                        <?php if ($au->isAdmin()) { ?>
                                            <th class="middleInTable col-3" style="white-space: nowrap;"><strong>НАЗВАНИЕ КАТАЛОГА</strong></th>
                                            <th class="middleInTable col-4"><strong>ПРОИЗВОДИТЕЛЬ</strong></th>
                                            <th class="middleInTable col-1"></th>
                                        <?php } else { ?>
                                            <th class="middleInTable col-6"><strong>ОПИСАНИЕ</strong></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody id="tbody-analogs" role="button" class="px-0" style="border: transparent;">

                                </tbody>
                            </table>

                            <div id="div-analogs-showMore" class="d-flex justify-content-center d-none">
                                <button class="btn btn-outline-primary mt-1 mb-5" onclick="showMoreArticles()">
                                    ПОКАЗАТЬ БОЛЬШЕ
                                </button>
                            </div>

                        </div> -->

                        <?php if ($au->isAdmin()) { ?>
                            <div id="div-parse-result" class="d-none mt-1">
                                <div class="d-inline-flex w-100 align-items-center justify-content-between mb-1">
                                    <h5 class="mb-0">РЕЗУЛЬТАТ ОБРАБОТКИ:</h5>
                                    <button id="btn-copy-parse-result" class="btn btn-outline text-white bg-primary" data-toggle="tooltip" title="Копировать!" onclick="copyParseResult()">
                                        <svg id="svg-copy" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" />
                                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" />
                                        </svg>
                                        <svg id="svg-copied" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg d-none" viewBox="0 0 16 16">
                                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="w-auto" style="position: relative;">
                                    <textarea id="textarea-parseResult" class="form-control" rows="30" readonly="true"></textarea>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-2">
                    <?php if ($au->isAdmin()) { ?>
                        <div id="div-select-producers" class="d-none mb-4">
                            <div class="row">
                                <div class="col-12">
                                    <select id="select-catalogue" class="form-select" aria-label="Default select example">
                                        <option selected>ВСЕ</option>
                                        <?php foreach ($ARRAY_CATALOGUES as $catalogue) { ?>
                                            <option value="<?= $catalogue['name'] ?>"><?= $catalogue['name'] ?></option>
                                        <?php
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-12 my-2">
                                    <div id="div-selected-producers" class="">

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div id="div-goto-producers" class="d-none">
                        <button class="btn btn-primary w-100 mb-2" onclick="openCatologueSites()" style="font-size:x-small;">
                            ОТКРЫТЬ ВСЕ КАТАЛОГИ
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('DONALDSON')">
                            <img src="src/img/image_donaldson2.jpg" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('HIFI-FILTER')">
                            <img src="src/img/image_hifi.png" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('MANN')">
                            <img src="src/img/icon_mann.jpg" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('FLEETGUARD')">
                            <img src="src/img/icon_fleetguard.png" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('SF-FILTER')">
                            <img src="src/img/icon_sf.png" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('BALDWIN')">
                            <img src="src/img/icon_baldwin.jpeg" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('FIL-FILTER')">
                            <img src="src/img/icon_filfilter.png" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('LEFONG')">
                            <img src="src/img/icon_lefong.png" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('STAL')">
                            <img src="src/img/icon_stal.webp" class="img-fluid">
                        </button>

                        <button class="btn btn-link border w-100 mb-2" onclick="openCatologueSites('EUROELEMENT')">
                            <img src="src/img/icon_euroelement.png" class="img-fluid">
                        </button>

                    </div>
                </div>
            </div>

        </div>

    </main>
</body>

<?php showSearchPopovers(); ?>




<script type="text/javascript">
    var selected_catalogues = [];

    // var article_for_edit = null;
    // var addingArticle = false;

    $('#input-article').focus();

    $(document).ready(function() {

        $("#select-catalogue option").each(function() {
            if (this.text != "ВСЕ")
                addSelectedProducer(this.text)
        });
    });

    //-------------------------------------------------------------------------------------------------------------
    // ОБРАБОТЧИКИ СОБЫТИЙ
    //-------------------------------------------------------------------------------------------------------------

    $('#btn-parse-result-different-sites').on("click", function(e) {

    });


    $('#btn-copy-parse-result').hover(
        function() {
            $(this).removeClass("bg-primary");
            $(this).addClass("bg-white");
            $(this).removeClass("text-white");
            $(this).addClass("text-primary");
        },
        function() {
            $(this).removeClass("bg-white");
            $(this).addClass("bg-primary");
            $(this).removeClass("text-primary");
            $(this).addClass("text-white");
        }
    );

    $('#select-catalogue').on("change", function(e) {
        // <div class="badge badge-primary badge-pill px-3 py-2 me-2 d-inline-flex align-items-center">
        //     <p class="p-0 m-0 me-2">DONALDSON</p>
        //     <button class="btn btn-link p-1" data-producer="DONALDSON" onclick="removeSelectedProducer()" style="cursor: pointer;">
        //         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
        //             <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
        //         </svg>
        //     </button>
        // </div>

        selected_option = $('#select-catalogue').val();
        if (selected_option == "ВСЕ") {
            producer_names = [];
            $("#select-catalogue option").each(function() {
                if (this.text != "ВСЕ")
                    producer_names.push(this.text);
            });
        } else
            producer_names = [selected_option];

        producer_names.forEach((producer_name) => {
            addSelectedProducer(producer_name);
        });

    });

    //-------------------------------------------------------------------------------------------------------------
    // ОСНОВНЫЕ ФУНКЦИИ
    //-------------------------------------------------------------------------------------------------------------


    function parseResult() {
        $('#div-parse-result').removeClass("d-none");

        var formData = new FormData();

        formData.append('article', JSON.stringify(analogs[0]));

        formData.append('analogs', JSON.stringify(analogs.slice(1)));
        formData.append('selected_catalogues', JSON.stringify(selected_catalogues));

        $.ajax({
            type: "POST",
            url: 'parse_action.php#content',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'html',
            success: function(response) {
                // console.log(response);
                $('#textarea-parseResult').text(response);
            },
            complete: function() {}
        });
    }


    function openCatologueSites(site_name = "") {
        let input = $('#input-article').val();
        if (input.split(" ").length > 1)
            input = input.split(" ")[1];

        if (site_name == "") {
            window.open("https://shop.donaldson.com/store/ru-ru/search?Ntt=" + input, '_blank');
            window.open("https://catalog.filfilter.com.tr/ru/search/" + input, '_blank');
            window.open("https://www.fleetguard.com/s/searchResults?language=en_US&propertyVal=" + input, '_blank');
            window.open("https://catalog.hifi-filter.com/en-GB/search/global/cross-reference?p=1&q=" + input, '_blank');
            window.open("https://catalog.mann-filter.com/EU/rus/", '_blank');
        } else if (site_name == "DONALDSON") {
            window.open("https://shop.donaldson.com/store/ru-ru/search?Ntt=" + input, '_blank');
        } else if (site_name == "HIFI-FILTER") {
            window.open("https://catalog.hifi-filter.com/en-GB/search/global/cross-reference?p=1&q=" + input, '_blank');
        } else if (site_name == "MANN") {
            window.open("https://catalog.mann-filter.com/EU/rus/", '_blank');
        } else if (site_name == "FLEETGUARD") {
            window.open("https://www.fleetguard.com/s/searchResults?language=en_US&propertyVal=" + input, '_blank');
        } else if (site_name == "SF-FILTER") {
            window.open("https://www.sf-filter.com/en/search.htm?query=" + input, '_blank');
        } else if (site_name == "BALDWIN") {
            window.open("https://baldwin.ru/find/", '_blank');
        } else if (site_name == "FIL-FILTER") {
            window.open("https://catalog.filfilter.com.tr/ru/search/" + input, '_blank');
        } else if (site_name == "LEFONG") {
            window.open("http://www.lefongfilter.com/?_l=en", '_blank');
        } else if (site_name == "STAL") {
            window.open("https://stalfiltercatalog.ru/", '_blank');
        } else if (site_name == "EUROELEMENT") {
            window.open("https://www.euroelement.com/catalog/search/?q=" + input, '_blank');
        }
    }

    // function showPopoverEdit(student_id) {
    //     $('#dialogModalEdit').modal('show');
    // }

    // function showPopoverAddArticle() {
    //     $('#dialogModalAddArticle').modal('show');
    // }


    // function ajaxEdit(producer_id, new_producer_name_dsts, new_producer_name, real_producer_name_dsts, real_producer_name) {
    //     var formData = new FormData();

    //     formData.append('producer_id', producer_id);
    //     formData.append('new_producer_name_dsts', new_producer_name_dsts);
    //     formData.append('new_producer_name', new_producer_name);

    //     $.ajax({
    //         type: "POST",
    //         url: 'edit_action.php#content',
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         data: formData,
    //         dataType: 'html',
    //         success: function(response) {
    //             response = JSON.parse(response);
    //             console.log(response);
    //             if (response.setProducerDSTSName || response.setSimmilarProducer) {
    //                 // searchAnalogs($('#input-article').val());
    //                 if (response.setProducerDSTSName)
    //                     updateTablesAfterEditProducerNameDSTS(real_producer_name_dsts, new_producer_name_dsts);
    //                 if (response.setSimmilarProducer)
    //                     updateTablesAfterEditProducerName(real_producer_name, new_producer_name);
    //                 $('#dialogModalEdit').modal('hide');
    //             }
    //         },
    //         complete: function() {}
    //     });
    // }

    // function ajaxAddArticle(article_name, catalogue_name) {
    //     var formData = new FormData();

    //     formData.append('article_name', article_name);
    //     formData.append('catalogue_name', catalogue_name);

    //     $('#modalAddArticle-spinner-waiting').removeClass("d-none");

    //     $.ajax({
    //         type: "POST",
    //         url: 'addArticle_action.php#content',
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         data: formData,
    //         dataType: 'html',
    //         success: function(response) {
    //             // response = JSON.parse(response);
    //             $('#modalAddArticle-spinner-waiting').addClass("d-none");
    //             $('#modalAddArticle-textarea-result').text(response);
    //             $('#modalAddArticle-div-result').removeClass("d-none");
    //             addingArticle = false;
    //             search();
    //         },
    //         complete: function() {}
    //     });
    // }

    // function ajaxStopAddArticle() {

    //     window.close();

    //     // var formData = new FormData();

    //     // formData.append('code_stop', 1);

    //     // $.ajax({
    //     //     type: "POST",
    //     //     url: 'addArticle_action.php#content',
    //     //     cache: false,
    //     //     contentType: false,
    //     //     processData: false,
    //     //     data: formData,
    //     //     dataType: 'html',
    //     //     success: function(response) {
    //     //         // response = JSON.parse(response);
    //     //         $('#modalAddArticle-spinner-waiting').removeClass("d-none");
    //     //         addingArticle = false;
    //     //     },
    //     //     complete: function() {}
    //     // });
    // }


    //-------------------------------------------------------------------------------------------------------------
    // ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
    //-------------------------------------------------------------------------------------------------------------


    // function updateTablesAfterEditProducerNameDSTS(last_producer_name_dsts, new_producer_name_dsts) {
    //     analogs.forEach((article) => {
    //         if (article.producer_name_dsts == last_producer_name_dsts)
    //             article.producer_name_dsts = new_producer_name_dsts;
    //     });

    //     dynamicUpdateTablesAfterChangeProducerNameDSTS("tbody-article", last_producer_name_dsts, new_producer_name_dsts);
    //     dynamicUpdateTablesAfterChangeProducerNameDSTS("tbody-main-analogs", last_producer_name_dsts, new_producer_name_dsts);
    //     dynamicUpdateTablesAfterChangeProducerNameDSTS("tbody-analogs", last_producer_name_dsts, new_producer_name_dsts);
    // }

    // function updateTablesAfterEditProducerName(last_producer_name, new_producer_name) {
    //     analogs.forEach((article) => {
    //         if (article.producer_name == last_producer_name)
    //             article.producer_name = new_producer_name;
    //     });

    //     dynamicUpdateTablesAfterChangeProducerName("tbody-article", last_producer_name, new_producer_name);
    //     dynamicUpdateTablesAfterChangeProducerName("tbody-main-analogs", last_producer_name, new_producer_name);
    //     dynamicUpdateTablesAfterChangeProducerName("tbody-analogs", last_producer_name, new_producer_name);


    // }

    // function dynamicUpdateTablesAfterChangeProducerNameDSTS(id, last_producer_name_dsts, new_producer_name_dsts) {
    //     $("#" + id).children().each((index, tr) => {
    //         // console.log(tr);
    //         let td_producer_name_dsts = tr.children[1];
    //         if (td_producer_name_dsts.innerText == last_producer_name_dsts) {
    //             tr.children[1].innerText = new_producer_name_dsts;
    //             tr.children[1].classList.remove("text-danger");
    //         }
    //     });
    // }

    // function dynamicUpdateTablesAfterChangeProducerName(id, last_producer_name, new_producer_name) {
    //     $("#" + id).children().each((index, tr) => {
    //         // console.log(tr);
    //         let strong = tr.children[3].getElementsByTagName("strong")[0];
    //         let producer_name = strong.innerText;
    //         if (producer_name == last_producer_name) {
    //             let new_value = tr.children[3].innerText.split("(")[0] +
    //                 "(<strong style='font-weight: bold;'>" + new_producer_name + "</strong>)";
    //             tr.children[3].innerHTML = new_value;
    //         }
    //     });
    // }

    function sendRequestToAddArticle() {

    }



    function copyParseResult() {
        $("#textarea-parseResult").focus();
        $("#textarea-parseResult").select();

        try {
            let successful = document.execCommand('copy');
        } catch (err) {
            console.log('Ошибка копирования!');
        }

        if (window.getSelection) {
            if (window.getSelection().empty) { // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) { // Firefox
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) { // IE?
            document.selection.empty();
        }

        $('#svg-copy').addClass('d-none');
        $('#svg-copied').removeClass('d-none');

        $('#textarea-parseResult').css("box-shadow", "inset 0 0 100px 100px rgba(255, 255, 255, 0.7)");
        setTimeout(function() {
            $('#textarea-parseResult').css("box-shadow", "unset");
        }, 250);


        $('#btn-copy-parse-result').attr("title", "Скопировано!");

    }

    // function updateSessionParams() {
    //     sessionStorage.setItem('search_request', $('#input-article').val());
    //     sessionStorage.setItem('search_type', last_search_type);
    // }


    function addSelectedProducer(producer_name) {
        if (!selected_catalogues.includes(producer_name)) {

            selected_catalogues.push(producer_name);

            let div = document.createElement("div");
            div.id = "div-selected-producers-" + producer_name;
            div.classList.add("badge", "badge-primary", "badge-pill", "px-3", "py-2", "my-1", "d-inline-flex", "align-items-center");

            // jQuery('<div>', {
            //     id: "div-selected-producers-" + selected_catalogues.length,
            //     class: "badge badge-primary badge-pill px-3 py-2 me-2 d-inline-flex align-items-center"
            // }).appendTo('#mySelector');

            let p = document.createElement("p");
            p.classList.add("p-0", "m-0", "me-2");
            p.innerText = producer_name;

            let button = document.createElement("button");
            button.classList.add("btn", "btn-link", "p-1");
            button.setAttribute("data-producer", producer_name);
            button.style.cursor = "pointer";
            button.addEventListener("click", function() {
                producer_name = this.getAttribute("data-producer");
                // console.log("CLICK! div-selected-producers-" + producer_name);
                $('#div-selected-producers-' + producer_name).remove();
                index = selected_catalogues.indexOf(producer_name);
                selected_catalogues.splice(index, 1);
            });

            let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.classList.add("bi", "bi-x-lg");
            svg.setAttribute('width', '16');
            svg.setAttribute('height', '16');
            svg.setAttribute('viewBox', '0 0 16 16');
            svg.setAttribute('fill', 'currentColor');
            svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');

            let path1 = document.createElementNS("http://www.w3.org/2000/svg", 'path');
            path1.setAttribute('d', 'M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z');

            svg.appendChild(path1);

            button.appendChild(svg);

            div.appendChild(p);
            div.appendChild(button);

            $('#div-selected-producers').append(div);
        }
    }


    // function setValuesToDialogModalEditFields(article) {
    //     $('#modalEdit-h5-title').text($('#modalEdit-h5-title').text() + article.article_name);
    //     $('#modalEdit-input-realProducerNameInDSTSCatalogue').val(getProducerNameDSTS(article));
    //     $('#modalEdit-input-realProducerName').val(article.producer_name);
    // }

    // function setValuesToDialogModalAddArticleFields(article) {
    //     $('#modalAddArticle-input-articleName').val($('#input-article').val());
    // }


    // function getProducerNameDSTS(article) {
    //     if (article.producer_name_dsts == "")
    //         return article.producer_name;
    //     else
    //         return article.producer_name_dsts;
    // }
</script>


<script type="text/javascript" src="js/search.js"></script>