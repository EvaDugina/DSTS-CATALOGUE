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
    <?php show_header($dbconnect, 'Вход в систему');?>
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
            <?php if($au->isAdmin()) {?>
                <div class="col-4">
                    <button id="btn-parse-result" class="btn btn-warning text-black w-100 disabled" onclick="parseResult()">ОБРАБОТАТЬ РЕЗУЛЬТАТ В СТРОКУ ДЛЯ 1С</button>
                </div>
            <?php }?>
        </div>
        <p id="p-errorSearchField" class="text-danger d-none mb-0 pb-0"><small>В строке поиска присутсвуют недопустимые символы!</small></p>
        <p class="text-muted"><small>ПРИМЕР ВВОДА: P550777 | P 550777 | P-550777 | P.550777</small></p>

        <div class="row">
            <div class="col-8">
                <ul id="ul-article" class="list-group">
                    
                </ul>
            </div>
        </div>
        
        </br>
                        
        <div id="div-search-results" class="row d-none">
            <div class="col-8">
                <h5>РЕЗУЛЬТАТЫ ПОИСКА<!-- по артикулу <strong id="p-strong-articleName"></strong>-->:</h5> 
                <h6 id="h6-error-search" class="text-danger d-none"></h6>
                <ul id="ul-analogs" class="list-group">
                    <!-- <li class="list-group-item">
                        <div class="d-inline-flex w-100 align-items-center justify-content-between">
                                <div class="flex-grow-1 me-2">
                                    АРТИКУЛ
                                </div>
                                <div class="text-primary me-2">
                                    ПРОИЗВОДИТЕЛЬ
                                </div>
                            <?php 
                            if($au->isAdmin()) {?>
                            <div class="ms-5">
                                <a class="badge badge-primary badge-pill" style="cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                                    </svg>
                                </a>
                            </div>
                            <?php }?>
                        </div>
                    </li> -->
                </ul>
            </div>
            <?php if($au->isAdmin()) {?>
                <div class="col-4">
                    <div id="div-parse-result" class="d-none">
                        <div class="d-inline-flex w-100 align-items-center justify-content-between mb-1">
                            <h5 class="mb-0">РЕЗУЛЬТАТ ОБРАБОТКИ:</h5>
                            <button id="btn-copy-parse-result" class="btn btn-outline text-white bg-primary" 
                            data-toggle="tooltip" title="Копировать!" onclick="copyParseResult()">
                                <svg id="svg-copy" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                    <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                    <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                </svg>
                                <svg id="svg-copied" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg d-none" viewBox="0 0 16 16">
                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="w-auto" style="position: relative;">
                            <textarea id="textarea-parseResult" class="form-control" rows="30" readonly="true"></textarea>
                        </div>
                    </div>
                </div>
            <?php }?>
        </div>

    </div>


    </main>
</body>


