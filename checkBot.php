<?php
require_once("utilities.php");
require_once("common.php");

// $au = new auth_ssh();
// checkAuLoggedIN($au);


show_head("СТРАНИЦА ИНФОРМАЦИИ О ТОВАРЕ");
?>

<body style="overflow-x: hidden;">
    <main class="d-flex flex-column p-5">

        <div class="d-flex mb-3">
            <button class="btn btn-success me-3" onclick="functions.checkConnectionToServer()">CHECK CONNECTION</button>
            <button class="btn btn-warning me-3" onclick="functions.updateLogProgressResult()">GET LOG, PROGRESS, RESULT</button>
            <button class="btn btn-danger me-3" onclick="functions.sendStopSearchRequest()">STOP SEARCHING</button>
            <button class="btn btn-danger" onclick="functions.sendCleanLogsRequest()">CLEAN LOGS</button>
        </div>

        <div class="d-flex mb-3">
            <button class="btn btn-primary me-3" onclick="functions.sendDonaldsonP55077SearchRequest()">DONALDSON P55077</button>
            <button class="btn btn-primary me-3" onclick="functions.sendFleetguardP55077SearchRequest()">FLEETGUARD P55077</button>
            <button class="btn btn-primary me-3" onclick="functions.sendMannP55077SearchRequest()">MANN P55077</button>
            <button class="btn btn-primary me-3" onclick="functions.sendHifiSh60161SearchRequest()">HIFI SH60161</button>
            <button class="btn btn-primary me-3" onclick="functions.sendFilfilterP55077SearchRequest()">FILFILTER P55077</button>
        </div>

        <div class="d-flex mb-3">
            <button class="btn btn-secondary me-3" onclick="functions.sendFastSearchRequest()">Быстрая проверка</button>
            <button class="btn btn-secondary me-3" onclick="functions.sendStandartSearchRequest()">Стандартная проверка</button>
            <button class="btn btn-secondary me-3" onclick="functions.sendDeepSearchRequest()">Глубокая проверка</button>
        </div>

        <div class="d-flex mb-3" style="height: 1000px;">
            <textarea id="textarea-log" class="w-100 h-100" style="overflow-y: scroll;" readonly></textarea>
        </div>

        <div class="d-flex" style="height: 500px;">
            <textarea id="textarea-result" class="w-100 h-100 me-3" style="overflow-y: scroll;" readonly></textarea>
            <textarea id="textarea-progress" class="w-75 h-100" style="overflow-y: scroll;" readonly></textarea>
        </div>

    </main>
</body>


<script type="text/javascript">
    const functions = {};
