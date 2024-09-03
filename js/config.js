var HOST = null;
var PORT = null;

function getServerParams() {
    ajaxGetServerParams();
}

getServerParams();

const $url = `ws://${HOST}:${PORT}`;
export default $url;


////
//// UTILITIES
////


function ajaxGetServerParams() {

    var formData = new FormData();

    formData.append('flag', "GetServerParameters");

    $.ajax({
        type: "POST",
        url: 'daemonHandler.php#content',
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'html',
        success: function (response) {
            response = JSON.parse(response);
            HOST = response.host;
            PORT = response.port;
        },
        complete: function () { }
    });
}
