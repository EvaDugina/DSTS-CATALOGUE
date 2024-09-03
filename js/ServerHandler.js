
import $url from './config.js';

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


////
//// ServerHandler
////


export default class ServerHandler {

    session_id = null;
    socket = null;
    serverData = null;

    output_object = null;
    output_object_key = null;

    constructor() {
        this.session_id = getCookie("PHPSESSID");
        this.socket = new WebSocket($url);
        this.defineInitOnMessage();
    }

    ////
    //// MAIN FUNCTIONS
    ////

    sendData(data, output_object = null, output_object_key = null) {
        if (output_object != null && output_object_key != null) {
            this.output_object = output_object;
            this.output_object_key = output_object_key;
        }

        let context = this;
        this.waitForConnection(function () {
            context.socket.send(JSON.stringify(Array.from(data.entries())));
            if (typeof callback !== 'undefined') {
                callback();
            }
        }, 1000);
    }


    ////
    //// JSON GETTERS
    ////

    getTestSearchRequestData() {
        return new Map([
            ['flag', "searchRequests"],
            [
                'requests', [
                    ["DONALDSON", "P550777"],
                    ["MANN", "P550777"]
                ]
            ]
        ]);
    }

    getStopSearchRequestData() {
        return new Map([
            ['flag', "stopSearch"]
        ]);
    }


    ////
    //// UTILITIES
    ////

    parseServerData(data) {
        return JSON.parse(data);
    }

    defineInitOnMessage() {

        this.socket.onmessage = (event) => {

            try {
                this.server_data = this.parseServerData(event.data);
                if (this.output_pointer != null && this.output_object_key != null)
                    this.output_object[this.output_object_key] = this.server_data;
                console.log(this.server_data);

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