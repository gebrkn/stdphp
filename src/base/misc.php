<?php

function _strlist($xs) {
    $a = STD\lst();
    foreach($xs as $x) {
        $a []= STD\str($x);
    }
    return $a;
}


/**
 * @private
 *
 * @test
 *
 * std\_is_list(array(1,2,3,4)); /// true
 * std\_is_list(array(1,2,'baa'=>3,4)); /// false
 *
 * @endtest
 *
 */
function _is_list($a) {
    if(!is_array($a))
        return false;
    $n = 0;
    foreach($a as $k => $v) {
        if($k !== $n++)
            return false;
    }
    return true;
}

function _cvt_sort_keys(&$keys) {
    foreach($keys as $i => $k) {
        if(is_string($k))
            $keys[$i] = '_' . $k;
        else if(is_array($k))
            _cvt_sort_keys($keys[$i]);
    }
}

function _is($x, $type) {
    switch($type) {
        case 'array':   case 'bool':  case 'callable':
        case 'double':  case 'float': case 'int':
        case 'integer': case 'long':  case 'null':
        case 'numeric': case 'real':  case 'resource':
        case 'scalar':  case 'string':
            $fn = "is_$type";
            return $fn($x);
        case 'iterable':
            return !is_null(\std\_iter($x));
    }

    if(!is_object($x))
        return false;

    switch($type) {
        case 'str':
            return $x instanceof \std\String;
        case 'list':
        case 'lst':
            return $x instanceof \std\Lst;
        case 'dict':
            return $x instanceof \std\Dict;
        case 'set':
            return $x instanceof \std\Set;
        case 're':
            return $x instanceof \std\Re;
        default:
            return $x instanceof $type;
    }
}
