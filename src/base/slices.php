<?php
/**
 * Parse an integer.
 * @private
 *
 */
function _parse_int($x) {
    switch(gettype($x)) {
        case 'integer':
            return $x;
        case 'double':
            return intval($x);
        case 'string':
            if(preg_match('~^-?[0-9]+$~', $x))
                return intval($x);
    }
    return null;
}

function _is_int($x) {
    return !is_null(_parse_int($x));
}


/**
 * Parse an index for the given length.
 * @private
 *
 * @test
 *
 * std\_parse_index(1, 10);  /// 1
 * std\_parse_index(-3, 10); /// 7
 * std\_parse_index(100, 10); /// null
 * std\_parse_index(-100, 10); /// null
 * std\_parse_index('foo', 10); /// null
 *
 * @endtest
 *
 */
function _parse_index($x, $len) {
    if(!is_int($x))
        return null;
    if($x < 0)
        $x += $len;
    if($x >= 0 && $x < $len)
        return $x;
    return null;
}

/**
 * Parse a string slice and return array(start,stop,step,size).
 * @private
 *
 * @see http://hg.python.org/cpython/file/2.7/Objects/sliceobject.c#l132
 *
 * @test
 *
 * std\_parse_slice("0:7", 10); /// array(0,7,1,7)
 * std\_parse_slice("0:6:2", 10); /// array(0,6,2,3)
 * std\_parse_slice(":6:2", 10); /// array(0,6,2,3)
 * std\_parse_slice("::2", 10); /// array(0,10,2,5)
 * std\_parse_slice("zzz::2", 10); /// null
 *
 * @endtest
 *
 */
function _parse_slice($x, $len) {
    $x = explode(':', $x);
    if(count($x) < 2 || count($x) > 3)
        return null;
    return _parse_slice3(
        strlen($x[0]) ? $x[0] : null,
        strlen($x[1]) ? $x[1] : null,
        isset($x[2]) && strlen($x[2]) ? $x[2] : null,
        $len
    );
}

/**
 * Parse a 3-elements slice and return array(start,stop,step,size).
 * @private
 *
 */
function _parse_slice3($start, $stop, $step, $len) {

    if(!is_null($start) && is_null($start = _parse_int($start))) return null;
    if(!is_null($stop)  && is_null($stop  = _parse_int($stop)))  return null;
    if(!is_null($step)  && is_null($step  = _parse_int($step)))  return null;

    if(is_null($step)) $step = 1;

    if($step === 0)
        return null;
    $back = $step < 0;

    if(is_null($start)) {
        $start = $back ? $len - 1 : 0;
    } else {
        if($start < 0) $start += $len;
        if($start < 0) $start = $back ? -1 : 0;
        if($start >= $len) $start = $back ? $len - 1: $len;
    }

    if(is_null($stop)) {
        $stop = $back ? -1 : $len;
    } else {
        if($stop < 0) $stop += $len;
        if($stop < 0) $stop = $back ? -1 : 0;
        if($stop >= $len) $stop = $back ? $len - 1 : $len;
    }

    $size = 0;
    if(!$back && $start <= $stop)
        $size = ($stop - $start - 1) / $step + 1;
    if($back && $start >= $stop)
        $size = ($stop - $start + 1) / $step + 1;

    return array($start, $stop, $step, intval($size));
}

/**
 * Apply a slice to an array.
 * @private
 */
function _apply_slice($ary, $start, $stop, $step) {
    $new = array();
    if($step > 0)
        for($i = $start; $i < $stop; $i += $step)
            $new []= $ary[$i];
    else
        for($i = $start; $i > $stop; $i += $step)
            $new []= $ary[$i];
    return $new;
}
