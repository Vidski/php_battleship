$(document).ready(function() {

    user = new User();
    userHandler = new UserHandler();
    roomHandler = new RoomHandler();
    battleshipHandler = new BattleshipHandler();

    //var wsUri = "ws://127.0.0.1:6966";
    var wsUri = "ws://172.18.1.113:6966";
    //var wsUri = "ws://ux-113.web.pb.bib.de:6969"

    websocket = new WebSocket(wsUri);

    websocket.onopen = function(ev) {
        loginBoxSuccess();
    }

    websocket.onmessage = function(ev) {
        data = JSON.parse(ev.data);
        console.log(data);

        switch (data['handler']) {
            case 'users_handler':
                userHandler.handle(data);
                break;

            case 'rooms_handler':
                roomHandler.handle(data);
                break;

            case 'battleship_handler':
                battleshipHandler.handle(data);
                break;

            default:
                break;
        }
    }

    websocket.onerror = function(ev) {
        loginBoxError();
        console.error("WebSocket error observed:", ev);
    }

    websocket.onclose = function(ev) {

    }

});