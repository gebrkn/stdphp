<?php

/**
 * Create a String object from the argument.
 *
 * @param $x
 * @param string $encoding
 * @return String
 *
 * @code
 *
 * $a = STD\s("fuß!");
 * print $a; /// fuß!
 * $a = STD\s("fu\xDF!", 'cp1252');
 * print $a; /// fuß!
 *
 * @endcode
 *
 */
function s($x, $encoding=null) {
    return str($x, $encoding);
}

function str($x, $encoding=null) {
    if($x instanceof String)
        return $x;
    $x = strval($x);
    if(\std\_is_ascii($x)) {
        $cls = \std\_getclass('String');
    } else {
        $x = \std\_to_utf8($x, $encoding);
        $cls = \std\_getclass('_UnicodeString');
    }
    return new $cls($x);
}


/**
 * Create a String object from codepoints.
 *
 * @param int $c,...
 * @return String
 *
 * @code
 *
 * print STD\char(0x44, 0xDF, 0x416); /// 'DßЖ'
 *
 * @endcode
 *
 */
function char($c) {
    $s = '';
    foreach(func_get_args() as $c) {
        $s .= \std\_utf8_chr($c);
    }
    return str($s);
}


/**
 * Create a list from arguments.
 *
 * @return Lst
 *
 * @code
 * print STD\a(11, 22, 33, 44); /// [11,22,33,44]
 * @endcode
 *
 */
function a() {
    $cls = \std\_getclass('Lst');
    return new $cls(func_get_args());
}

/**
 * Create a list from an iterable.
 *
 * @param Traversable $xs
 * @return Lst
 *
 * @code
 *
 * print STD\lst("abcd"); /// ["a","b","c","d"]
 *
 * @endcode
 *
 */
function lst($xs=null) {
    $cls = \std\_getclass('Lst');
    return new $cls($xs);
}

/**
 * Create a dict from an associative Traversable.
 *
 * @param Traversable $xs
 * @return Dict
 *
 * @code
 *
 * $a = STD\dict(array('x'=>1, 'y'=>2));
 * print $a; /// '{"x":1,"y":2}'
 *
 * $b = STD\dict($a);
 * $b['x'] = 9;
 *
 * print $a; /// '{"x":1,"y":2}'
 * print $b; /// '{"x":9,"y":2}'
 *
 * @endcode
 *
 */
function dict($xs=null) {
    $cls = \std\_getclass('Dict');
    return new $cls($xs);
}

/**
 * Create a dict from arguments.
 * Odd arguments are keys, even args are values.
 *
 * @return Dict
 *
 * @code
 *
 * print STD\d('a', 1, 'b', 2); /// '{"a":1,"b":2}'
 *
 * @endcode
 *
 */
function d() {
    $d = dict();
    $args = func_get_args();
    for($i = 0; $i < count($args); $i += 2) {
        $d[$args[$i]] = $args[$i + 1];
    }
    return $d;
}


/**
 * Create a dict from an array of key/value pairs.
 *
 * @param Traversable $pairs
 * @return Dict
 *
 * @code
 *
 * $a = array(
 *    array('foo', 11),
 *    array('bar', 22));
 * print STD\pairdict($a); /// '{"foo":11,"bar":22}'
 *
 * @endcode
 *
 */
function pairdict($pairs) {
    $d = dict();
    foreach(STD\pairs($pairs) as $k => $v) {
        $d[$k] = $v;
    }
    return $d;

}

/**
 * Create a dict with keys from `$keys` and values set to `$val`.
 *
 * @param Traversable $keys
 * @param mixed $val
 * @return Dict
 *
 * @code
 *
 * print STD\keydict('abc', 9); /// '{"a":9,"b":9,"c":9}'
 *
 * @endcode
 *
 */
function keydict($keys, $val) {
    $d = dict();
    foreach(iter($keys) as $k)
        $d[$k] = $val;
    return $d;
}

/**
 * Create a set from an iterable.
 *
 * @param Traversable $xs
 * @return Set
 *
 * @code
 *
 * print STD\set([1,2,3,2]); /// [1,2,3]
 *
 * @endcode
 *
 */
function set($xs=null) {
    $cls = \std\_getclass('Set');
    return new $cls($xs);
}


/**
 * Create a regular expression object.
 * Unlike php, no delimiters required, flags can be provided in a second argument.
 *
 * @param string $pattern
 * @param string $flags
 * @return Re
 *
 */
function re($pattern, $flags=null) {
    if($pattern instanceof \std\Re)
        return $pattern;
    $cls = \std\_getclass('Re');
    return new $cls($pattern, $flags);
}
