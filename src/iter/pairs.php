<?php

/**
 * @private
 *
 * @test
 *
 * $it = new std\_PairsIterator(array(array('a',11),array('b',22),array('c',33)));
 * $z = "";
 * foreach($it as $k=>$v) $z .= "$k($v)";
 * $z; /// 'a(11)b(22)c(33)'
 *
 * @endtest
 *
 */
class _PairsIterator implements \Iterator
{
    protected $it;

    function __construct($x) {
        $this->it = iter($x);
        $this->val = null;
    }

    public function current() {
        $this->val = $this->it->current();
        return $this->val[1];
    }

    public function key() {
        return $this->val[0];
    }

    public function next() {
        $this->it->next();
    }

    public function rewind() {
        $this->it->rewind();
    }

    public function valid() {
        return $this->it->valid();
    }
}

/**
 * @private
 *
 * @test
 *
 * $it = new std\_AssocIterator(array('a'=>11,'b'=>22,'c'=>33));
 * $z = "";
 * foreach($it as $k=>$v) $z .= "$k($v)";
 * $z; /// 'a(11)b(22)c(33)'
 *
 * @endtest
 *
 */
class _AssocIterator implements \Iterator
{
    protected $a;
    protected $len;
    protected $n;

    function __construct($x) {
        $this->a = $x;
        $this->keys = array_keys($x);
        $this->n = 0;
        $this->len = count($this->a);
    }
    public function current() {
        return $this->a[$this->keys[$this->n]];
    }
    public function key() {
        return $this->keys[$this->n];
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
