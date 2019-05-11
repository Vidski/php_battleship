<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8' />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div id="login_box" class="container">
        <div class="row justify-content-center align-items-center" style="height:100vh">
            <div class="col-4">
                <div class="card">
                    <div class="card-body" style="height:140px">
                        <div id="login_box_connecting">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-grow text-warning" style="width: 3rem; height: 3rem;" role="status">
                                </div>
                            </div>
                            <p class="text-center"></p>
                        </div>
                        <form action="" autocomplete="off">
                            <div class="form-group">
                                <input type="text" class="form-control" id="inputUsername" placeholder="Name">
                            </div>
                            <button type="button" id="btn_inputUsername" class="btn btn-primary btn-block">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="menu_box" class="container">
        <div class="row justify-content-center align-items-center" style="height:100vh">
            <div class="col-4">
                <div class="card">
                    <div class="card-body" style="height:140px">
                        <form action="" autocomplete="off">
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" disabled id="createRoomPin">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btn_createRoom">Create</button>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" placeholder="0000" id="joinRoomPin">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btn_joinRoom">Join</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="js/client.js"></script>

</body>

</html>