</script>
<script type="module">
    import ServerHandler from "./js/ServerHandler.js";

    var serverHandler = new ServerHandler();
    var FLAG_END = true;

    // 
    // 
    // 

    export async function checkConnectionToServer() {
        serverHandler = new ServerHandler();
    }
    functions.checkConnectionToServer = checkConnectionToServer;

    // 
    // 
    // 

    export async function checkSearchLogProgressResult() {
        if (FLAG_END == true)
            return;

        await checkSearchFlagEndRequest();
        await updateLogProgressResult();
    }
    functions.checkSearchLogProgressResult = checkSearchLogProgressResult;
    setInterval(checkSearchLogProgressResult, 5000);

    export async function checkSearchFlagEndRequest() {
        await sendRequest(serverHandler.getGetSearchFlagEndRequestData(), true, function(server_data) {
            FLAG_END = server_data['flag_end'];
        });
        if (FLAG_END == true)
            alert("Поиск окончен!");
    }
    functions.checkSearchFlagEndRequest = checkSearchFlagEndRequest;

    export async function sendStopSearchRequest() {
        sendRequest(serverHandler.getStopSearchRequestData());
        FLAG_END = true;
    }
    functions.sendStopSearchRequest = sendStopSearchRequest;

    export async function sendCleanLogsRequest() {
        sendRequest(serverHandler.getCleanLogsRequestData());
    }
    functions.sendCleanLogsRequest = sendCleanLogsRequest;




    // 
    // 
    // 

    export async function updateLogProgressResult() {
        await sendRequest(serverHandler.getGetLogProgressResultRequestData(), true, function(server_data) {
            let textarea_log = document.getElementById('textarea-log')
            textarea_log.value = server_data['logs'].join("\n");
            textarea_log.scrollTop = textarea_log.scrollHeight;

            document.getElementById('textarea-progress').value = server_data['progress'].join("\n");

            let text = "";
            server_data['result'].forEach((element) => {
                for (let [key, value] of Object.entries(element)) {
                    text += value + "\t\t";
                }
                text += "\n"
            });
            document.getElementById('textarea-result').value = text;
        });
    }
    functions.updateLogProgressResult = updateLogProgressResult;

    export async function updateLog() {
        await sendRequest(serverHandler.getGetLogRequestData(), true, function(server_data) {
            document.getElementById('textarea-log').value = server_data['logs'].join("\n")
        });
    }
    functions.updateLog = updateLog;

    export async function updateProgress() {
        await sendRequest(serverHandler.getGetProgressRequestData(), true, function(server_data) {
            document.getElementById('textarea-progress').value = server_data['progress'].join("\n");
        });
    }
    functions.updateProgress = updateProgress;

    export async function updateResult() {
        await sendRequest(serverHandler.getGetResultRequestData(), true, function(server_data) {
            let text = "";
            server_data['result'].forEach((element) => {
                for (let [key, value] of Object.entries(element)) {
                    text += value + "\t\t";
                }
                text += "\n"
            });
            document.getElementById('textarea-result').value = text;
        });
    }
    functions.updateResult = updateResult;

    // 
    // 
    // 

    export async function sendSearchRequest(search_request) {
        sendRequest(search_request);
        FLAG_END = false;
    }
    functions.sendSearchRequest = sendSearchRequest;



    export async function sendDonaldsonP55077SearchRequest() {
        sendSearchRequest(serverHandler.getDonaldsonP55077SearchRequestData())
    }
    functions.sendDonaldsonP55077SearchRequest = sendDonaldsonP55077SearchRequest;

    export async function sendFleetguardP55077SearchRequest() {
        sendSearchRequest(serverHandler.getFleetguardP55077SearchRequestData())
    }
    functions.sendFleetguardP55077SearchRequest = sendFleetguardP55077SearchRequest;

    export async function sendMannP55077SearchRequest() {
        sendSearchRequest(serverHandler.getMannP55077SearchRequestData())
    }
    functions.sendMannP55077SearchRequest = sendMannP55077SearchRequest;

    export async function sendHifiSh60161SearchRequest() {
        sendSearchRequest(serverHandler.getHifiSh60161SearchRequestData())
    }
    functions.sendHifiSh60161SearchRequest = sendHifiSh60161SearchRequest;

    export async function sendFilfilterP55077SearchRequest() {
        sendSearchRequest(serverHandler.getFilfilterP55077SearchRequestData())
    }
    functions.sendFilfilterP55077SearchRequest = sendFilfilterP55077SearchRequest;

    // 
    // 
    // 

    export async function sendFastSearchRequest() {
        sendSearchRequest(serverHandler.getFastCheckSearchRequestData())
    }
    functions.sendFastSearchRequest = sendFastSearchRequest;

    export async function sendStandartSearchRequest() {
        sendSearchRequest(serverHandler.getStandartCheckSearchRequestData())
    }
    functions.sendStandartSearchRequest = sendStandartSearchRequest;

    export async function sendDeepSearchRequest() {
        sendSearchRequest(serverHandler.getDeepCheckTestSearchRequestData())
    }
    functions.sendDeepSearchRequest = sendDeepSearchRequest;

    // 
    // 
    // 

    async function sendRequest(sendingData, flag_wait_for_answer = false, callback = null) {
        if (!serverHandler.isEnabled()) {
            checkConnectionToServer();
        }

        let result = await serverHandler.sendData(sendingData, flag_wait_for_answer, callback);
        if (result !== undefined && result !== null && result.constructor == Object && "error" in result) {
            alert("Отсутствует соединение с сервером!");
            FLAG_END = true;
        }
    }
</script>