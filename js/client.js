$(document).ready(function () {

    var wsUri = "ws://127.0.0.1:6969";
    var username = "";
    websocket = new WebSocket(wsUri);

    websocket.onopen = function (ev) {
        loginBoxSuccess();
    }

    //Nachricht vom Server
    websocket.onmessage = function (ev) {
        msgObject = JSON.parse(ev.data);
        console.log(msgObject);

        switch (msgObject['handler']) {
            case 'game_handler':
                switch (msgObject['action']) {
                    case 'set_username':
                        username = msgObject['content'];
                        break;

                    default:
                        break;
                }
                break;

            case 'rooms_handler':
                switch (msgObject['action']) {
                    case 'requested_room':
                        break;

                    default:
                        break;
                }
                break;

            default:
                break;
        }

        console.log(ev.data);
    };

    //Fehler
    websocket.onerror = function (ev) {
        loginBoxError();
        console.error("WebSocket error observed:", ev);
    };

    //Verbindung zum Server wurde getrennt
    websocket.onclose = function (ev) {
        $('#message_box').append('<span class="system">Verbindung vom Server getrennt</span><br>');
    };


    //CLICK EVENTS

    $("#btn_inputUsername").click(function (event) {
        event.preventDefault();
        var name = $("#inputUsername").val();
        websocket.send(JSON.stringify({
            "handler": "game_handler",
            "action": "register",
            "username": name
        }));
        $('#btn_inputUsername').prop("disabled",true);
    });


    //FUNCTIONS

    function loginBoxSuccess() {
        $('#login_box_connecting .spinner-grow').removeClass('text-warning');
        $('#login_box_connecting .spinner-grow').addClass('text-success');
        $('#login_box_connecting').fadeOut(2000, function(){$('#login_box form').fadeIn();});
    }

    function loginBoxError() {
        $('#login_box_connecting p').text("Could not connect to Server");
        $('#login_box_connecting .spinner-grow').removeClass('text-warning');
        $('#login_box_connecting .spinner-grow').addClass('text-danger');
        console.error("WebSocket error observed:", ev);
    }

});