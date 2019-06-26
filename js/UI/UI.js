//TODOS
//SCALE SHIP SIZE WITH $(td).width()

//CLICK EVENTS
$("#form_inputUsername").submit(function(event) {
    event.preventDefault();
    var name = $("#inputUsername").val();
    userHandler.send_set_username(name);
    $('#form_inputUsername').prop("disabled", true);
    $('#username').text(name);
});

$("#battleshipcover").click(function(event) {
    event.preventDefault();
    roomHandler.send_create_room();
    $('#btn_inputUsername').prop("disabled", true);
});

$("#form_joinRoom").submit(function(event) {
    event.preventDefault();
    var pin = $('#joinRoomPin').val()
    roomHandler.send_join_room(pin);
    $('#btn_joinRoom').prop("disabled", true);
});

$("#form_chat").submit(function(event) {
    event.preventDefault();
    var message = $('#input_sendMessage').val();
    $('#input_sendMessage').val("");
    roomHandler.send_message(message);
});

//UI EVENTS
function loginBoxError() {
    $('#login_box_connecting p').text("Could not connect to Server");
    $('#login_box_connecting .spinner-grow').removeClass('text-warning');
    $('#login_box_connecting .spinner-grow').addClass('text-danger');
}

function loginBoxSuccess() {
    $('#login_box_connecting .spinner-grow').removeClass('text-warning');
    $('#login_box_connecting .spinner-grow').addClass('text-success');
    $('#login_box_connecting').fadeOut(2000, function() { $('#login_box_form').fadeIn(); });
}

//HELPERS
function getCurrentTime() {
    var d = new Date();
    return ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);
}


function show_modal(title, body) {
    $('#exampleModal .modal-title').text(title);
    $('#exampleModal .modal-body').text(body);
    $('#exampleModal').modal('show');
}