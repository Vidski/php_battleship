//CLICK EVENTS
$("#field_right table").on("click", "td", function () {
    battleshipHandler.send_shoot($(this).attr('data-col'), $(this).attr('data-row'));
});


//FUNCTIONS
/**
 * Funktion um die Spielfelder zu generieren.
 * Hier wird die Data info erstellt und das Drag and Drop für das Plazieren der Schiffe eingerichtet.
 */
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
    $('#ship2V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });
    $('#ship3V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });
    $('#ship4V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });
    $('#ship5V').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });
    $('#ship2H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });
    $('#ship3H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });
    $('#ship4H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });
    $('#ship5H').draggable({ helper: "clone", snap: '.table td', snapMode: "outter", cursorAt: { top: cAt, left: cAt }, containment: "window" });

    $('td').droppable({
        tolerance: "pointer",
        drop: Drop,
        over: Over
    });

    $('#left td').dblclick(function () {
        if ($('#ships').is(":visible"))
            battleshipHandler.send_remove($(this).attr('data-col'), $(this).attr('data-row'));
    });
}

/**
 * Funktion die beim Platzieren eines Schiffes aufgerufen wird.
 * Die Funktion leitet die Aufgabe an den BattleshipHandler weitergeleitet.
 * 
 * @param {*} event 
 * @param {*} ui 
 */
function Drop(event, ui) {
    battleshipHandler.send_place($(this).attr('data-col'), $(this).attr('data-row'), ui['draggable'][0]['id']);
}

/**
 * Funktion die bei dem Halten eines SChiffes über dem Spielfeld aufgerufen wird. 
 * @param {*} event 
 * @param {*} ui 
 */
function Over(event, ui) {
    var classes = $(ui['helper']).attr("class");
    var classes_arr = classes.split(" ");

    //TODO: ! NOCH NICHT GUT ANGEPASST
    //console.log(classes_arr[1].slice(-2, -1));
    if (classes_arr[1].slice(-1) == 'V') {
        $(ui['helper']).height(($(event['target']).height() * parseInt(classes_arr[1].slice(-2, -1))) + parseInt(classes_arr[1].slice(-2, -1)) + 5);
        $(ui['helper']).width($(event['target']).width() + 2);
    } else {
        $(ui['helper']).width(($(event['target']).width() * parseInt(classes_arr[1].slice(-2, -1))) + parseInt(classes_arr[1].slice(-2, -1)) + 6);
        $(ui['helper']).height($(event['target']).height() + 2);
    }
    $(ui['helper']).css('background', 'unset');
    $(ui['helper']).css('background-color', 'rgba(255, 0, 0, .5)');
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