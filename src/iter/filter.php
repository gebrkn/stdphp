<?php

class _FilterIterator implements \Iterator
{
    function __construct($func, $iter) {
        $this->it = STD\iter($iter);
        $this->func = $func;
        $this->n = 0;
        $this->val = null;
    }

    function _getval() {
        while($this->it->valid()) {
            $x = $this->it->current();
            if(call_user_func($this->func, $x)) {
                $this->val = $x;
                return true;
            }
            $this->it->next();
        }
        return false;
    }

    public function current() {
        return $this->val;
    }

    public function key() {
        return $this->n;
    }

    public function next() {
        $this->it->next();
        $this->n++;
    }

    public function rewind() {
        $this->it->rewind();
        $this->n = 0;
    }

    public function valid() {
        return $this->_getval();
    }
}
