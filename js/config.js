export function getUrl() {
    let data = ajaxGetServerParams();
    let url = `ws://${data['host']}:${data['port']}`;
    return url;
}


////
//// UTILITIES
////

function ajaxGetServerParams() {

    var formData = new FormData();

    formData.append('flag', "GetServerParameters");

    let host = null;
    let port = null;

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
            host = response.host;
            port = response.port;
        },
        complete: function () { }
    });

    return { "host": host, "port": port }
}
