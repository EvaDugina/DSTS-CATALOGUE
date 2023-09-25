// import { clickToButtonEditLine } from './PopoverHandler.js';


var analogs = [];
var producers_names = [];

var SEARCH_REQUEST = "";
var SEARCH_REQUEST_PRODUCER_NAME = "";
var SEARCH_REQUEST_ARTICLE_NAME = "";
var offeringProducerNames = [];

var search_type = "soft";
var last_search_type = "soft";

var flagValidation = false;

var COUNT_LOADING_ELEMENTS = 20;


function searchAnalogs(article_name = "", producer_name = "") {
    console.log("searchAnalogs()");

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
    if (producer_name != "") {
        formData.append('producer_name', producer_name);
    }

    $('#spinner-waiting-search').removeClass("d-none");

    $.ajax({
        type: "POST",
        url: 'search_action.php#content',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'html',
        success: function (response) {
            // console.log(response);
            response = JSON.parse(response);

            let all_analogs = [];

            // Обработка и вывод ошибок поиска
            if (response.error) {
                console.log("ERROR!");
                if (response.error == "article_id") {
                    $('#h6-error-search').text("Не удалось найти товар по запросу: " + article_name);
                } else if (response.error == "group_id") {
                    $('#h6-error-search').text("Aналогие не найдены!");
                    delete response.error;
                    Object.entries(response).forEach((article, index) => {
                        let tr = createArticleElement(article[1], true);
                        $('#tbody-article').append(tr);
                        $('#div-miidle-row').removeClass("d-none");
                    });
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

                // Вывод на страницу результатов поиска
                let count_0 = 0;
                let count_1 = 0;
                let count_2 = 0;
                response.forEach((article, index) => {

                    if (article.status == 0) {
                        all_analogs.push(article);
                        count_0 += 1;
                        let tr = createArticleElement(article);
                        $('#tbody-article').append(tr);
                    } else if (article.status == 1) {
                        count_1 += 1;
                        let tr = createArticleElement(article);
                        $('#tbody-main-analogs').append(tr);
                    } else {
                        all_analogs.push(article);
                        count_2 += 1;
                        if (count_2 < COUNT_LOADING_ELEMENTS) {
                            let tr = createArticleElement(article);
                            $('#tbody-analogs').append(tr);
                        }
                    }

                });

                if (count_1 < 1) {
                    $('#div-main-analogs').addClass("d-none");
                } else {
                    $('#div-main-analogs').removeClass("d-none");
                }


                if (count_2 >= COUNT_LOADING_ELEMENTS)
                    $('#div-analogs-showMore').removeClass("d-none");

                analogs = all_analogs;

                $('#btn-parse-result').removeClass("disabled");
                $('#div-miidle-row').removeClass("d-none");

                showDivsWhenSearchSuccess();
            }

            $('#div-goto-producers').removeClass("d-none");

            $('#div-analogResults').removeClass("d-none");

            $('#spinner-waiting-search').addClass("d-none");
        },
        complete: function () { }
    });

    last_search_type = search_type;
    setSearchType("soft");
}

function ajaxGetProducerNames() {
    var formData = new FormData();

    formData.append('getProducersNameDsts', true);

    $.ajax({
        type: "POST",
        url: 'search_action.php#content',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'html',
        success: function (response) {
            producers_names = JSON.parse(response);
            // console.log(producers_names);
        },
        complete: function () { }
    });
}


function showMoreArticles(analogs) {
    for (let i = COUNT_LOADING_ELEMENTS; i < analogs.length; i++) {
        let tr = createArticleElement(analogs[i]);
        $('#tbody-analogs').append(tr);
    }
    $('#div-analogs-showMore').addClass("d-none");
}


function createArticleElement(article, needToChoose = false) {
    // console.log(article);

    let tr = document.createElement("tr");
    tr.classList.add("border");

    let td_artcle_name = document.createElement("td");
    td_artcle_name.classList.add("middleInTable", "col-2");
    let button = document.createElement("button");
    if (article.hasInfo) {
        button.style.color = "green";
    }
    button.setAttribute('onclick', 'goToArticleDetails(' + article.article_id + ")");
    button.classList.add("btn", "btn-link");
    button.textContent = article.article_name;
    button.style.fontSize = "inherit";
    td_artcle_name.appendChild(button);

    let td_producer_dsts_name = document.createElement("td");
    td_producer_dsts_name.classList.add("middleInTable", "col-2");
    if (article.producer_name_dsts == "") {
        td_producer_dsts_name.innerText = article.producer_name;
        td_producer_dsts_name.classList.add("text-danger");
    } else
        td_producer_dsts_name.innerText = article.producer_name_dsts;


    let td_catalogue_name = document.createElement("td");
    td_catalogue_name.classList.add("middleInTable", "col-3");
    td_catalogue_name.innerText = article.catalogue_name;

    tr.appendChild(td_artcle_name);
    tr.appendChild(td_producer_dsts_name);

    if (is_admin) {

        let td_producer_name = document.createElement("td");
        td_producer_name.classList.add("middleInTable", "col-4");
        td_producer_name.innerHTML = "<span>" + article.producer_name_by_catalogue +
            " (<strong style='font-weight: bold;'>" + article.producer_name + "</strong>)</span>";

        let td_edit = document.createElement("td");
        td_edit.classList.add("middleInTable", "col-1");

        let button = document.createElement("button");
        button.classList.add("badge", "badge-primary", "badge-pill");
        button.addEventListener("click", function () {
            clickToButtonEditLine();
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

        tr.appendChild(td_catalogue_name);
        tr.appendChild(td_producer_name);
        tr.appendChild(td_edit);
    } else {
        let td_type = document.createElement("td");
        td_type.classList.add("middleInTable", "col-6");
        if (article.description != "")
            td_type.innerHTML = article.description;
        else
            td_type.innerHTML = "(тип неопределён)";
        tr.appendChild(td_type);
    }


    if (needToChoose) {
        // $('#th-choose').removeClass("d-none");

        let td_choose = document.createElement("td");
        td_choose.classList.add("middleInTable");

        let button = document.createElement("button");
        button.classList.add("btn", "btn-outline-primary");
        button.innerText = "ВЫБРАТЬ"
        button.addEventListener("click", function () {
            cleanSearchResult();
            $('#input-article').val(article.article_name);
            setSearchType("strict");
            searchAnalogs(article.article_name);
        });

        td_choose.appendChild(button);

        tr.appendChild(td_choose);

        $('#th-choose').removeClass("d-none");
    } else {
        $('#th-choose').addClass("d-none");
    }

    return tr;
}

// function clickToButtonEditLine(article) {
//     article_for_edit = article;
//     setValuesToDialogModalEditFields(article);
//     showPopoverEdit();
// }


function goToArticleDetails(article_id) {
    document.location.href = 'article_details.php?article_id=' + article_id;
}

function setSearchType(new_search_type) {
    search_type = new_search_type;
}


function validateSearchField() {
    if (checkSearchFieldSymbols() == false) {
        flagValidation = false;
        // $('#btn-search').addClass("disabled");
        showInputError("В поисковом запросе присутствуют недопустимые символы!");
    } else {
        flagValidation = true;
        // $('#btn-search').removeClass("disabled");
        hideInputError();
    }
}

function showInputError(error_text) {
    $('#p-errorSearchField').text(error_text);
    $('#p-errorSearchField').removeClass("d-none");
    $('#input-article').addClass("is-invalid");
}
function hideInputError() {
    $('#p-errorSearchField').addClass("d-none");
    $('#input-article').removeClass("is-invalid");
}

function checkSearchField() {
    let array_search_request = fieldText.split(" ");
    if (array_search_request.length > 2 || (array_search_request.length == 2 && array_search_request[1] == ""))
        return false;
    return true;
}

function checkSearchFieldSymbols() {
    let fieldText = $('#input-article').val();
    if (fieldText == "")
        return false;

    let regex = /^[a-zA-Zа-яА-Я0-9. -+]+$/;
    if (!regex.test(fieldText)) {
        return false;
    }
    return true;
}

function cleanSearchResult() {
    $('#div-analogResults').addClass("d-none");
    cleanTables();
    $('#h6-error-search').addClass("d-none");

    $('#div-miidle-row').addClass("d-none");
    $('#div-select-producers').addClass("d-none");
    $('#div-goto-producers').addClass("d-none");

    $('#btn-parse-result').addClass("disabled");
    $('#div-parse-result').addClass("d-none");
    $('#textarea-parseResult').empty();
    $('#svg-copy').removeClass('d-none');
    $('#svg-copied').addClass('d-none');

    hideAnalogTables();

    $('#div-analogs-showMore').addClass("d-none");
}

function showDivsWhenSearchSuccess() {
    $('#div-select-producers').removeClass("d-none");
    $('#table-analogs').removeClass("d-none");
    $('#table-main-analogs').removeClass("d-none");
    $('#div-analogs-showMore').removeClass("d-none");
}

function showChooseArticle() {

}

function cleanTables() {
    $('#tbody-article').empty();
    $('#tbody-main-analogs').empty();
    $('#tbody-analogs').empty();
}

function hideAnalogTables() {
    $('#table-analogs').addClass("d-none");
    $('#table-main-analogs').addClass("d-none");
}