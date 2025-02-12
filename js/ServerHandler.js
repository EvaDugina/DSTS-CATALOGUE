
import { getUrl } from './config.js';

export function startDaemon() {
    ajaxStartStopDaemon(true);
}

export function stopDaemon() {
    ajaxStartStopDaemon(false);

}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function ajaxStartStopDaemon(flag) {
    var formData = new FormData();

    if (flag)
        formData.append('flag', "StartDaemon");
    else
        formData.append('flag', "StopDaemon");

    $.ajax({
        type: "POST",
        url: 'edit_action.php#content',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'html',
        success: function (response) {

        },
        complete: function () { }
    });
}

function sleep(s) {
    return new Promise(resolve => setTimeout(resolve, s * 1000));
}


////
//// ServerHandler
////


export default class ServerHandler {

    session_id = null;
    url = null;
    socket = null;
    serverData = null;

    constructor() {
        this.session_id = getCookie("PHPSESSID");
        this.url = getUrl();
        this.connect();
    }

    ////
    //// MAIN FUNCTIONS
    ////

    async connect() {
        console.log("Connecting to server...")
        this.socket = new WebSocket(this.url);
        this.initDefaultOnFunctions();
    }

    async reconnectIfNotConnected() {
        if (!this.isEnabled)
            await this.connect();
    }

    isEnabled() {
        if (this.socket.readyState == WebSocket.OPEN)
            return true;
        return false;
    }

    getServerData() {
        return this.serverData;
    }

    async waitForServerData() {
        let timeout = 10;
        let step = 0.5;
        let count_time = 0;
        while (this.serverData == null && count_time < timeout) {
            // console.log("sleepping...")
            await sleep(step);
            count_time += step;
        }
        if (this.serverData == null)
            return { "error": "Данные с сервера не получены!" }
        return this.serverData;
    }

    async sendData(data, flag_wait_for_answer = false, callback = null) {

        // console.log("sendData()");

        if (!this.isEnabled()) {
            return { "error": "Отсутствует соединение с сервером!" };
        }

        var context = this;
        this.waitForConnection(function () {
            context.serverData = null;
            context.socket.send(JSON.stringify(Array.from(data.entries())));
            // if (typeof callback !== 'undefined') {
            //     callback();
            // }
        }, 1000);

        if (flag_wait_for_answer && callback != null) {
            // this.setCollbackOnMessage(callback);
            let server_data = await this.waitForServerData();
            if ("error" in server_data)
                return server_data;
            callback(server_data);
        }

        return "Запрос отправлен!"
    }

    async sendSearchRequest(search_request) {
        let searchRequestData = this.getSearchRequestData(search_request);
        return await this.sendData(searchRequestData);
    }

    async sendGetSearchProgressRequest() {
        let searchRequestData = this.getSearchProgressRequestData();
        return await this.sendData(searchRequestData, true);
    }

    async sendStopSearchRequest() {
        let searchRequestData = this.getStopSearchRequestData();
        return await this.sendData(searchRequestData);
    }


    ////
    //// JSON GETTERS
    ////

