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

            case 'remove':
                this.handle_remove(data);
                break;

            case 'reconnect':
                this.handle_reconnect(data);
                break;

            case 'start':
                this.handle_start(data);
                break;

            case 'limit':
                this.handle_limit(data);
                break;

            case 'ready':
                this.handle_ready(data);
                break;

            case 'winner':
                this.handle_winner(data);
                break;

            default:
                break;
        }
    }

    /**
     * send_shoot(x,y)
     * 
     * Sendet eine Json an den Server mit der Position des Schusses.
     * 
     * @param {int} x position
     * @param {int} y position
     */
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

    /**
     * handle_shoot
     * 
     * Diese Funktion verwaltet, was nach dem Schuss passiert. 
     * Dazu werden CSS Klassen hinzugefügt, um das Ergebnis grafisch anzuzeigen.
     * 
     * @param {Array} data 
     */
    handle_shoot(data) {

        /**
         * Hier wird das Feld ausgegraut, falls man gerade nicht am Zug ist.
         */
        if (data['content']['myturn'] == true) {
            $('#field_right .card').removeClass('notmyturn');
            $('#field_right .card').addClass('myturn');
            $('#field_left .card').addClass('notmyturn');
        } else {
            $('#field_right .card').removeClass('myturn');
            $('#field_right .card').addClass('notmyturn');
            $('#field_left .card').removeClass('notmyturn');
        }

        //FALLS GETROFFEN WURDE
        if (data['content']['hit'] == 1) {

            var x = parseInt(data['content']['x']);
            var y = parseInt(data['content']['y']);

            $('#field_' + data['content']['field'] + ' td[data-col="' + x + '"][data-row="' + y + '"]').addClass('hit');
            $('#field_' + data['content']['field'] + ' td[data-col="' + x + '"][data-row="' + y + '"]').removeClass('ui-droppable');

            if (data['content']['field'] != 'left') {
                $('#field_' + data['content']['field'] + ' td[data-col="' + (x - 1) + '"][data-row="' + (y - 1) + '"]').addClass('blocked');
                $('#field_' + data['content']['field'] + ' td[data-col="' + (x - 1) + '"][data-row="' + (y + 1) + '"]').addClass('blocked');
                $('#field_' + data['content']['field'] + ' td[data-col="' + (x + 1) + '"][data-row="' + (y - 1) + '"]').addClass('blocked');
                $('#field_' + data['content']['field'] + ' td[data-col="' + (x + 1) + '"][data-row="' + (y + 1) + '"]').addClass('blocked');
            }

            if (data['content']['ship']) {
                if (data['content']['field'] != 'left') {
                    for (let index = 0; index < data['content']['ship']['position'].length; index++) {
                        var element = data['content']['ship']['position'][index];
                        x = parseInt(element['x']);
                        y = parseInt(element['y']);
                        $('#field_' + data['content']['field'] + ' td[data-col="' + x + '"][data-row="' + (y - 1) + '"]').addClass('blocked');
                        $('#field_' + data['content']['field'] + ' td[data-col="' + (x - 1) + '"][data-row="' + y + '"]').addClass('blocked');
                        $('#field_' + data['content']['field'] + ' td[data-col="' + x + '"][data-row="' + (y + 1) + '"]').addClass('blocked');
                        $('#field_' + data['content']['field'] + ' td[data-col="' + (x + 1) + '"][data-row="' + y + '"]').addClass('blocked');
                    }
                }
                data['content']['ship']['position'].forEach(element => {
                    $('#field_' + data['content']['field'] + ' td[data-col="' + (element['x']) + '"][data-row="' + (element['y']) + '"]').removeClass('blocked');
                    $('#field_' + data['content']['field'] + ' td[data-col="' + (element['x']) + '"][data-row="' + (element['y']) + '"]').addClass('dead');
                });

            }

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
                $('#field_left td[data-col="' + data['content']['placed'][index][0] + '"][data-row="' + data['content']['placed'][index][1] + '"]').removeClass('blocked');
                $('#field_left td[data-col="' + data['content']['placed'][index][0] + '"][data-row="' + data['content']['placed'][index][1] + '"]').addClass('shipplaced');
            }
        }
    }

    send_remove(x, y) {
        websocket.send(JSON.stringify({
            "handler": "rooms_handler",
            "action": "game_action",
            "content": {
                "position": {
                    "x": x,
                    "y": y
                },
                "action": "remove"
            }
        }));
    }

    handle_remove(data) {
        if ($('#' + data['content']['id']).is(":hidden")) {
            var substring = data['content']['id'].substring(0, 5);
            $('#' + substring + "V").show();
            $('#' + substring + "H").show();
        }

        for (let index = 0; index < data['content']['position'].length; index++) {
            $('#field_left td[data-col="' + data['content']['position'][index][0] + '"][data-row="' + data['content']['position'][index][1] + '"]').removeClass('blocked');
            $('#field_left td[data-col="' + data['content']['position'][index][0] + '"][data-row="' + data['content']['position'][index][1] + '"]').removeClass('shipplaced');
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

    handle_limit(data) {
        var ship = data['content']['ship'];
        $('#' + ship + 'H, #' + ship + 'V').hide();
    }

    handle_ready(data) {
        if (data['content']['ready']) {
            var table = document.getElementById("left");
            for (var i = 0, row; row = table.rows[i]; i++) {
                for (var j = 0, col; col = row.cells[j]; j++) {
                    if ($(col).hasClass('blocked'))
                        $(col).removeClass('blocked');
                }
            }
            $('#ships').hide();
        }
    }

    handle_winner(data) {
        // $('#exampleModal .modal-title').text(data['content']['title']);
        // $('#exampleModal .modal-body').text(data['content']['body']);
        // $('#exampleModal').modal('show');
        show_modal(data['content']['title'], data['content']['body']);
        $('#field_left .card').removeClass('myturn');
        $('#field_left .card').addClass('notmyturn');
        $('#field_right .card').removeClass('myturn');
        $('#field_right .card').addClass('notmyturn');
    }

    handle_reconnect(data) {
        if (data['content']['game_started']) {
            $('#ships').hide();
            $('#field_right').show();

            if (data['content']['my_turn']) {
                $('#field_right .card').removeClass('notmyturn');
                $('#field_right .card').addClass('myturn');
                $('#field_left .card').addClass('notmyturn');
            }

            var table = document.getElementById("right");
            for (var i = 1, row; row = table.rows[i]; i++) {
                for (var j = 1, col; col = row.cells[j]; j++) {
                    currfield = data['content']['enemy_field'][(j - 1) + "" + (i - 1)];
                    if (currfield == 2) {
                        $(col).addClass('hit');
                    } else if (currfield == 3) {
                        $(col).addClass('missed');
                    } else if (currfield == 6) {
                        $(col).addClass('blocked');
                    } else if (currfield == 5) {
                        if (data['content']['enemy_field'][(j - 2) + "" + (i - 1)] == 0) //Links
                            $(table.rows[i].cells[j - 1]).addClass('blocked');
                        if (data['content']['enemy_field'][(j) + "" + (i - 1)] == 0) //Rechts
                            $(table.rows[i].cells[j + 1]).addClass('blocked');
                        if (data['content']['enemy_field'][(j - 1) + "" + (i - 2)] == 0) //Unten
                            $(table.rows[i - 1].cells[j]).addClass('blocked');
                        if (data['content']['enemy_field'][(j - 1) + "" + (i)] == 0) //Oben
                            $(table.rows[i + 1].cells[j]).addClass('blocked');
                        $(col).addClass('dead');
                    }
                }
            }
        }

        var table = document.getElementById("left");
        var currfield;
        for (var i = 1, row; row = table.rows[i]; i++) {
            for (var j = 1, col; col = row.cells[j]; j++) {
                currfield = data['content']['own_field'][(j - 1) + "" + (i - 1)];
                if (currfield == 1) {
                    $(col).addClass('shipplaced');
                } else if (currfield == 2) {
                    $(col).addClass('hit');
                } else if (currfield == 4 || currfield == 6) {
                    if (!data['content']['game_started'])
                        $(col).addClass('blocked');
                } else if (currfield == 5) {
                    $(col).addClass('dead');
                }
            }
        }
    }
}