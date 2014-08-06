<?php

/**
 * Return the index of substring in the string.
 *
 * @param string $sub
 * @return int|null
 *
 * @code
 *
 * $s= STD\s("abcdabcd");
 * $s->find("bcd"); /// 1
 * $s->find("xyz"); /// null
 *
 * @endcode
 *
 * @test
 *
 * $s= STD\s("füßchen");
 * $s->find("c"); /// 3
 * $s->find("ßch"); /// 2
 *
 * $s= STD\s("ü123");
 * $s->find("ü"); /// 0

 * @endtest
 *
 */
function find($sub) {
    $p = strpos($this->_str, strval(str($sub)));
    if($p === false)
        return null;
#ifdef UNICODE
    $n = 0;
    while($p > 0) {
        $p -= strlen($this->_chars[$n++]);
    }
    return $p === 0 ? $n : null;
#else
    return $p;
#endif
}

/**
 * Capitalize the string.
 */
function capitalize() {
#ifdef UNICODE
    $s = _utf8_upper($this->_chars[0]) . _utf8_lower(substr($this->_str, strlen($this->_chars[0])));
#else
    $s = ucfirst(strtolower($this->_str));
#endif
    return STD\str($s);
}

/**
 * Convert the stirng to upper case.
 */
function upper() {
#ifdef UNICODE
    $s = _utf8_upper($this->_str);
#else
    $s = strtoupper($this->_str);
#endif
    return STD\str($s);
}

/**
 * Convert the stirng to lower case.
 */
function lower() {
#ifdef UNICODE
    $s = _utf8_lower($this->_str);
#else
    $s = strtolower($this->_str);
#endif
    return STD\str($s);
}

/**
 * Return a reversed copy of the stirng.
 *
 * @code
 *
 * print STD\s('füße')->reverse(); /// 'eßüf'
 *
 * @endcode
 *
 *
 */
function reverse() {
#ifdef UNICODE
    $s = implode('', array_reverse($this->_chars));
#else
    $s = strrev($this->_str);
#endif
    return STD\str($s);
}
