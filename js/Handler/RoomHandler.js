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
            case 'receive_message':
                this.handle_receive_message(data);
                break;
            case 'set_owner':
                this.handle_set_owner(data);
                break;
            default:
                break;
        }
    }

    send_create_room() {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "create_room",
            "game": "Battleship"
        }));
    }

    in_queue() {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "in_queue"
        }));
    }

    leave_queue() {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "leave_queue"
        }));
    }

    send_leave_room() {
        this.reset_room_for_new_game();
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "leave_room",
            "game": "Battleship"
        }));
    }

    reset_room_for_new_game() {
        this.owner = false;
        $('#chat_box').html("");
        $('#ships').show().children().children().children().show();
        $('#field_right').hide();
        $('#field_left .card').removeClass('notmyturn');
    }

    handle_create_room(data) {
        $('#createRoomPin').val(data['content']['pin']);
        window.document.title = window.document.title + " | Room - " + data['content']['pin'];
        if (data['content']['pin']) {
            $('#menu_box').fadeOut(1000, function() {
                $('#battleship_game_box').fadeIn(1000);
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
            $('.modal-footer').hide();
            $('.close').show();
            show_modal("⚠ Error", data['content']['message']);
            $('#btn_joinRoom').prop("disabled", false);
            return;
        }
        if (!data['content']['joined']) {
            show_modal("⚠ Error", data['content']['message']);
            return;
        }

        window.document.title = window.document.title + " | Room - " + data['content']['pin'];
        $('#menu_box').fadeOut(1000, function() {
            $('#battleship_game_box').fadeIn(1000);
        });
        // $('#chat_box').append("<p>" + "[" + getCurrentTime() + "] " + data['content']['message'] + "</p>")
        if (!this.owner) {
            generateTable();
            $('#field_right .card').addClass('notmyturn');
        }
    }

    send_message(message) {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "send_message",
            "message": message
        }));
    }

    handle_receive_message(data) {
        $('#chat_box').append("<p>" + "[" + getCurrentTime() + "] " + data['content']['message'] + "</p>");
        $('#chat_box').animate({
            scrollTop: $('#chat_box')[0].scrollHeight
        }, 500);
    }

    handle_set_owner(data) {
        this.owner = data['owner'];
    }

}