<script type="text/javascript">


    var analogs = [];

    var flagValidation = false;

    //-------------------------------------------------------------------------------------------------------------
    // ОБРАБОТЧИКИ СОБЫТИЙ
    //-------------------------------------------------------------------------------------------------------------

    $('#input-article').on("change", function (e) {
        validateSearchField();
    });

    $('#input-article').on("keydown", function (e) {
        if (e.key == "Enter" || e.keyCode == 13) {
            validateSearchField();
            if (flagValidation)
                searchAnalogs();
            else
                cleanSearchResult();
        } else {
            if(checkPressCharInSearchField(e.key) == false) {
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
        }, function() {
            $(this).removeClass("bg-white");
            $(this).addClass("bg-primary");
            $(this).removeClass("text-primary");
            $(this).addClass("text-white");
        }
    );


    //-------------------------------------------------------------------------------------------------------------
    // ОСНОВНЫЕ ФУНКЦИИ
    //-------------------------------------------------------------------------------------------------------------


    function searchAnalogs() {
        console.log("searchAnalogs()");

        cleanSearchResult();

        if(!flagValidation)
            return;

        var formData = new FormData();

        let article_name = $('#input-article').val().toUpperCase();
        // $('#p-strong-articleName').text(article_name);

        if(article_name == "") {
            return;
        }

        formData.append('article_name', article_name);

        $.ajax({
        type: "POST",
        url: 'search_action.php#content',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType : 'html',
        success: function(response) {
            // console.log(response);
            response = JSON.parse(response);

            // Обработка и вывод ошибок поиска
            if(Object.keys(response).length == 1) {
                console.log("ERROR!");
                if(response[0].error == "article_id") {
                    $('#h6-error-search').text("Не удалось найти товар по артикулу: " + article_name );
                } else {
                    $('#h6-error-search').text("Неизвестная ошибка!");
                }
                $('#h6-error-search').removeClass("d-none");
                
            } else {
                $('#h6-error-search').addClass("d-none");

                // Вывод на страницу результатов поиска
                response.forEach((article, index) => {

                    let li = document.createElement("li");
                    li.classList.add("list-group-item");

                    let div = document.createElement("div");
                    div.classList.add("d-inline-flex", "w-100", "align-items-center", "justify-content-between");

                    let div_artcle_name = document.createElement("div");
                    div_artcle_name.classList.add("flex-grow-1", "me-2");
                    div_artcle_name.innerText = article.article_name;

                    let div_producer_name = document.createElement("div");
                    div_producer_name.classList.add("text-primary", "me-2");
                    div_producer_name.innerText = article.producer_name;

                    div.appendChild(div_artcle_name);
                    div.appendChild(div_producer_name);

                    if (is_admin) {
                        // <div class="ms-5">
                        //     <a class="badge badge-primary badge-pill" style="cursor: pointer;">
                        //         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                        //             <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                        //         </svg>
                        //     </a>
                        // </div>
                        
                        let div_edit = document.createElement("div");
                        div_edit.classList.add("ms-5");

                        let a_edit_badge = document.createElement("a");
                        a_edit_badge.classList.add("badge", "badge-primary", "badge-pill");
                        a_edit_badge.style = "cursor: pointer;";
                        
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
                        a_edit_badge.appendChild(svg);
                        div_edit.appendChild(a_edit_badge);

                        div.appendChild(div_edit);
                    }

                    li.appendChild(div);

                    if (index==0)
                        $('#ul-article').append(li);
                    else 
                        $('#ul-analogs').append(li);
                });

                analogs = JSON.stringify(response);

                $('#btn-parse-result').removeClass("disabled");
            }

            $('#div-search-results').removeClass("d-none");
        },
        complete: function() {
        }
        });
  }


  function parseResult() {
    $('#div-parse-result').removeClass("d-none");

    var formData = new FormData();
    formData.append('analogs_json', analogs);
    
    $.ajax({
        type: "POST",
        url: 'parse_action.php#content',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType : 'html',
        success: function(response) {
            // console.log(response);
            $('#textarea-parseResult').text(response);
        },
        complete: function() {
        }
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
            // console.log('Скопировано!');
        } catch (err) {
            console.log('Ошибка копирования!');
        }

        if (window.getSelection) {
        if (window.getSelection().empty) {  // Chrome
            window.getSelection().empty();
        } else if (window.getSelection().removeAllRanges) {  // Firefox
            window.getSelection().removeAllRanges();
        }
        } else if (document.selection) {  // IE?
        document.selection.empty();
        }

        $('#svg-copy').addClass('d-none');
        $('#svg-copied').removeClass('d-none');

        $('#textarea-parseResult').css("box-shadow", "inset 0 0 100px 100px rgba(255, 255, 255, 0.7)");
        setTimeout(function(){
            $('#textarea-parseResult').css("box-shadow", "unset");
            }, 250);


        $('#btn-copy-parse-result').attr("title", "Скопировано!");

    }



  function checkPressCharInSearchField(symbol) {
    let regex = RegExp('[0-9a-zA-Zа-яА-Я. -]');
    if(!regex.test(symbol)) {
        return false;
    }
    return true;
  }

  
    function validateSearchField() {
      if(checkSearchField() == false) {
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
      if(fieldText == "")
          return false;
  
      let regex = /^[a-zA-Zа-яА-Я0-9. -]+$/;
      if(!regex.test(fieldText)) {
          return false;
      }
      return true;
    }

    function cleanSearchResult() {
        $('#div-search-results').addClass("d-none");
        $('#ul-analogs').empty();
        $('#h6-error-search').addClass("d-none");

        $('#btn-parse-result').addClass("disabled");
        $('#div-parse-result').addClass("d-none");
        $('#textarea-parseResult').empty();
        $('#svg-copy').removeClass('d-none');
        $('#svg-copied').addClass('d-none');

    }


</script>