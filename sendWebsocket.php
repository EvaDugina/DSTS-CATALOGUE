<?php
require_once("utilities.php");
require_once("common.php");

// $au = new auth_ssh();
// checkAuLoggedIN($au);


show_head("СТРАНИЦА ИНФОРМАЦИИ О ТОВАРЕ");
?>

<body style="overflow-x: hidden;">
    <main class="">

        <div class="d-flex">
            <button class="btn btn-success" onclick="functions.sendSearchRequest()">START</button>
            <button class="btn btn-danger" onclick="functions.sendStopSearchRequest()">STOP</button>
        </div>

    </main>
</body>


<script type="text/javascript">
    const functions = {};
</script>
<script type="module">
    import ServerHandler from "./js/ServerHandler.js";

    var serverHandler = new ServerHandler();

    export async function sendSearchRequest() {
        sendRequest(serverHandler.getTestSearchRequestData());
    }

    export async function sendStopSearchRequest() {
        sendRequest(serverHandler.getStopSearchRequestData());
    }

    async function sendRequest(sendingData) {
        console.log(">> sendRequest()");
        serverHandler.sendData(sendingData);
        console.log("<< sendRequest()");
    }

    functions.sendSearchRequest = sendSearchRequest;
    functions.sendStopSearchRequest = sendStopSearchRequest;
</script>