<?php

function _iter($xs) {
    switch(gettype($xs)) {
        case 'array':
            return new _ArrayIterator($xs);
        case 'string':
            return STD\str($xs)->getIterator();
        case 'object':
            if($xs instanceof \Iterator)
                return $xs;
            if(method_exists($xs, 'getIterator'))
                return $xs->getIterator();
            return new _AssocIterator(get_object_vars($xs));
    }
    return null;
}

/**
 * Consume an iterator.
 * @private
 */
function _read_iter($it, $count=null) {
    $a = array();
    if(is_null($count)) {
        foreach($it as $e) {
            $a []= $e;
        }
        return $a;
    }
    $n = 0;
    foreach($it as $e) {
        $a []= $e;
        if(++$n > $count) {
            // nb: unlike python, we don't complain if the iter
            // is _longer_ than expected
            return $a;
        }
    }
    if($n < $count)
        return null;
    return $a;
}

#include filter
#include list
#include pairs
#include range
#include repeat
#include zip
