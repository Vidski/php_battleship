<?php

interface iHandler
{
    public function action($msgObj, $socket = null);
    public function build_packet($function, $action, $content);
}
