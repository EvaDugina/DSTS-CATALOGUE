var article_for_edit = null;
var flag_addingArticle = false;
const functions = {};



// 
// LISTENERS
// LISTENERS
// 

$('#btn-add-article').on("click", function () {
    setValuesToDialogModalAddArticleFields();
    showPopoverAddArticle();
});

$('#modalEdit-button-apply').on("click", function (event) {
    real_producer_name_dsts = $('#modalEdit-input-realProducerNameInDSTSCatalogue').val();
    new_producer_name_dsts = $('#modalEdit-input-newProducerNameInDSTSCatalogue').val();
    real_producer_name = $('#modalEdit-input-realProducerName').val();
    new_producer_name = $('#modalEdit-select-newProducerName').val();
    ajaxEdit(article_for_edit.producer_id, new_producer_name_dsts, new_producer_name, real_producer_name_dsts, real_producer_name);
    // updateTablesAfterEditProducerName(real_producer_name, new_producer_name);
    // updateSessionParams();
    // location.reload();
});

$('#dialogModalEdit').on('hidden.bs.modal', function (e) {
    $('#modalEdit-input-newProducerNameInDSTSCatalogue').val("");
    $('#modalEdit-select-newProducerName').val("");
});

$('#modalAddArticle-button-apply').on("click", function (event) {
    let search_requests = [];
    let divs = $('.div-article-input');
    var flag = true;
    divs.each(function () {
        let article_name = $(this).children(".input-article-name").val();
        article_name.replace(" ", "").replace("-", "").replace(".", "").trim();
        if (article_name == "") {
            $('#modalAddArticle-p-inputError').removeClass("d-none");
            $('#modalAddArticle-input-articleName').addClass("is-invalid");
            $(this).children(".input-article-name").addClass("bg-danger");
            flag = false;
            return;
        }
        $(this).children(".input-article-name").removeClass("bg-danger");
        let catalogue_name = $(this).children('.select-catalogue-name').val();
        search_requests.push([catalogue_name, article_name]);
    });

    if (flag == false)
        return;
    else {
        $('#modalAddArticle-p-inputError').addClass("d-none");
        $('#modalAddArticle-input-articleName').removeClass("is-invalid");
    }

    flag_addingArticle = true;
    functions.sendSearchRequest(search_requests);

    waitWhileSearchEnds();

});

$('#modalAddArticle-button-reject').on("click", function (event) {
    event.preventDefault();
    let flag = confirm("Вы уверены, что хотети отменить процедуру поиска?")
    if (flag) {
        functions.sendStopSearchRequest();
        $('#dialogModalAddArticle').modal('hide');
    }

});

$('#dialogModalAddArticle').on('hidden.bs.modal', function (event) {
    if (flag_addingArticle) {
        event.preventDefault();
        var confirm = window.confirm("Идёт добавление артикула, вы уверены, что хотите закрыть окно?");
        if (confirm)
            ajaxStopAddArticle();
        else
            return;
    }
    $('#modalAddArticle-div-result').html("");
    $('#modalAddArticle-div-result').addClass("d-none");
    $('#dialogModalAddArticle').modal('hide');

});

// 
// 
// 
// 

function sleep(s) {
    return new Promise(resolve => setTimeout(resolve, s * 1000));
}

function showPopoverEdit(student_id) {
    $('#dialogModalEdit').modal('show');
}

function showPopoverAddArticle() {
    $('#dialogModalAddArticle').modal('show');
}

async function waitWhileSearchEnds() {
    $('#modalAddArticle-div-result').removeClass("d-none");
    while (true) {
        server_data = await functions.sendGetSearchProgressRequest();
        if (server_data.error) {
            console.log(server_data.error);
            functions.reconnect();
            continue;
        }

        console.log(server_data.progress);
        let html = ajaxGenerateHtmlArticleElements(JSON.stringify(server_data.progress));

        $('#modalAddArticle-div-result').html(html);
        if (server_data.flag_end)
            break;

        await sleep(2);
    }
}


function deleteCurrentInputBlock(context) {
    context.parentElement.remove();
}

function addNewArticleInputBlock() {
    let div_new_input = $('#div-first-article-input').clone();
    let count_inputs = $('.div-article-input').length;
    div_new_input.attr('id', "div-article-input-" + count_inputs);
    div_new_input.addClass("mb-3");
    div_new_input.children('.input-article-name').val("");
    div_new_input.children(".input-article-name").removeClass("bg-danger");
    div_new_input.children('.input-article-name').attr('id', "modalAddArticle-input-articleName-" + count_inputs);
    div_new_input.children('#modalAddArticle-first-select-catalogueName').attr('id', "modalAddArticle-select-articleName-" + count_inputs);
    div_new_input.children('#button-delete-current-input').removeClass("d-none");
    let div_where_add = $('#div-more-article-inputs');
    div_where_add.append(div_new_input);
}

function updateTablesAfterEditProducerNameDSTS(last_producer_name_dsts, new_producer_name_dsts) {
    analogs.forEach((article) => {
        if (article.producer_name_dsts == last_producer_name_dsts)
            article.producer_name_dsts = new_producer_name_dsts;
    });

    dynamicUpdateTablesAfterChangeProducerNameDSTS("tbody-article", last_producer_name_dsts, new_producer_name_dsts);
    dynamicUpdateTablesAfterChangeProducerNameDSTS("tbody-main-analogs", last_producer_name_dsts, new_producer_name_dsts);
    dynamicUpdateTablesAfterChangeProducerNameDSTS("tbody-analogs", last_producer_name_dsts, new_producer_name_dsts);
}

