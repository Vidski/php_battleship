<?php

interface iHandler
{
    public function action($messageObj, $user = null);
    public function build_packet($function, $action, $content);
}
