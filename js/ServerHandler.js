
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
        this.socket = new WebSocket(this.url);
        this.defineInitOnMessage();
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
        let timeout = 5;
        let count_time = 0;
        while (this.serverData == null && count_time < timeout) {
            // console.log("sleepping...")
            await sleep(0.5);
            count_time += 0.5;
        }
        if (this.serverData == null)
            return { "error": "Данные с сервера не получены!" }
        return this.serverData;
    }

    async sendData(data, flag_wait_for_answer = false) {

        // console.log("sendData()");

        let context = this;
        this.waitForConnection(function () {
            context.serverData = null;
            context.socket.send(JSON.stringify(Array.from(data.entries())));
            if (typeof callback !== 'undefined') {
                callback();
            }
        }, 1000);

        if (flag_wait_for_answer) {
            return await this.waitForServerData();
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

    getTestSearchRequestData() {
        return new Map([
            ['flag', "SearchRequests"],
            [
                'requests', [
                    ["DONALDSON", "P550777"],
                    ["MANN", "P550777"]
                ]
            ]
        ]);
    }

    getSearchProgressRequestData() {
        return new Map([
            ['flag', "GetSearchProgress"]
        ]);
    }

    getSearchRequestData(search_requests) {
        return new Map([
            ['flag', "SearchRequests"],
            ['requests', search_requests]
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

    defineInitOnMessage() {
        var context = this;

        context.socket.onmessage = (event) => {
            try {
                context.serverData = context.parseServerData(event.data);
            }
            catch (error) {
                console.log(error);
            }
        };

        context.socket.onerror = function (event) {
            console.log("onerror(): " + event);
        }

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