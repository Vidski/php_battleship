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

    //var wsUri = "ws://127.0.0.1:6966";
    var wsUri = "ws://172.18.1.113:6966";
    var username = "";
    var userid = null;
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
                userid = msgObject['content']['userid'];
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
                break;

            case 'join_room':
                if (msgObject['content']['error']) {
                    return;
                }
                generateTable();
                $('#menu_box').fadeOut();
                $('#battleship_game_box').fadeIn();
                $('#chat_box').append("<p>" + msgObject['content']['message'] + "</p>")
                break;

            case 'send_message_room':
                $('#chat_box').append("<p>" + msgObject['content']['message'] + "</p>")
                break;
                
            default:

                break;
        }
    }


    //HIER KOMMT DAS REIN WAS GEÄNDERT WERDEN SOLL
    function battleship_handler(data) {
        switch (msgObject['action']) {
            case 'shoot':
                console.log(data);
                console.log(msgObject['content']);
                console.log(userid);
                if(msgObject['content']['ergebnis']){
                    if(msgObject['content']['userid'] == userid){
                        $($('#field_right td[data-col="'+ msgObject['content']['positionX'] +'"][data-row="'+ msgObject['content']['positionY'] +'"]')).css('background-color', 'black');
                    } else{
                        $($('#field_left td[data-col="'+ msgObject['content']['positionX'] +'"][data-row="'+ msgObject['content']['positionY'] +'"]')).css('background-color', 'black');
                    }
                   
                }
                break;

            case 'place_ship':

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
            "x" : posX,
            "y" : posY
        };
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "game_action",
            "content": {
                position : position,
                'action' : 'shoot'
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

    $("table").on("click", "td", function() {
        bh_shoot($(this).attr('data-col'), $(this).attr('data-row'));
    });

    //FUNCTIONS

    function Drop(event, ui) {
        if (currentModus == "placement") {
            checkPlacement($(this).attr('data-col'), $(this).attr('data-row'), ui['draggable'][0]['id']);
        }
    }

    function checkPlacement(x, y, id) {
        console.log(x + " " + y + " " + id);
        console.log(ship2V);

        y = parseInt(y);
        switch (id) {
            case "ship2":
                var asd = ship2V['height'] + y;
                console.log(ship2V['height'] + " + " + y + " = " + asd);
                if (ship2V['height'] + y > 10) {
                    console.log("Cant place here");
                }
                break;

            default:
                break;
        }
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
        $('#ship2').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship3').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship4').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });
        $('#ship5').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: 24, left: 24 } });

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