<?php

/**
 * @private

 * @test
 *
 * $it = new std\_ListIterator(array(11,22,33));
 * $a = array();
 * foreach($it as $x) $a []= $x;
 * implode(',', $a); /// '11,22,33'
 *
 * @endtest
 *
 */
class _ListIterator implements \Iterator
{
    protected $a;
    protected $len;
    protected $n;

    function __construct($x) {
        $this->a = $x;
        $this->n = 0;
        $this->len = count($this->a);
    }
    public function current() {
        return $this->a[$this->n];
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
        return $this->n < $this->len;
    }
}

class _ArrayIterator extends _ListIterator {
    function __construct($x) {
        parent::__construct(_is_list($x) ? $x : array_values($x));
    }
}

class _ByteStringIterator extends _ListIterator
{
    function __construct($x) {
        parent::__construct($x);
        $this->len = strlen($x);
    }
}
