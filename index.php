<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8' />
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>

<body>

    <?php require dirname(__FILE__) . '/html/login_box.html'; ?>
    <?php require dirname(__FILE__) . '/html/menu_box.html'; ?>

    <div id="battleship_game_box" class="container" style="padding-top: 50px;">
        <!-- Battlefield -->
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6" id="ships">
                <div class="card">
                    <div class="card-body">
                        <div id="ship2V" class="ship ship2V"></div>
                        <div id="ship3V" class="ship ship3V"></div>
                        <div id="ship4V" class="ship ship4V"></div>
                        <div id="ship5V" class="ship ship5V"></div>
                        <div id="ship2H" class="ship ship2H"></div>
                        <div id="ship3H" class="ship ship3H"></div>
                        <div id="ship4H" class="ship ship4H"></div>
                        <div id="ship5H" class="ship ship5H"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" id="field_left">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-sm" id="left">
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" id="field_right">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-sm">
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
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <form id="form_chat" autocomplete="off">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Message" id="input_sendMessage" autocomplete="off">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">Send</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require dirname(__FILE__) . '/html/scripts.html'; ?>
    <?php require dirname(__FILE__) . '/html/navbar.html'; ?>

</body>

</html>