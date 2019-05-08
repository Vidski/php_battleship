<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8' />
    <style type="text/css">
		.message_box {
            background: #FFFFFF;
            height: 300px;
            overflow: auto;
            padding: 10px;
            border: 1px solid #999999;
        }
        
        input {
            padding: 2px 2px 2px 5px;
        }
        
		#chat_box {
			/*display: none;*/
		}
		
        #message {
            width: 60%;
        }
        
        .system {
            color: red;
        }
        
        .user {
            color: green;
        }
    </style>
</head>

<body>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {

            //Neues WebSocket object erzeugen
            //var wsUri = "ws://ux-113.pb.bib.de:6969";
			var wsUri = "ws://127.0.0.1:6969";
            websocket = new WebSocket(wsUri);

            //#### Verbindung zum Server wurde geöffnet??
            websocket.onopen = function(ev) {
                //alert("verbunden");
            }

            //#### Nachricht vom Server??
            websocket.onmessage = function(ev) {
                msgObject = JSON.parse(ev.data);

                console.log(msgObject);
                //TODO SWITCH CASE (?)
                if (msgObject['handler'] == 'chat_handler' && msgObject['action'] == 'receive_message') {
                    $('#message_box').append('<span>' + msgObject['content'] + '</span><br>');
                }

                console.log(ev.data);
            };

            //#### Fehler??
            websocket.onerror = function(ev) {
                console.error("WebSocket error observed:", ev);
            };

            //#### Verbindung zum Server wurde getrennt??
            websocket.onclose = function(ev) {
                $('#message_box').append('<span class="system">Verbindung vom Server getrennt</span><br>');
            };

            $("#send-btn").click(function(event) {
                event.preventDefault();
                var message = $("#message").val();
                websocket.send(JSON.stringify({
                    "handler": "chat_handler",
                    "action": "send_message_all",
                    "message": message
                }));
            });

        });
    </script>
	<div id="chat_box">
        <div class="message_box" id="message_box"></div>
        <form>
            <input type="text" name="message" id="message" placeholder="Message"/>
            <button id="send-btn">Send</button>
        </form>
	</div>

</body>

</html>