    getDonaldsonP55077SearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["DONALDSON", "P55077"]
                ]
            ]
        ]);
    }

    getFleetguardP55077SearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["FLEETGUARD", "P55077"]
                ]
            ]
        ]);
    }

    getMannP55077SearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["MANN", "P55077"]
                ]
            ]
        ]);
    }

    getHifiSh60161SearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["HIFI", "SH60161"]
                ]
            ]
        ]);
    }

    getFilfilterP55077SearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["FILFILTER", "P55077"]
                ]
            ]
        ]);
    }

    // 
    // 
    // 

    getFastCheckSearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["DONALDSON", "P550777"],
                    ["FLEETGUARD", "P550777"],
                    ["MANN", "P550777"],
                    ["HIFI", "SH60161"],
                    ["FILFILTER", "P550777"],
                ]
            ]
        ]);
    }

    getStandartCheckSearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["DONALDSON", "P55077"],
                    ["FLEETGUARD", "P55077"],
                    ["MANN", "P55077"],
                    ["HIFI", "SH60161"],
                    ["FILFILTER", "P55077"],
                ]
            ]
        ]);
    }



    getDeepCheckTestSearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["FLEETGUARD", "FS19572"],
                    ["FLEETGUARD", "P111"],
                    ["FLEETGUARD", "P5507"],
                    ["FLEETGUARD", "P55077765568"],
                    ["MANN", "C10050"],
                    ["MANN", "FP24"],
                    ["MANN", "W14"],
                    ["MANN", "FP24"],
                    ["MANN", "P55077"],
                    ["MANN", "H1"],
                    ["HIFI", "SH60161"],
                    ["HIFI", "SH6016"],
                    ["HIFI", "P550777"],
                    ["HIFI", "P5517"],
                    ["FILFILTER", "ZP32890172531526"],
                    ["FILFILTER", "ZP69"],
                    ["FILFILTER", "P550777"],
                    ["FILFILTER", "ZP507"],
                    ["DONALDSON", "P5507713124"],
                    ["DONALDSON", "P550777"],
                    ["DONALDSON", "P550771"],
                    ["DONALDSON", "ZP11"],
                    ["DONALDSON", "P55077"]
                ]
            ]
        ]);
    }

    // 
    // 
    // 

    getGetLogRequestData() {
        return new Map([
            ['flag', "GetSearchLog"]
        ]);
    }

    getGetProgressRequestData() {
        return new Map([
            ['flag', "GetSearchProgress"]
        ]);
    }

    getGetResultRequestData() {
        return new Map([
            ['flag', "GetSearchResult"]
        ]);
    }

    getGetLogProgressResultRequestData() {
        return new Map([
            ['flag', "GetSearchLogProgressResult"]
        ]);
    }

    getGetSearchFlagEndRequestData() {
        return new Map([
            ['flag', "GetSearchFlagEnd"]
        ]);
    }

    getStopSearchRequestData() {
        return new Map([
            ['flag', "StopSearch"]
        ]);
    }


    ////
    //// UTILITIES
    ////

    parseServerData(data) {
        return JSON.parse(data);
    }

    initDefaultOnFunctions() {
        var context = this;

        context.socket.onopen = function (event) {
            console.log("The connection was opened!");
        };

        this.initDefaultOnMessage();

        context.socket.onerror = function (event) {
            console.log("Websocket error!");
        }

        context.socket.onclose = function (event) {
            var reason;
            if (event.code == 1000)
                reason = "Normal closure, meaning that the purpose for which the connection was established has been fulfilled.";
            else if (event.code == 1001)
                reason = "An endpoint is \"going away\", such as a server going down or a browser having navigated away from a page.";
            else if (event.code == 1002)
                reason = "An endpoint is terminating the connection due to a protocol error";
            else if (event.code == 1003)
                reason = "An endpoint is terminating the connection because it has received a type of data it cannot accept (e.g., an endpoint that understands only text data MAY send this if it receives a binary message).";
            else if (event.code == 1004)
                reason = "Reserved. The specific meaning might be defined in the future.";
            else if (event.code == 1005)
                reason = "No status code was actually present.";
            else if (event.code == 1006)
                reason = "The connection was closed abnormally, e.g., without sending or receiving a Close control frame";
            else if (event.code == 1007)
                reason = "An endpoint is terminating the connection because it has received data within a message that was not consistent with the type of the message (e.g., non-UTF-8 [https://www.rfc-editor.org/rfc/rfc3629] data within a text message).";
            else if (event.code == 1008)
                reason = "An endpoint is terminating the connection because it has received a message that \"violates its policy\". This reason is given either if there is no other sutible reason, or if there is a need to hide specific details about the policy.";
            else if (event.code == 1009)
                reason = "An endpoint is terminating the connection because it has received a message that is too big for it to process.";
            else if (event.code == 1010) // Note that this status code is not used by the server, because it can fail the WebSocket handshake instead.
                reason = "An endpoint (client) is terminating the connection because it has expected the server to negotiate one or more extension, but the server didn't return them in the response message of the WebSocket handshake. <br /> Specifically, the extensions that are needed are: " + event.reason;
            else if (event.code == 1011)
                reason = "A server is terminating the connection because it encountered an unexpected condition that prevented it from fulfilling the request.";
            else if (event.code == 1015)
                reason = "The connection was closed due to a failure to perform a TLS handshake (e.g., the server certificate can't be verified).";
            else
                reason = "Unknown reason";

            console.log(event.code + " The connection was closed for reason: " + reason);
            alert(event.code + " The connection was closed for reason: " + reason);
        };
    }

    initDefaultOnMessage() {
        var context = this;

        context.socket.onmessage = (event) => {
            try {
                context.serverData = context.parseServerData(event.data);
                console.log("serverData:", context.serverData)
            }
            catch (error) {
                console.log(error);
            }
        };
    }

    setCollbackOnMessage(callback) {
        var context = this;

        context.socket.onmessage = (event) => {
            try {
                context.serverData = context.parseServerData(event.data);
                console.log("serverData:", context.serverData);
                callback(context.serverData);
                this.initDefaultOnMessage();
            }
            catch (error) {
                console.log(error);
            }
        };
    }

    waitForConnection(callback, interval) {
        if (this.socket.readyState === 1) {
            callback();
        } else {
            var that = this;
            // optional: implement backoff for interval here
            setTimeout(function () {
                that.waitForConnection(callback, interval);
            }, interval);
        }
    }

}