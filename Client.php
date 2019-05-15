<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8' />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
                        <div id="login_box_form">
                            <div class="form-group">
                                <input type="text" class="form-control" id="inputUsername" placeholder="Name">
                            </div>
                            <button type="button" id="btn_inputUsername" class="btn btn-primary btn-block">Save</button>
                        </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="battleship_game_box" class="container" style="padding-top: 50px;">
        <!-- Battlefield -->
        <div class="row justify-content-center align-items-center">
            <div class="col-6" id="field_left">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">A</th>
                                    <th scope="col">B</th>
                                    <th scope="col">C</th>
                                    <th scope="col">D</th>
                                    <th scope="col">E</th>
                                    <th scope="col">F</th>
                                    <th scope="col">G</th>
                                    <th scope="col">H</th>
                                    <th scope="col">I</th>
                                    <th scope="col">J</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">5</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">6</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">7</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">8</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">9</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">10</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6" id="field_right">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">A</th>
                                    <th scope="col">B</th>
                                    <th scope="col">C</th>
                                    <th scope="col">D</th>
                                    <th scope="col">E</th>
                                    <th scope="col">F</th>
                                    <th scope="col">G</th>
                                    <th scope="col">H</th>
                                    <th scope="col">I</th>
                                    <th scope="col">J</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">5</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">6</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">7</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">8</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">9</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                                <tr>
                                    <th scope="row">10</th>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                    <td>O</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chat -->
        <div class="row justify-content-center align-items-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div id="chat_box">
                                    <p>David: Hello World! :)</p>
                                    <p>IGN: "Nice Game 11/10"</p>
                                    <p>GameSpot: "Nice Game 11/10"</p>
                                    <p>Polygon: "Nice Game 11/10"</p>
                                    <p>Polygon: "Nice Game 11/10"</p>
                                    <p>Polygon: "Nice Game 11/10"</p>
                                    <p>Polygon: "Nice Game 11/10"</p>
                                    <p>Polygon: "Nice Game 11/10"</p>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Message">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="btn_joinRoom">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="js/client.js"></script>

</body>

</html>