<?php

require 'GameServer.php';

$host = '172.18.1.113';
//$host = '127.0.0.1';
$port = 6969;

$server = new GameServer($host, $port);