function updateTablesAfterEditProducerName(last_producer_name, new_producer_name) {
    analogs.forEach((article) => {
        if (article.producer_name == last_producer_name)
            article.producer_name = new_producer_name;
    });

    dynamicUpdateTablesAfterChangeProducerName("tbody-article", last_producer_name, new_producer_name);
    dynamicUpdateTablesAfterChangeProducerName("tbody-main-analogs", last_producer_name, new_producer_name);
    dynamicUpdateTablesAfterChangeProducerName("tbody-analogs", last_producer_name, new_producer_name);


}

function dynamicUpdateTablesAfterChangeProducerNameDSTS(id, last_producer_name_dsts, new_producer_name_dsts) {
    $("#" + id).children().each((index, tr) => {
        // console.log(tr);
        let td_producer_name_dsts = tr.children[1];
        if (td_producer_name_dsts.innerText == last_producer_name_dsts) {
            tr.children[1].innerText = new_producer_name_dsts;
            tr.children[1].classList.remove("text-danger");
        }
    });
}

function dynamicUpdateTablesAfterChangeProducerName(id, last_producer_name, new_producer_name) {
    $("#" + id).children().each((index, tr) => {
        // console.log(tr);
        let strong = tr.children[3].getElementsByTagName("strong")[0];
        let producer_name = strong.innerText;
        if (producer_name == last_producer_name) {
            let new_value = tr.children[3].innerText.split("(")[0] +
                "(<strong style='font-weight: bold;'>" + new_producer_name + "</strong>)";
            tr.children[3].innerHTML = new_value;
        }
    });
}

function setValuesToDialogModalEditFields(article) {
    $('#modalEdit-h5-title').text($('#modalEdit-h5-title').text() + article.article_name);
    $('#modalEdit-input-realProducerNameInDSTSCatalogue').val(getProducerNameDSTS(article));
    $('#modalEdit-input-realProducerName').val(article.producer_name);
}

function setValuesToDialogModalAddArticleFields(article) {
    $('#modalAddArticle-input-articleName').val($('#input-article').val());
}

function getProducerNameDSTS(article) {
    if (article.producer_name_dsts == "")
        return article.producer_name;
    else
        return article.producer_name_dsts;
}

function clickToButtonEditLine(article) {
    article_for_edit = article;
    setValuesToDialogModalEditFields(article);
    showPopoverEdit();
}


// 
// AJAX
// AJAX
// 

function ajaxGenerateHtmlArticleElements(progress_data) {
    var formData = new FormData();

    formData.append('flag', "generateSearchLogResultArticleElements");
    formData.append('data', progress_data);

    html = "";
    $.ajax({
        type: "POST",
        url: 'htmlHandler.php#content',
        cache: false,
        contentType: false,
        processData: false,
        async: false,
        data: formData,
        dataType: 'html',
        success: function (response) {
            html = response;
        },
        complete: function () { }
    });

    return html;
}

function ajaxEdit(producer_id, new_producer_name_dsts, new_producer_name, real_producer_name_dsts, real_producer_name) {
    var formData = new FormData();

    formData.append('producer_id', producer_id);
    formData.append('new_producer_name_dsts', new_producer_name_dsts);
    formData.append('new_producer_name', new_producer_name);

    $('#modalEdit-spinner-waiting').removeClass("d-none");

    $.ajax({
        type: "POST",
        url: 'edit_action.php#content',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'html',
        success: function (response) {
            response = JSON.parse(response);
            console.log(response);
            if (response.setProducerDSTSName || response.setSimmilarProducer) {
                // searchAnalogs($('#input-article').val());
                if (response.setProducerDSTSName)
                    updateTablesAfterEditProducerNameDSTS(real_producer_name_dsts, new_producer_name_dsts);
                if (response.setSimmilarProducer)
                    updateTablesAfterEditProducerName(real_producer_name, new_producer_name);
                $('#modalEdit-spinner-waiting').addClass("d-none");
                $('#dialogModalEdit').modal('hide');
            }
        },
        complete: function () { }
    });
}

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
//         success: function (response) {
//             // response = JSON.parse(response);
//             $('#modalAddArticle-spinner-waiting').addClass("d-none");
//             $('#modalAddArticle-div-result').text(response);
//             $('#modalAddArticle-div-result').removeClass("d-none");
//             flag_addingArticle = false;
//             search();
//         },
//         complete: function () { }
//     });
// }

function ajaxStopAddArticle() {

    window.close();

    // var formData = new FormData();

    // formData.append('code_stop', 1);

    // $.ajax({
    //     type: "POST",
    //     url: 'addArticle_action.php#content',
    //     cache: false,
    //     contentType: false,
    //     processData: false,
    //     data: formData,
    //     dataType: 'html',
    //     success: function(response) {
    //         // response = JSON.parse(response);
    //         $('#modalAddArticle-spinner-waiting').removeClass("d-none");
    //         flag_addingArticle = false;
    //     },
    //     complete: function() {}
    // });
}