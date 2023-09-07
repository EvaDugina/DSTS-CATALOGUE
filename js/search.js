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


$(document).ready(function() {
    ajaxGetProducerNames();

    console.log("sessionStorage: " + sessionStorage.getItem('search_request') + " | " + sessionStorage.getItem('search_type'));
    if (sessionStorage.getItem('search_request') && sessionStorage.getItem('search_type')) {
        flagValidation = true;
        $('#input-article').val(sessionStorage.getItem('search_request'));
        search_type = sessionStorage.getItem('search_type');
        search();
    }
});

// $('#input-article').on("change", function(e) {
//         flagValidation = true;

//         SEARCH_REQUEST = $('#input-article').val();
//         // let search_request_splitted = SEARCH_REQUEST.split(" ");
//         if (!hasNumber(SEARCH_REQUEST) && SEARCH_REQUEST.split(" ").length < 2) {
//             offeringProducerNames = findProducerByFragment(SEARCH_REQUEST);
//             refreshAutocomplete(offeringProducerNames);
//         } else {
//             var div = document.getElementById('div-autocomplete');
//             div.classList.add("d-none");
//             div.innerHTML = '';
//         }
//         $('#input-article').focus();
//     // }
// });

function searchSimmilarProducers() {
    flagValidation = true;

    SEARCH_REQUEST = $('#input-article').val().toUpperCase();
    if (!hasNumber(SEARCH_REQUEST) && SEARCH_REQUEST.split(" ").length < 2) {
        offeringProducerNames = findProducerByFragment(SEARCH_REQUEST);
        refreshAutocomplete(offeringProducerNames);
    } else {
        var div = document.getElementById('div-autocomplete');
        div.classList.add("d-none");
        div.innerHTML = '';
    }
    $('#input-article').focus();
}


$('#input-article').on("keydown", function(e) {
    if (e.key == "Enter" || e.keyCode == 13) {
        if (!$('#div-autocomplete').hasClass("d-none")) {
            $('#div-autocomplete').children().each((index, button) => {
                if (button.classList.contains("btn-autocomplete-hover")) {
                    chooseArticleProducer(button.innerText);
                    return true;
                }
            });
        } else
            search();
    } else if (e.ctrlKey) {
        // if (e.key == "Backspace")
        //     SEARCH_REQUEST_PRODUCER_NAME = "";
        return;
    } else {
        if (checkPressKeyUpDownLeftRight(e.key)) {
            if (e.key == "ArrowUp")
                    navigateByArrows(1);
                else if (e.key == "ArrowDown")
                    navigateByArrows(-1);
        } else {
            if(e.key.length > 1)
                return true;
            else if (checkPressCharInSearchField(e.key) == false){
                e.preventDefault();
            } else {
                e.preventDefault();
                $('#input-article').val($('#input-article').val() + e.key.toUpperCase());
                searchSimmilarProducers();
            }
        }
        // if (checkPressCharInSearchField(e.key) == false) {
        //     if (checkPressKeyUpOrDown(e.key) == false) {
        //         e.preventDefault();
        //     } else {
        //         if (e.key == "ArrowUp")
        //             navigateByArrows(1);
        //         else if (e.key == "ArrowDown")
        //             navigateByArrows(-1);
        //     }
        // } else {
        //     flagValidation = true;
        //     e.preventDefault();
        //     // SEARCH_REQUEST += e.key;
        //     if (e.key == "Backspace") {
        //         $('#input-article').val($('#input-article').val().slice(0, -1));
        //         if ($('#input-article').val().split(" ").length < 2)
        //             SEARCH_REQUEST_PRODUCER_NAME = "";
        //     } else
        //         $('#input-article').val($('#input-article').val() + e.key.toUpperCase());

        //     SEARCH_REQUEST = $('#input-article').val();
        //     // let search_request_splitted = SEARCH_REQUEST.split(" ");
        //     if (!hasNumber(SEARCH_REQUEST) && SEARCH_REQUEST_PRODUCER_NAME == "") {
        //         offeringProducerNames = findProducerByFragment(SEARCH_REQUEST);
        //         refreshAutocomplete(offeringProducerNames);
        //     } else {
        //         var div = document.getElementById('div-autocomplete');
        //         div.classList.add("d-none");
        //         div.innerHTML = '';
        //     }
        //     $('#input-article').focus();
        // }
    }
});

