<?php
require_once("utilities.php");
require_once("common.php");

$au = new auth_ssh();

checkAuLoggedIN($au);
checkAuIsAdmin($au);


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
                <div class="col-8">
                    <div class="d-flex w-100">
                        <div class="flex-grow-1 form-outline me-2">
                            <i class="fas fa-search trailing" aria-hidden="true"></i>
                            <input id="input-article" type="text" class="form-control form-icon-trailing mb-0" style="text-transform:uppercase;">
                            <label class="form-label" for="form1">Поиск аналогов по артикулу</label>

                            <div class="form-notch">
                                <div class="form-notch-leading" style="width: 9px;"></div>
                                <div class="form-notch-middle" style="width: 114.4px;"></div>
                                <div class="form-notch-trailing"></div>
                            </div>
                        </div>
                        <button id="btn-search" class="btn btn-primary me-2" onclick="searchAnalogs()">ПОИСК</button>
                    </div>
                </div>
                <?php if ($au->isAdmin()) { ?>
                    <div class="col-4">
                        <button id="btn-parse-result" class="btn btn-warning text-black w-100 disabled" onclick="parseResult()">
                            ОБРАБОТАТЬ РЕЗУЛЬТАТ</button>
                    </div>
                <?php } ?>
            </div>
            <p id="p-errorSearchField" class="text-danger d-none mb-0 pb-0"><small>В строке поиска присутсвуют недопустимые символы!</small></p>
            <p class="text-muted"><small>ПРИМЕР ВВОДА: P550777 | P 550777 | P-550777 | P.550777</small></p>

            <div class="row">
                <div class="col-10">
                    <div id="div-miidle-row" class="row d-none">
                        <table id="table-article" class="table border rounded">
                            <thead>
                                <tr>
                                    <th scope="col" class="middleInTable"><strong>АРТИКУЛ</strong></th>
                                    <th scope="col" class="middleInTable"><strong>ПРОИЗВОДИТЕЛЬ ПО DSTS</strong></th>
                                    <th scope="col" class="middleInTable"><strong>НАЗВАНИЕ КАТАЛОГА</strong></th>
                                    <th scope="col" class="middleInTable"><strong>ПРОИЗВОДИТЕЛЬ</strong></th>
                                    <th scope="col" class="middleInTable"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-article" role="button" style="cursor:auto;">
                                <!-- <tr>
                            <th scope="row">PF2161</th>
                            <td>DELCO</td>
                            <td>DONALDSON</td>
                            <td>AC DELCO</td>
                            <td>
                                <a class="badge badge-primary badge-pill" style="cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr> -->

                            </tbody>
                        </table>
                    </div>

                    </br>

                    <div id="div-search-results" class="row d-none mb-5">
                        <h5>РЕЗУЛЬТАТЫ ПОИСКА <!--по артикулу <strong id="p-strong-articleName"></strong>:--></h5>
                        <h6 id="h6-error-search" class="text-danger d-none"></h6>

                        <table id="table-analogs" class="table border rounded d-none">
                            <thead>
                                <tr>
                                    <th scope="col" class="middleInTable"><strong>АРТИКУЛ</strong></th>
                                    <th scope="col" class="middleInTable"><strong>ПРОИЗВОДИТЕЛЬ ПО DSTS</strong></th>
                                    <th scope="col" class="middleInTable"><strong>НАЗВАНИЕ КАТАЛОГА</strong></th>
                                    <th scope="col" class="middleInTable"><strong>ПРОИЗВОДИТЕЛЬ</strong></th>
                                    <th scope="col" class="middleInTable"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-analogs" role="button">
                                <!-- <tr>
                            <th scope="row">PF2161</th>
                            <td>DELCO</td>
                            <td>DONALDSON</td>
                            <td>AC DELCO</td>
                            <td>
                                <a class="badge badge-primary badge-pill" style="cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr> -->

                            </tbody>
                        </table>

                        <div id="div-showMore" class="d-flex justify-content-center d-none">
                            <button class="btn btn-outline-primary mt-1 mb-5" onclick="showMoreArticles()">
                                ПОКАЗАТЬ БОЛЬШЕ
                            </button>
                        </div>

                    </div>
                </div>

                <div id="div-select-producers" class="col-2 d-none">
                    <div class="row">
                        <div class="col-12">
                            <select id="select-catalogue" class="form-select" aria-label="Default select example">
                                <option selected>ВСЕ</option>
                                <?php foreach ($ARRAY_CATALOGUES as $catalogue) {
                                    if ($catalogue[1]) { ?>
                                        <option value="<?= $catalogue[0] ?>"><?= $catalogue[0] ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-12 my-2">
                            <div id="div-selected-producers" class="">

                            </div>
                            <!-- <div class="badge badge-primary badge-pill px-3 py-2 me-2 d-inline-flex align-items-center">
                                <p class="p-0 m-0 me-2">DONALDSON</p>
                                <button class="btn btn-link p-1" data-producer="DONALDSON" onclick="removeSelectedProducer()" style="cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg>
                                </button>
                            </div> -->
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100" onclick="openCatologueSites()">
                                ОТКРЫТЬ КАТАЛОГИ
                            </button>
                        </div>
                    </div>

                    <?php if ($au->isAdmin()) { ?>
                        <div id="div-parse-result" class="d-none mt-5">
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

        </div>

    </main>
</body>


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
                <button id="modalEdit-button-apply" type="button" class="btn btn-primary">ПРИМЕНИТЬ</button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    var analogs = [];
    var selected_catalogues = [];

    var article_for_edit = null;
    var search_type = "soft";

    var flagValidation = false;
    var COUNT_LOADING_ELEMENTS = 20;

    $(document).ready(function() {
        $("#select-catalogue option").each(function() {
            if (this.text != "ВСЕ")
                addSelectedProducer(this.text)
        });
    });

    //-------------------------------------------------------------------------------------------------------------
    // ОБРАБОТЧИКИ СОБЫТИЙ
    //-------------------------------------------------------------------------------------------------------------

    $('#input-article').on("change", function(e) {
        validateSearchField();
    });

    $('#input-article').on("keydown", function(e) {
        if (e.key == "Enter" || e.keyCode == 13) {
            validateSearchField();
            if (flagValidation)
                searchAnalogs();
            else
                cleanSearchResult();
        } else {
            if (checkPressCharInSearchField(e.key) == false) {
                e.preventDefault();
            }
        }
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


    $('#modalEdit-button-apply').on("click", function(event) {
        event.preventDefault();
        new_producer_name_dsts = $('#modalEdit-input-newProducerNameInDSTSCatalogue').val();
        new_producer_name = $('#modalEdit-select-newProducerName').val();
        ajaxEdit(article_for_edit.producer_id, new_producer_name_dsts, new_producer_name);
        $('#dialogModalEdit').modal('hide');
    });

    $('#dialogModalEdit').on('hidden.bs.modal', function(e) {
        article_for_edit = null;
    })


    //-------------------------------------------------------------------------------------------------------------
    // ОСНОВНЫЕ ФУНКЦИИ
    //-------------------------------------------------------------------------------------------------------------


    function searchAnalogs(article_name = "") {
        console.log("searchAnalogs()");

        cleanSearchResult();

        if (!flagValidation)
            return;

        var formData = new FormData();

        if (article_name == "")
            article_name = $('#input-article').val();

        article_name.toUpperCase();
        // $('#p-strong-articleName').text(article_name);

        if (article_name == "") {
            return;
        }

        formData.append('article_name', article_name);
        formData.append('search_type', search_type);

        $.ajax({
            type: "POST",
            url: 'search_action.php#content',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'html',
            success: function(response) {
                // console.log(response);
                response = JSON.parse(response);

                // Обработка и вывод ошибок поиска
                if (response.error) {
                    console.log("ERROR!");
                    if (response.error == "article_id") {
                        $('#h6-error-search').text("Не удалось найти товар по запросу: " + article_name);
                    } else if (response.error == "articles") {
                        $('#h6-error-search').html("Нашлось несколько товаров по запросу: <strong>" + article_name + "</strong><br>" +
                            "Выберите один артикул по которому хотите получить список аналогов.");
                        delete response.error;
                        Object.entries(response).forEach((article, index) => {
                            let tr = createArticleElement(article[1], true);
                            $('#tbody-article').append(tr);
                            $('#div-miidle-row').removeClass("d-none");
                        });
                    } else {
                        $('#h6-error-search').text("Неизвестная ошибка!");
                    }
                    $('#h6-error-search').removeClass("d-none");

                } else {
                    $('#h6-error-search').addClass("d-none");

                    let flag = false;
                    // Вывод на страницу результатов поиска
                    response.forEach((article, index) => {
                        if (index >= COUNT_LOADING_ELEMENTS) {
                            flag = true;
                            return true;
                        }
                        let tr = createArticleElement(article);
                        if (index == 0)
                            $('#tbody-article').append(tr);
                        else
                            $('#tbody-analogs').append(tr);
                    });

                    if (flag)
                        $('#div-showMore').removeClass("d-none");

                    analogs = response;

                    $('#btn-parse-result').removeClass("disabled");
                    $('#div-miidle-row').removeClass("d-none");

                    showDivsWhenSearchSuccess();
                }

                $('#div-search-results').removeClass("d-none");
            },
            complete: function() {}
        });

        setSearchType("soft");
    }


    function showMoreArticles() {
        for (let i = COUNT_LOADING_ELEMENTS; i < analogs.length; i++) {
            let tr = createArticleElement(analogs[i]);
            $('#tbody-analogs').append(tr);
        }
        $('#div-showMore').addClass("d-none");
    }


    function parseResult() {
        $('#div-parse-result').removeClass("d-none");

        var formData = new FormData();
        formData.append('analogs', JSON.stringify(analogs));
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


    function openCatologueSites() {
        window.open("https://shop.donaldson.com/store/ru-ru/search?Ntt=" + $('#input-article').val().toUpperCase(), '_blank');
        window.open("https://catalog.filfilter.com.tr/ru/search/" + $('#input-article').val().toUpperCase(), '_blank');
        window.open("https://www.fleetguard.com/s/searchResults?language=en_US&propertyVal=" + $('#input-article').val().toUpperCase(), '_blank');
        window.open("https://catalog.hifi-filter.com/en-GB/search/global/cross-reference?p=1&q=" + $('#input-article').val().toUpperCase(), '_blank');
        window.open("https://catalog.mann-filter.com/EU/rus/", '_blank');

    }

    function showPopoverEdit(student_id) {
        $('#dialogModalEdit').modal('show');
    }


    function ajaxEdit(producer_id, new_producer_name_dsts, new_producer_name) {
        var formData = new FormData();

        formData.append('producer_id', producer_id);
        formData.append('new_producer_name_dsts', new_producer_name_dsts);
        formData.append('new_producer_name', new_producer_name);

        $.ajax({
            type: "POST",
            url: 'edit_action.php#content',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'html',
            success: function(response) {
                response = JSON.parse(response);
                console.log(response);
                if (response.setProducerDSTSName || response.setSimmilarProducer) {
                    searchAnalogs($('#input-article').val());
                }
            },
            complete: function() {}
        });
    }


    //-------------------------------------------------------------------------------------------------------------
    // ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
    //-------------------------------------------------------------------------------------------------------------


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


    function createArticleElement(article, needToChoose = false) {
        // console.log(article);

        let tr = document.createElement("tr");

        let td_artcle_name = document.createElement("td");
        td_artcle_name.classList.add("middleInTable");
        let a = document.createElement("a");
        if (article.hasInfo) {
            a.style.color = "green";
        }
        a.setAttribute('href', 'article_details.php?article_id=' + article.article_id);
        a.textContent = article.article_name;
        td_artcle_name.appendChild(a);

        let td_producer_dsts_name = document.createElement("td");
        td_producer_dsts_name.classList.add("middleInTable");
        if (article.producer_name_dsts == "") {
            td_producer_dsts_name.innerText = article.producer_name;
            td_producer_dsts_name.classList.add("text-danger");
        } else
            td_producer_dsts_name.innerText = article.producer_name_dsts;


        let td_catalogue_name = document.createElement("td");
        td_catalogue_name.classList.add("middleInTable");
        td_catalogue_name.innerText = article.catalogue_name;

        let td_producer_name = document.createElement("td");
        td_producer_name.classList.add("middleInTable");
        td_producer_name.innerText = article.producer_name_by_catalogue + " (" + article.producer_name + ")";

        tr.appendChild(td_artcle_name);
        tr.appendChild(td_producer_dsts_name);
        tr.appendChild(td_catalogue_name);
        tr.appendChild(td_producer_name);

        if (is_admin) {
            let td_edit = document.createElement("td");
            td_edit.classList.add("middleInTable");

            let button = document.createElement("button");
            button.classList.add("badge", "badge-primary", "badge-pill");
            button.addEventListener("click", function() {
                article_for_edit = article;
                setValuesToDialogModalFields(article);
                showPopoverEdit();
            });
            button.style.border = "unset";

            let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.classList.add("bi", "bi-pen-fill");
            svg.setAttribute('width', '16');
            svg.setAttribute('height', '16');
            svg.setAttribute('viewBox', '0 0 16 16');
            svg.setAttribute('fill', 'currentColor');
            svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');

            let path1 = document.createElementNS("http://www.w3.org/2000/svg", 'path');
            path1.setAttribute('d', 'm13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z');

            svg.appendChild(path1);
            button.appendChild(svg);
            td_edit.appendChild(button);

            tr.appendChild(td_edit);
        }

        if (needToChoose) {
            let td_choose = document.createElement("td");
            td_choose.classList.add("middleInTable");

            let button = document.createElement("button");
            button.classList.add("btn", "btn-outline-primary");
            button.innerText = "ВЫБРАТЬ"
            button.addEventListener("click", function() {
                cleanSearchResult();
                $('#input-article').val(article.article_name);
                setSearchType("strict");
                searchAnalogs(article.article_name);
            });

            td_choose.appendChild(button);

            tr.appendChild(td_choose);
        }

        return tr;
    }


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


    function setValuesToDialogModalFields(article) {
        $('#modalEdit-h5-title').text($('#modalEdit-h5-title').text() + article.article_name);
        $('#modalEdit-input-realProducerNameInDSTSCatalogue').val(getProducerNameDSTS(article));
        $('#modalEdit-input-realProducerName').val(article.producer_name);
    }


    function getProducerNameDSTS(article) {
        if (article.producer_name_dsts == "")
            return article.producer_name;
        else
            return article.producer_name_dsts;
    }



    function checkPressCharInSearchField(symbol) {
        let regex = RegExp('[0-9a-zA-Zа-яА-Я. -]');
        if (!regex.test(symbol)) {
            return false;
        }
        return true;
    }

    function setSearchType(new_search_type) {
        search_type = new_search_type;
    }


    function validateSearchField() {
        if (checkSearchField() == false) {
            flagValidation = false;
            $('#btn-search').addClass("disabled");
            $('#p-errorSearchField').removeClass("d-none");
            $('#input-article').addClass("is-invalid");
        } else {
            flagValidation = true;
            $('#btn-search').removeClass("disabled");
            $('#p-errorSearchField').addClass("d-none");
            $('#input-article').removeClass("is-invalid");
        }
    }

    function checkSearchField() {
        let fieldText = $('#input-article').val();
        if (fieldText == "")
            return false;

        let regex = /^[a-zA-Zа-яА-Я0-9. -]+$/;
        if (!regex.test(fieldText)) {
            return false;
        }
        return true;
    }

    function cleanSearchResult() {
        $('#div-search-results').addClass("d-none");
        $('#tbody-analogs').empty();
        $('#tbody-article').empty();
        $('#h6-error-search').addClass("d-none");

        $('#div-miidle-row').addClass("d-none");
        $('#div-select-producers').addClass("d-none");

        $('#btn-parse-result').addClass("disabled");
        $('#div-parse-result').addClass("d-none");
        $('#textarea-parseResult').empty();
        $('#svg-copy').removeClass('d-none');
        $('#svg-copied').addClass('d-none');

        $('#table-analogs').addClass("d-none");

        $('#div-showMore').addClass("d-none");
    }

    function showDivsWhenSearchSuccess() {
        $('#div-select-producers').removeClass("d-none");
        $('#table-analogs').removeClass("d-none");
        $('#div-showMore').removeClass("d-none");
    }
</script>