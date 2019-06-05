//TODOS
//SCALE SHIP SIZE WITH $(td).width()

//CLICK EVENTS
$("#btn_inputUsername").click(function (event) {
    event.preventDefault();
    var name = $("#inputUsername").val();
    userHandler.send_set_username(name);
    $('#btn_inputUsername').prop("disabled", true);
});

$("#btn_createRoom").click(function (event) {
    event.preventDefault();
    roomHandler.send_create_room();
    $('#btn_inputUsername').prop("disabled", true);
});

$("#btn_joinRoom").click(function (event) {
    event.preventDefault();
    var pin = $('#joinRoomPin').val()
    roomHandler.send_join_room(pin);
});

$("#btn_sendMessage").click(function (event) {
    event.preventDefault();
    var message = $('#input_sendMessage').val();
    roomHandler.send_message_room(message);
});

$("#btn_ready").click(function (e) {
    e.preventDefault();
    ready();
});

$("#field_right table").on("click", "td", function () {
    battleshipHandler.send_shoot($(this).attr('data-col'), $(this).attr('data-row'));
});



//UI EVENTS
function loginBoxError() {
    $('#login_box_connecting p').text("Could not connect to Server");
    $('#login_box_connecting .spinner-grow').removeClass('text-warning');
    $('#login_box_connecting .spinner-grow').addClass('text-danger');
    console.error("WebSocket error observed:", ev);
}

function loginBoxSuccess() {
    $('#login_box_connecting .spinner-grow').removeClass('text-warning');
    $('#login_box_connecting .spinner-grow').addClass('text-success');
    $('#login_box_connecting').fadeOut(2000, function () { $('#login_box_form').fadeIn(); });
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
    var cAt = $('td').width() / 3;
    $('#ship2V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });
    $('#ship3V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });
    $('#ship4V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });
    $('#ship5V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });
    $('#ship2H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });
    $('#ship3H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });
    $('#ship4H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });
    $('#ship5H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt } });

    $('td').droppable({
        tolerance: "pointer",
        drop: Drop,
        over: Over
    });
}

function Drop(event, ui) {
    battleshipHandler.send_place($(this).attr('data-col'), $(this).attr('data-row'), ui['draggable'][0]['id']);
}

function Over(event, ui) {
    var classes = $(ui['helper']).attr("class");
    var classes_arr  = classes.split(" ");

    //TODO ! NOCH NICHT GUT ANGEPASST
    console.log(classes_arr[1].slice(-2, -1));
    if (classes_arr[1].slice(-1) == 'V') {
        $(ui['helper']).height(($(event['target']).height() * parseInt(classes_arr[1].slice(-2, -1))) + parseInt(classes_arr[1].slice(-2, -1)) + 5);
        $(ui['helper']).width($(event['target']).width());
    }
    else {
        $(ui['helper']).width(($(event['target']).width() * parseInt(classes_arr[1].slice(-2, -1))) + parseInt(classes_arr[1].slice(-2, -1)));
        $(ui['helper']).height($(event['target']).height());
    }
    $(ui['helper']).css('background-size', $(event['target']).width() + "px " + $(event['target']).height() + "px");
}

function ready() {
    var table = document.getElementById("left");
    for (var i = 0, row; row = table.rows[i]; i++) {
        for (var j = 0, col; col = row.cells[j]; j++) {
            if ($(col).hasClass('blocked'))
                $(col).removeClass('blocked')
        }
    }
    $('#btn_ready').hide();
    $('#ships').hide();
    $('#field_right').show();
}

//RESPONSIVE SHIPS
$(window).resize(function () {
    $('#ship2V').width($('td').width());
    $('#ship3V').width($('td').width());
    $('#ship4V').width($('td').width());
    $('#ship5V').width($('td').width());
    $('#ship2V').height(($('td').height() * 2));
    $('#ship3V').height(($('td').height() * 3));
    $('#ship4V').height(($('td').height() * 4));
    $('#ship5V').height(($('td').height() * 5));
    $('#ship2V').css('background-size', $('td').width() + "px " + $('td').height() + "px");
    $('#ship3V').css('background-size', $('td').width() + "px " + $('td').height() + "px");
    $('#ship4V').css('background-size', $('td').width() + "px " + $('td').height() + "px");
    $('#ship5V').css('background-size', $('td').width() + "px " + $('td').height() + "px");

    $('#ship2H').width($('td').width() * 2);
    $('#ship3H').width($('td').width() * 3);
    $('#ship4H').width($('td').width() * 4);
    $('#ship5H').width($('td').width() * 5);
    $('#ship2H').height($('td').height());
    $('#ship3H').height($('td').height());
    $('#ship4H').height($('td').height());
    $('#ship5H').height($('td').height());
    $('#ship2H').css('background-size', $('td').width() + "px " + $('td').height() + "px");
    $('#ship3H').css('background-size', $('td').width() + "px " + $('td').height() + "px");
    $('#ship4H').css('background-size', $('td').width() + "px " + $('td').height() + "px");
    $('#ship5H').css('background-size', $('td').width() + "px " + $('td').height() + "px");

});

//HELPERS
function getCurrentTime() {
    var d = new Date();
    return ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);
}