function navigateByArrows(step) {
    let now_index_selected = -1;
    $('#div-autocomplete').children().each((index, button) => {
        if (button.classList.contains("btn-autocomplete-hover")) {
            now_index_selected = index;
            button.classList.remove("btn-autocomplete-hover");
            button.classList.add("btn-autocomplete-unhover");
            return true;
        }
    });

    let new_index_selected = now_index_selected - step;
    if ((step == 1 && now_index_selected > 0) || (step == -1 && now_index_selected < $('#div-autocomplete').children().length - 1)) {
        // $('#div-autocomplete').children()[now_index_selected].classList.remove("bg-primary", "text-white");
        // $('#div-autocomplete').children()[now_index_selected].classList.add("bg-white", "text-primary");
        // $('#div-autocomplete').children()[new_index_selected].classList.remove("bg-white", "text-primary");
        // $('#div-autocomplete').children()[new_index_selected].classList.add("bg-primary", "text-white");
        $('#div-autocomplete').children()[new_index_selected].classList.remove("btn-autocomplete-unhover");
        $('#div-autocomplete').children()[new_index_selected].classList.add("btn-autocomplete-hover");
    }
}



function refreshAutocomplete(offeringProducerNames) {
    var div = document.getElementById('div-autocomplete');
    div.classList.add("d-none");
    div.innerHTML = '';

    offeringProducerNames.forEach((producer_name, index) => {
        let button = document.createElement("button");
        button.id = "btn-autocomplete-" + index;
        button.classList.add("list-group-item", "list-group-item-action", "border", "border-primary");
        if (index == 0)
            button.classList.add("btn-autocomplete-hover");
        else
            button.classList.add("btn-autocomplete-unhover");

        button.addEventListener("mouseover", function() {
            $('#div-autocomplete').children().each((index, button) => {
                if (button.classList.contains("btn-autocomplete-hover")) {
                    button.classList.remove("btn-autocomplete-hover");
                    button.classList.add("btn-autocomplete-unhover");
                    return true;
                }
            });
            button.classList.remove("btn-autocomplete-unhover");
            button.classList.add("btn-autocomplete-hover");
        });
        // button.hover();
        //     button.classList.add("bg-primary", "text-white");
        // else
        // button.classList.add("bg-white", "text-primary");

        button.innerText = producer_name;
        button.setAttribute("onclick", "chooseArticleProducer('" + producer_name + "')");
        div.appendChild(button);
    });

    div.classList.remove("d-none");
}

// function handler(e) {
//     console.log(e.which);
//     active.classList.remove("hover");
//     if (e.which == 40) {
//         active = active.nextElementSibling || active;
//     } else if (e.which == 38) {
//         active = active.previousElementSibling || active;
//     } else {
//         active = e.target;
//     }
//     active.classList.add("hover");
// }

function chooseArticleProducer(producer_name) {
    let producer_name_splitted = producer_name.split(" ");
    if (producer_name_splitted.length > 1) {
        producer_name = "";
        producer_name_splitted.forEach((name_part, index) => {
            if (index != 0)
                producer_name += "+";
            producer_name += name_part;
        });
    }

    SEARCH_REQUEST_PRODUCER_NAME = producer_name;
    $('#input-article').val(SEARCH_REQUEST_PRODUCER_NAME + " ");
    var div = document.getElementById('div-autocomplete');
    div.classList.add("d-none");
    div.innerHTML = '';
    $('#input-article').focus();
}

