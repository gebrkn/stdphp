<?php

/**
 * The list class.
 *
 * Lists support slicing like in python:
 *
 * @code
 *
 * $xs = STD\lst('0123456789');
 * print $xs['1::3']; /// ["1","4","7"]
 *
 * $xs['1::2'] = STD\repeat('x', 5);
 * print $xs; /// ["0","x","2","x","4","x","6","x","8","x"]
 *
 * unset($xs['1::2']);
 * print $xs; /// ["0","2","4","6","8"]

 * @endcode
 *
 */
class Lst extends MutableSequence implements \JsonSerializable
{
    protected $_items;

    function __construct($xs=null) {
        $this->_items = array();
        if($xs)
            foreach(iter($xs) as $x)
                $this->_items []= $x;
    }

    function count() {
        return count($this->_items);
    }

    function at($index) {
        return $this->_items[$index];
    }

    function _slice($start, $stop, $step) {
        return STD\lst(_apply_slice($this->_items, $start, $stop, $step));
    }

    function getIterator() {
        return new _ListIterator($this->_items);
    }

    function __toString() {
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE);
    }

    function jsonSerialize() {
        $a = array();
        foreach($this->_items as $x) {
            if(is_object($x) && method_exists($x, 'jsonSerialize'))
                $a []= $x->jsonSerialize();
            else
                $a []= $x;
        }
        return $a;

    }

    #include offset
    #include misc
}