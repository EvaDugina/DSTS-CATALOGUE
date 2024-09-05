<?php
require_once("utilities.php");
require_once("common.php");

// $au = new auth_ssh();
// checkAuLoggedIN($au);


show_head("СТРАНИЦА ИНФОРМАЦИИ О ТОВАРЕ");
?>

<body style="overflow-x: hidden;">
    <main class="d-flex flex-column">

        <div class="d-flex mb-3">
            <!-- <button class="btn btn-success me-3" onclick="startDaemon()">START DAEMON</button>
            <button class="btn btn-danger me-3" onclick="stopDaemon()">STOP DAEMON</button> -->
            <button class="btn btn-primary" onclick="functions.checkConnectionToServer()">CHECK CONNECTION!</button>
        </div>

        <div class="d-flex">
            <button class="btn btn-success me-3" onclick="functions.sendSearchRequest()">START SEARCHING</button>
            <button class="btn btn-danger me-3" onclick="functions.sendStopSearchRequest()">STOP SEARCHING</button>
            <button class="btn btn-primary" onclick="functions.sendGetSearchProgressRequest()">GET SEARCH PROGRESS</button>
        </div>

    </main>
</body>


<script type="text/javascript">
    const functions = {};

    // function startDaemon() {
    //     ajaxStartStopDaemon(true);
    // }

    // function stopDaemon() {
    //     ajaxStartStopDaemon(false);
    // }


    // function sleep(s) {
    //     return new Promise(resolve => setTimeout(resolve, s * 1000));
    // }

    // function ajaxStartStopDaemon(flag) {

    //     var formData = new FormData();

    //     if (flag)
    //         formData.append('flag', "StartDaemon");
    //     else
    //         formData.append('flag', "StopDaemon");

    //     $.ajax({
    //         type: "POST",
    //         url: 'daemonHandler.php#content',
    //         cache: false,
    //         async: false,
    //         contentType: false,
    //         processData: false,
    //         data: formData,
    //         dataType: 'html',
    //         success: function(response) {
    //             console.log(response);
    //         },
    //         complete: function() {}
    //     });
    // }
</script>
<script type="module">
    import ServerHandler from "./js/ServerHandler.js";

    var serverHandler = new ServerHandler();

    export async function checkConnectionToServer() {
        new ServerHandler();
    }
    functions.checkConnectionToServer = checkConnectionToServer;


    export async function sendSearchRequest() {
        sendRequest(serverHandler.getTestSearchRequestData());
    }
    functions.sendSearchRequest = sendSearchRequest;

    export async function sendGetSearchProgressRequest() {
        sendRequest(serverHandler.getSearchProgressRequestData());
    }
    functions.sendGetSearchProgressRequest = sendGetSearchProgressRequest;

    export async function sendStopSearchRequest() {
        sendRequest(serverHandler.getStopSearchRequestData());
    }
    functions.sendStopSearchRequest = sendStopSearchRequest;

    async function sendRequest(sendingData) {
        console.log(">> sendRequest()");
        serverHandler.sendData(sendingData);
        console.log("<< sendRequest()");
    }
</script>