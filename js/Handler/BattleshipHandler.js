class BattleshipHandler {

    ships = {
        ship2: 0,
        ship2_limit: 4,
        ship3: 0,
        ship3_limit: 3,
        ship4: 0,
        ship4_limit: 2,
        ship5: 0,
        ship5_limit: 1
    }

    ship2V = { height: 2, width: 1 };
    ship3V = { height: 3, width: 1 };
    ship4V = { height: 4, width: 1 };
    ship5V = { height: 5, width: 1 };

    ship2H = { height: 1, width: 2 };
    ship3H = { height: 1, width: 3 };
    ship4H = { height: 1, width: 4 };
    ship5H = { height: 1, width: 5 };

    handle(data) {
        switch (data['action']) {

            case 'shoot':
                this.handle_shoot(data);
                break;

            case 'place':
                this.handle_place(data);
                break;

            case 'start':
                this.handle_start(data);

            case 'playerready':
                this.handle_ready(data);

            default:
                break;
        }
    }

    send_shoot(x, y) {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "game_action",
            "content": {
                "position": {
                    "x": x,
                    "y": y
                },
                "action": "shoot"
            }
        }));
    }

    handle_shoot(data) {
        if (data['content']['myturn'] == true) {
            $('#field_right .card').removeClass('notmyturn');
            $('#field_right .card').addClass('myturn');
            $('#field_left .card').addClass('notmyturn');
        } else {
            $('#field_right .card').removeClass('myturn');
            $('#field_right .card').addClass('notmyturn');
            $('#field_left .card').removeClass('notmyturn');
        }
        if (data['content']['hit'] == 1) {

            $('#field_' + data['content']['field'] + ' td[data-col="' + data['content']['x'] + '"][data-row="' + data['content']['y'] + '"]').addClass('hit');
            $('#field_' + data['content']['field'] + ' td[data-col="' + data['content']['x'] + '"][data-row="' + data['content']['y'] + '"]').removeClass('ui-droppable');

            var x = parseInt(data['content']['x']);
            var y = parseInt(data['content']['y'])

            $('#field_' + data['content']['field'] + ' td[data-col="' + (x - 1) + '"][data-row="' + (y - 1) + '"]').addClass('missed');
            $('#field_' + data['content']['field'] + ' td[data-col="' + (x - 1) + '"][data-row="' + (y + 1) + '"]').addClass('missed');
            $('#field_' + data['content']['field'] + ' td[data-col="' + (x + 1) + '"][data-row="' + (y - 1) + '"]').addClass('missed');
            $('#field_' + data['content']['field'] + ' td[data-col="' + (x + 1) + '"][data-row="' + (y + 1) + '"]').addClass('missed');

        } else {
            $('#field_' + data['content']['field'] + ' td[data-col="' + data['content']['x'] + '"][data-row="' + data['content']['y'] + '"]').addClass('missed');
            $('#field_' + data['content']['field'] + ' td[data-col="' + data['content']['x'] + '"][data-row="' + data['content']['y'] + '"]').removeClass('ui-droppable');
        }
    }

    send_place(x, y, id) {
        var ship = null;
        x = parseInt(x);
        y = parseInt(y);
        switch (id) {
            case "ship2V":
                ship = this.ship2V;
                break;

            case "ship3V":
                ship = this.ship3V;
                break;

            case "ship4V":
                ship = this.ship4V;
                break;

            case "ship5V":
                ship = this.ship5V;
                break;

            case "ship2H":
                ship = this.ship2H;
                break;
            case "ship3H":
                ship = this.ship3H;
                break;

            case "ship4H":
                ship = this.ship4H;
                break;

            case "ship5H":
                ship = this.ship5H;
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

        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "game_action",
            "content": {
                "position": {
                    "x": x,
                    "y": y
                },
                "ship": id,
                "action": "place"
            }
        }));
    }

    handle_place(data) {
        if (data['content']['placed']) {
            for (let index = 0; index < data['content']['blocked'].length; index++) {
                $('#field_left td[data-col="' + data['content']['blocked'][index][0] + '"][data-row="' + data['content']['blocked'][index][1] + '"]').addClass('blocked');
            }
            for (let index = 0; index < data['content']['placed'].length; index++) {
                $('#field_left td[data-col="' + data['content']['placed'][index][0] + '"][data-row="' + data['content']['placed'][index][1] + '"]').css('background-color', 'red');
            }
        }
    }

    handle_start(data) {
        if (data['content']['ready']) {
            var table = document.getElementById("left");
            for (var i = 0, row; row = table.rows[i]; i++) {
                for (var j = 0, col; col = row.cells[j]; j++) {
                    if ($(col).hasClass('blocked'))
                        $(col).removeClass('blocked')
                }
            }
            $('#ships').hide();
            $('#field_right').show();
        }
    }

    handle_ready(data) {

    }

}