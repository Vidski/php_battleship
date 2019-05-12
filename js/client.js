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
            case 'users_handler':
                users_handler(msgObject);
                break;

            case 'rooms_handler':
                rooms_handler(msgObject);
                break;

            default:
                break;
        }
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


    //Handlers

    function users_handler(data) {
        
    }

    function rooms_handler(data) {

    }

    //Handler functions

    function uh_set_username(username) {
        websocket.send(JSON.stringify({
            "handler": "users_handler",
            "action": "set_username",
            "username": username
        }));
    }

    function rh_create_room() {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "create_room"
        }));
    }

    function rh_join_room(pin) {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "join_room",
            "pin": pin
        }));
    }

    function rh_my_room() {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "my_room"
        }));
    }

    //Click events

    $("#btn_inputUsername").click(function (event) {
        event.preventDefault();
        var name = $("#inputUsername").val();
        uh_set_username(name);
        $('#btn_inputUsername').prop("disabled", true);
    });

    $("#btn_createRoom").click(function (event) {
        event.preventDefault();
        rh_create_room();
        $('#btn_inputUsername').prop("disabled", true);
    });

    $("#btn_joinRoom").click(function (event) {
        event.preventDefault();
        var pin = $('#joinRoomPin').val()
        rh_join_room(pin);
        $('#btn_inputUsername').prop("disabled", true);
    });

    //FUNCTIONS

    function loginBoxSuccess() {
        $('#login_box_connecting .spinner-grow').removeClass('text-warning');
        $('#login_box_connecting .spinner-grow').addClass('text-success');
        $('#login_box_connecting').fadeOut(2000, function () { $('#login_box form').fadeIn(); });
    }

    function loginBoxError() {
        $('#login_box_connecting p').text("Could not connect to Server");
        $('#login_box_connecting .spinner-grow').removeClass('text-warning');
        $('#login_box_connecting .spinner-grow').addClass('text-danger');
        console.error("WebSocket error observed:", ev);
    }

});