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
    roomHandler.send_message(message);
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

//HELPERS
function getCurrentTime() {
    var d = new Date();
    return ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);
}