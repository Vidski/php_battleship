<?php

interface iHandler
{
    public function action($messageObj, $user = null);
}
