<?php

class _ZipIterator implements \Iterator
{
    function __construct($iters, $opts=null) {
        $this->iters = $iters;
        $this->opts = $opts ? $opts : null;
        $this->n = 0;
        $this->curr = null;
    }
    public function current() {
        if(!isset($this->opts['func']))
            return STD\lst($this->curr);
        return call_user_func_array($this->opts['func'], $this->curr);
    }
    public function key() {
        return $this->n;
    }
    public function next() {
        $this->n++;
    }
    public function rewind() {
        foreach($this->iters as $it)
            $it->rewind();
        $this->n = 0;
    }
    public function valid() {
        $this->curr = array();
        $v = false;
        foreach($this->iters as $it) {
            if($it->valid()) {
                $this->curr []= $it->current();
                $it->next();
                $v = true;
            } else if(isset($this->opts['fill'])) {
                $this->curr []= $this->opts['fill'];
            } else
                return false;
        }
        return $v;
    }
}
