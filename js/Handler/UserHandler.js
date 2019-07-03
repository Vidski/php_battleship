class UserHandler {

    handle(data) {
        switch (data['action']) {
            case 'set_username':
                this.handle_set_username(data);
                break;
            default:
                break;
        }
    }

    send_set_username(username) {
        websocket.send(JSON.stringify({
            "handler": "users_handler",
            "action": "set_username",
            "username": username
        }));
    }

    handle_set_username(data) {
        $('#login_box').fadeOut(function () {
            $('#menu_box').fadeIn();
        });
    }

    

}