<?php

interface iHandler
{
    public function action($msgObj);
    public function build_packet($messageAll, $action, $content);
}
