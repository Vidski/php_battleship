<?php

/**
 * Stack implementierung
 *
 * @author  David Rydwanski
 */
class Stack
{

    private $arr = array();

    public function push($s)
    {
        $this->arr[] = $s;
    }

    public function pop()
    {
        return array_pop($this->arr);
    }

    public function peek()
    {
        return end($this->arr);
    }

    public function is_empty()
    {
        return empty($this->arr);
    }

    public function length() {
        return count($this->arr);
    }
}
