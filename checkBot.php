<?php
require_once("utilities.php");
require_once("common.php");

$au = new auth_ssh();
checkAuLoggedIN($au);


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
    const TIMEOUT = 5;
    const MAX_WAITING_TIME = 60;
    var FLAG_END = true;
    var WAITING_TIME = 0;
    var LAST_LOGS = "";
    var PAUSED = false;


    // 
    // 
    // 

    function mapToObj(map) {
        const obj = {}
        for (let [k, v] of map)
            obj[k] = v
        return obj
    }

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
        if (FLAG_END || PAUSED)
            return;

        await checkSearchFlagEndRequest();
        await updateLogProgressResult();
    }
    functions.checkSearchLogProgressResult = checkSearchLogProgressResult;
    setInterval(checkSearchLogProgressResult, TIMEOUT * 1000);

    export async function checkSearchFlagEndRequest() {

        await sendRequest(serverHandler.getGetSearchFlagEndRequestData(), function(server_data) {
            FLAG_END = server_data['flag_end'];
            if (FLAG_END)
                alert("Поиск окончен!");
        });
    }
    functions.checkSearchFlagEndRequest = checkSearchFlagEndRequest;

    export async function sendStopSearchRequest() {
        await sendRequest(serverHandler.getStopSearchRequestData());
        FLAG_END = true;
    }
    functions.sendStopSearchRequest = sendStopSearchRequest;

    export async function sendCleanLogsRequest() {
        await sendRequest(serverHandler.getCleanLogsRequestData());
    }
    functions.sendCleanLogsRequest = sendCleanLogsRequest;




    // 
    // 
    // 

    export async function updateLogProgressResult() {
        await sendRequest(serverHandler.getGetLogProgressResultRequestData(), function(server_data) {
            let textarea_log = document.getElementById('textarea-log');
            textarea_log.value = server_data['logs'].join("");
            textarea_log.scrollTop = textarea_log.scrollHeight;

            document.getElementById('textarea-progress').value = server_data['progress'].join("\n");

            let text = "";
            server_data['result'].forEach((element) => {
                for (let [key, value] of Object.entries(element)) {
                    text += value + "\t\t";
                }
            });
            document.getElementById('textarea-result').value = text;

            if (server_data['logs'] == LAST_LOGS) {
                WAITING_TIME += TIMEOUT;
            } else {
                LAST_LOGS = server_data['logs'];
                WAITING_TIME = 0;
            }

            if (WAITING_TIME >= MAX_WAITING_TIME) {
                let confirm = confirm("Продолжить ожидание сервера?")
                if (confirm) {
                    WAITING_TIME = 0;
                } else {
                    FLAG_END = true;
                    sendStopSearchRequest();
                }
            }
        });
    }
    functions.updateLogProgressResult = updateLogProgressResult;

    export async function updateLog() {
        await sendRequest(serverHandler.getGetLogRequestData(), function(server_data) {
            document.getElementById('textarea-log').value = server_data['logs'].join("\n")
        });
    }
    functions.updateLog = updateLog;

    export async function updateProgress() {
        await sendRequest(serverHandler.getGetProgressRequestData(), function(server_data) {
            document.getElementById('textarea-progress').value = server_data['progress'].join("\n");
        });
    }
    functions.updateProgress = updateProgress;

    export async function updateResult() {
        await sendRequest(serverHandler.getGetResultRequestData(), function(server_data) {
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
        WAITING_TIME = 0;
        LAST_LOGS = "";
        FLAG_END = false;
        await sendRequest(search_request);
    }
    functions.sendSearchRequest = sendSearchRequest;



    export async function sendDonaldsonP55077SearchRequest() {
        await sendSearchRequest(serverHandler.getDonaldsonP55077SearchRequestData())
    }
    functions.sendDonaldsonP55077SearchRequest = sendDonaldsonP55077SearchRequest;

    export async function sendFleetguardP55077SearchRequest() {
        await sendSearchRequest(serverHandler.getFleetguardP55077SearchRequestData())
    }
    functions.sendFleetguardP55077SearchRequest = sendFleetguardP55077SearchRequest;

    export async function sendMannP55077SearchRequest() {
        await sendSearchRequest(serverHandler.getMannP55077SearchRequestData())
    }
    functions.sendMannP55077SearchRequest = sendMannP55077SearchRequest;

    export async function sendHifiSh60161SearchRequest() {
        await sendSearchRequest(serverHandler.getHifiSh60161SearchRequestData())
    }
    functions.sendHifiSh60161SearchRequest = sendHifiSh60161SearchRequest;

    export async function sendFilfilterP55077SearchRequest() {
        await sendSearchRequest(serverHandler.getFilfilterP55077SearchRequestData())
    }
    functions.sendFilfilterP55077SearchRequest = sendFilfilterP55077SearchRequest;

    // 
    // 
    // 

    export async function sendFastSearchRequest() {
        await sendSearchRequest(serverHandler.getFastCheckSearchRequestData())
    }
    functions.sendFastSearchRequest = sendFastSearchRequest;

    export async function sendStandartSearchRequest() {
        await sendSearchRequest(serverHandler.getStandartCheckSearchRequestData())
    }
    functions.sendStandartSearchRequest = sendStandartSearchRequest;

    export async function sendDeepSearchRequest() {
        await sendSearchRequest(serverHandler.getDeepCheckTestSearchRequestData())
    }
    functions.sendDeepSearchRequest = sendDeepSearchRequest;

    // 
    // 
    // 

    async function sendRequest(sendingData, callback = null) {
        if (!serverHandler.isEnabled()) {
            checkConnectionToServer();
        }

        PAUSED = true;
        let result = await serverHandler.sendData(sendingData, function(server_data) {
            let isError = checkErrorCallback(server_data);
            if (!isError) {
                if (callback != null)
                    callback(server_data);
            } else {
                FLAG_END = true;
            }

            PAUSED = false;
        });
    }

    function checkErrorCallback(result) {
        if (result !== undefined && result !== null && result.constructor == Object && "error" in result) {
            alert("Ошибка! \n" + result['error']);
            return true;
        }
        return false;
    }
</script>