$(document).ready(function() {

    var ships = {
        ship2: 0,
        ship2_limit: 4,
        ship3: 0,
        ship3_limit: 3,
        ship4: 0,
        ship4_limit: 2,
        ship5: 0,
        ship5_limit: 1
    }

    var ship2V = { height: 2, width: 1 };
    var ship3V = { height: 3, width: 1 };
    var ship4V = { height: 4, width: 1 };
    var ship5V = { height: 5, width: 1 };

    var ship2H = { height: 1, width: 2 };
    var ship3H = { height: 1, width: 3 };
    var ship4H = { height: 1, width: 4 };
    var ship5H = { height: 1, width: 5 };

    var currentModus = "placement";
    var owner = false;

    //var wsUri = "ws://127.0.0.1:6969";
    var wsUri = "ws://172.18.1.113:6966";
    var username = "";
    websocket = new WebSocket(wsUri);

    websocket.onopen = function(ev) {
        loginBoxSuccess();
    }

    //Nachricht vom Server
    websocket.onmessage = function(ev) {
        msgObject = JSON.parse(ev.data);
        console.log(msgObject);

        switch (msgObject['handler']) {
            case 'users_handler':
                users_handler(msgObject);
                break;

            case 'rooms_handler':
                rooms_handler(msgObject);
                break;

            case 'battleship_handler':
                battleship_handler(msgObject);
                break;
            default:
                break;
        }
    };

    //Fehler
    websocket.onerror = function(ev) {
        loginBoxError();
        console.error("WebSocket error observed:", ev);
    };

    //Verbindung zum Server wurde getrennt
    websocket.onclose = function(ev) {
        $('#message_box').append('<span class="system">Verbindung vom Server getrennt</span><br>');
    };

    //Handlers
    function users_handler(data) {
        switch (msgObject['action']) {
            case 'set_username':
                username = msgObject['content']['username'];
                $('#login_box').fadeOut(function() {
                    $('#menu_box').fadeIn();
                });
                break;

            default:
                break;
        }
    }

    function rooms_handler(data) {
        switch (msgObject['action']) {
            case 'create_room':
                $('#createRoomPin').val(msgObject['content']['pin']);
                if (msgObject['content']['pin']) {
                    $('#menu_box').fadeOut(function() {
                        $('#battleship_game_box').fadeIn();
                    });
                    $('#chat_box').append("<p style='color: red'>Room PIN: " + msgObject['content']['pin'] + "</p>")
                }
                generateTable();
                owner = true;
                $('#field_right .card').css('border', '2px solid blue');
                break;

            case 'join_room':
                if (msgObject['content']['error']) {
                    return;
                }
                generateTable();
                $('#menu_box').fadeOut();
                $('#battleship_game_box').fadeIn();
                $('#chat_box').append("<p>" + "[" + getCurrentTime() + "] " + msgObject['content']['message'] + "</p>")
                if (!owner)
                    $('#field_left .card').css('border', '2px solid blue');
                break;

            case 'send_message_room':
                $('#chat_box').append("<p>" + "[" + getCurrentTime() + "] " + msgObject['content']['message'] + "</p>")
                break;

            default:
                break;
        }
    }


    //HIER KOMMT DAS REIN WAS GEÄNDERT WERDEN SOLL
    function battleship_handler(data) {
        switch (msgObject['action']) {
            case 'shoot':
                console.log(msgObject['action']);
                if (msgObject['content']['myturn'] == true) {
                    $('#field_left .card').css('border', '1px solid rgba(0,0,0,.125)');
                    $('#field_right .card').css('border', '2px solid blue');
                } else {
                    $('#field_right .card').css('border', '1px solid rgba(0,0,0,.125)');
                    $('#field_left .card').css('border', '2px solid blue');
                }
                if (msgObject['content']['hit'] == 1) {
                    $('#field_' + msgObject['content']['field'] + ' td[data-col="' + msgObject['content']['x'] + '"][data-row="' + msgObject['content']['y'] + '"]').css('background-color', 'black');
                } else {
                    $('#field_' + msgObject['content']['field'] + ' td[data-col="' + msgObject['content']['x'] + '"][data-row="' + msgObject['content']['y'] + '"]').css('background-color', 'yellow');
                }
                break;

            case 'place':
                console.log(msgObject['action']);
                if (msgObject['content']['placed']) {
                    for (let index = 0; index < msgObject['content']['blocked'].length; index++) {
                        $('#field_left td[data-col="' + msgObject['content']['blocked'][index][0] + '"][data-row="' + msgObject['content']['blocked'][index][1] + '"]').css('background-color', 'grey');
                    }
                    for (let index = 0; index < msgObject['content']['placed'].length; index++) {
                        $('#field_left td[data-col="' + msgObject['content']['placed'][index][0] + '"][data-row="' + msgObject['content']['placed'][index][1] + '"]').css('background-color', 'red');
                    }
                }
                break;

            default:
                break;
        }
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

    function rh_send_message() {
        var message = $('#input_sendMessage').val();
        console.log(message);
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "send_message_room",
            "message": message
        }));
    }

    function bh_shoot(posX, posY) {
        var position = {
            "x": posX,
            "y": posY
        };
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "game_action",
            "content": {
                position: position,
                'action': 'shoot'
            }
        }));
    }

    //Click events

    $("#btn_inputUsername").click(function(event) {
        event.preventDefault();
        var name = $("#inputUsername").val();
        uh_set_username(name);
        $('#btn_inputUsername').prop("disabled", true);
    });

    $("#btn_createRoom").click(function(event) {
        event.preventDefault();
        rh_create_room();
        $('#btn_inputUsername').prop("disabled", true);
    });

    $("#btn_joinRoom").click(function(event) {
        event.preventDefault();
        var pin = $('#joinRoomPin').val()
        rh_join_room(pin);
    });

    $("#btn_sendMessage").click(function(event) {
        event.preventDefault();
        var pin = $('#joinRoomPin').val()
        rh_send_message();
    });

    $("#field_right table").on("click", "td", function() {
        bh_shoot($(this).attr('data-col'), $(this).attr('data-row'));
    });

    //FUNCTIONS

    function getCurrentTime() {
        var d = new Date();
        return ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);
    }

    function Drop(event, ui) {
        if (currentModus == "placement") {
            checkPlacement($(this).attr('data-col'), $(this).attr('data-row'), ui['draggable'][0]['id']);
        }
    }

    function checkPlacement(x, y, id) {
        console.log("X:" + x + " Y:" + y + " ID:" + id);
        var ship = null;
        y = parseInt(y);
        x = parseInt(x);
        switch (id) {
            case "ship2V":
                ship = ship2V;
                break;

            case "ship3V":
                ship = ship3V;
                break;

            case "ship4V":
                ship = ship4V;
                break;

            case "ship5V":
                ship = ship5V;
                break;

            case "ship2H":
                ship = ship2H;
                break;

            case "ship3H":
                ship = ship3H;
                break;

            case "ship4H":
                ship = ship4H;
                break;

            case "ship5H":
                ship = ship5H;
                break;

            default:
                break;
        }

        if (ship['height'] + y > 10 || ship['height'] + y < 0) {
            console.log("[HEIGHT] Can't place here!");
            return;
        }

        if (ship['width'] + x > 10) {
            console.log("[WIDTH] Can't place here!");
            return;
        }

        var position = {
            "x": x,
            "y": y
        };
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "game_action",
            "content": {
                'position': position,
                'ship': id,
                'action': 'place'
            }
        }));
    }

    function generateTable() {
        var table = "";
        var fieldSize = 10;
        for (var i = 0; i < fieldSize; i++) {
            if (i == 0) {
                table += '<thead><tr><th scope="col"></th> <th scope="col">A</th> <th scope="col">B</th> <th scope="col">C</th> <th scope="col">D</th> <th scope="col">E</th> <th scope="col">F</th> <th scope="col">G</th> <th scope="col">H</th> <th scope="col">I</th> <th scope="col">J</th> </tr></thead><tbody>';
            }
            table += '<tr>';
            for (var j = 0; j < fieldSize; j++) {
                if (j == 0) {
                    table += '<th scope="row">' + (i + 1) + '</th>';
                }
                table += '<td data-row=' + i + ' data-col=' + j + '></td>';

            }
            table += "</tr>";
        }
        table += '</tbody>';
        $('table').html(table);
        $('#ship2V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship3V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship4V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship5V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship2H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship3H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship4H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship5H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });

        $('td').droppable({
            tolerance: "pointer",
            drop: Drop
        });
    }

    function loginBoxSuccess() {
        $('#login_box_connecting .spinner-grow').removeClass('text-warning');
        $('#login_box_connecting .spinner-grow').addClass('text-success');
        $('#login_box_connecting').fadeOut(2000, function() { $('#login_box_form').fadeIn(); });
    }

    function loginBoxError() {
        $('#login_box_connecting p').text("Could not connect to Server");
        $('#login_box_connecting .spinner-grow').removeClass('text-warning');
        $('#login_box_connecting .spinner-grow').addClass('text-danger');
        console.error("WebSocket error observed:", ev);
    }
});