function findProducerByFragment(fragment_str) {
    let LIMIT_COUNT_OFFERING_PRODUCERS = 5;
    simmilar_producer_names = [];
    if (fragment_str != "") {
        let count = 0;
        if (fragment_str.includes("+"))
            fragment_str = fragment_str.replace("+", " ");
        producers_names.forEach((producer_name) => {
            if (count >= LIMIT_COUNT_OFFERING_PRODUCERS)
                return true;
            if (producer_name.includes(fragment_str)) {
                simmilar_producer_names.push(producer_name);
                count += 1;
            }
        });
    }
    return simmilar_producer_names;
}

function checkPressCharInSearchField(symbol) {
    let regex = RegExp('[0-9a-zA-Zа-яА-Я+]');
    if (symbol == "Backspace")
        return true;
    if (!regex.test(symbol) || symbol.length > 1) {
        return false;
    }
    return true;
}

function checkPressKeyUpDownLeftRight(key) {
    if (key != "ArrowUp" && key != "ArrowDown" && key != "ArrowLeft" && key != "ArrowRight")
        return false;
    else
        return true;
}

function hasNumber(myString) {
    return /\d/.test(myString);
}



function search() {
    validateSearchField();
    cleanSearchResult();
    if (flagValidation) {
        let search_request = $('#input-article').val();
        let search_request_array = search_request.split(" ");
        if (search_request_array.length > 1)
            searchAnalogs(search_request_array[1], search_request_array[0]);
        if (search_request_array.length == 1)
            searchAnalogs(search_request_array[0]);
    }
}

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
        success: function(response) {
            // console.log(response);
            response = JSON.parse(response);

            all_analogs = [];

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

            $('#div-analogResults').removeClass("d-none");

            $('#spinner-waiting-search').addClass("d-none");
        },
        complete: function() {}
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
        success: function(response) {
            producers_names = JSON.parse(response);
            // console.log(producers_names);
        },
        complete: function() {}
    });
}


function showMoreArticles() {
    for (let i = COUNT_LOADING_ELEMENTS; i < analogs.length; i++) {
        let tr = createArticleElement(analogs[i]);
        $('#tbody-analogs').append(tr);
    }
    $('#div-analogs-showMore').addClass("d-none");
}


function createArticleElement(article, needToChoose = false) {
    // console.log(article);

    let tr = document.createElement("tr");

    let td_artcle_name = document.createElement("td");
    td_artcle_name.classList.add("middleInTable");
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
    td_producer_name.innerHTML = "<span>" + article.producer_name_by_catalogue +
        " (<strong style='font-weight: bold;'>" + article.producer_name + "</strong>)</span>";

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
            setValuesToDialogModalEditFields(article);
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
        // $('#th-choose').removeClass("d-none");

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

        $('#th-choose').removeClass("d-none");
    } else {
        $('#th-choose').addClass("d-none");
    }

    return tr;
}


function goToArticleDetails(article_id) {
    updateSessionParams();
    document.location.href = 'article_details.php?article_id=' + article_id;
}

function setSearchType(new_search_type) {
    search_type = new_search_type;
}


function validateSearchField() {
    if (checkSearchField() == false) {
        flagValidation = false;
        // $('#btn-search').addClass("disabled");
        $('#p-errorSearchField').removeClass("d-none");
        $('#input-article').addClass("is-invalid");
    } else {
        flagValidation = true;
        // $('#btn-search').removeClass("disabled");
        $('#p-errorSearchField').addClass("d-none");
        $('#input-article').removeClass("is-invalid");
    }
}

function checkSearchField() {
    let fieldText = $('#input-article').val();
    if (fieldText == "")
        return false;

    let regex = /^[a-zA-Zа-яА-Я0-9. -+]+$/;
    let array_search_request = fieldText.split(" ");
    if (!regex.test(fieldText) || array_search_request.length > 2 ||
        (array_search_request.length == 1 && SEARCH_REQUEST_PRODUCER_NAME != "") ||
        (array_search_request.length == 2 && array_search_request[1] == "")) {
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