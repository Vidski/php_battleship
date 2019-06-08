class RoomHandler {

    owner = false;

    handle(data) {
        switch (data['action']) {
            case 'create_room':
                this.handle_create_room(data);
                break;

            case 'join_room':
                this.handle_join_room(data);
                break;

            //DAS IS KAKE
            case 'send_message_room':
                this.handle_send_message_room(data);
                break;

            default:
                break;
        }
    }

    send_create_room() {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "create_room"
        }));
    }

    handle_create_room(data) {
        $('#createRoomPin').val(data['content']['pin']);
        if (data['content']['pin']) {
            $('#menu_box').fadeOut(function () {
                $('#battleship_game_box').fadeIn();
            });
            $('#chat_box').append("<p style='color: red'>Room PIN: " + data['content']['pin'] + "</p>")
        }
        generateTable();
        this.owner = true;
        $('#field_right .card').addClass('myturn');
    }

    send_join_room(pin) {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "join_room",
            "pin": pin
        }));
    }

    handle_join_room(data) {
        if (data['content']['error']) {
            return;
        }
        $('#menu_box').fadeOut();
        $('#battleship_game_box').fadeIn();
        $('#chat_box').append("<p>" + "[" + getCurrentTime() + "] " + data['content']['message'] + "</p>")
        if (!this.owner) {
            generateTable();
            $('#field_right .card').addClass('notmyturn');
        }
    }

    send_message_room(message) {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "send_message_room",
            "message": message
        }));
    }

    handle_send_message_room(data) {
        $('#chat_box').append("<p>" + "[" + getCurrentTime() + "] " + data['content']['message'] + "</p>")
    }

}