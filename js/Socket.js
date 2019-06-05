$(document).ready(function () {

    var wsUri = "ws://127.0.0.1:6969";
    // var wsUri = "ws://172.18.1.113:6969";

    websocket = new WebSocket(wsUri);

    websocket.onopen = function(ev) {

    }

    websocket.onmessage = function(ev) {
        msgObject = JSON.parse(ev.data);
        console.log(msgObject);

        switch (msgObject['handler']) {
            case 'users_handler':
                break;

            case 'rooms_handler':
                break;

            case 'battleship_handler':
                break;

            default:
                break;
        }
    }

    websocket.onerror = function(ev) {
        console.error("WebSocket error observed:", ev);
    }

    websocket.onclose = function(ev) {
        
    }

});