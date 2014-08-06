<?php

class _RepeatIterator implements \Iterator
{
    protected $value;
    protected $count;
    protected $n;

    function __construct($value, $count=null) {
        $this->value = $value;
        $this->count = $count;
        $this->rewind();
    }
    public function current() {
        return $this->value;
    }
    public function key() {
        return $this->n;
    }
    public function next() {
        $this->n++;
    }
    public function rewind() {
        $this->n = 0;
    }
    public function valid() {
        return is_null($this->count) || $this->n < $this->count;
    }
}
