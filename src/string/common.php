<?php

/**
 * Return a copy of the string with substring `$old` replaced to `$new`.
 *
 *
 * @param string $old
 * @param string $new
 * @param int $count
 * @return String
 *
 * @code
 *
 * $s= STD\s('fuß');
 * print $s->replace('ß', 'ss'); /// 'fuss'
 *
 * $s= STD\s('abcabcabc');
 * print $s->replace("bc", 'X', 2); /// 'aXaXabc'
 *
 * @endcode
 *
 */
function replace($old, $new, $count=-1) {
    $old = STD\str($old);
    $new = STD\str($new);
    $s = preg_replace(_regex($old), strval($new), $this->_str, $count);
    return STD\str($s);
}

/**
 * Split a string.
 *
 * @param string $sep
 * @param int $limit
 * @return Lst
 *
 * @code
 *
 * $a= STD\s('ab|cd|ef');
 * print $a->split('|'); /// ["ab","cd","ef"]
 *
 * $a= STD\s("  ab   cd      ef  \n\n\r   gh   ");
 * print $a->split(); /// ["ab","cd","ef","gh"]
 *
 * @endcode
 *
 */
function split($sep=null, $limit=-1) {
    if(is_null($sep)) {
        $r = preg_split('~\\s+~', $this->_str, -1, PREG_SPLIT_NO_EMPTY);
    } else {
        $sep = STD\str($sep);
        if($limit > 0) {
            $r = preg_split(_regex($sep), $this->_str, $limit);
        } else {
            $r = explode(strval($sep), $this->_str);
        }
    }
    return _strlist($r);
}

/**
 * Split a string by newlines.
 *
 * @return Lst
 *
 * @code
 *
 * $a= STD\s("
 * Lorem ipsum dolor
 *
 * sit amet, consectetur
 * adipisicing elit");
 *
 * print $a->splitlines(); /// [""," Lorem ipsum dolor",""," sit amet, consectetur"," adipisicing elit"]
 *
 * @endcode
 *
 */
function splitlines() {
    return _strlist(preg_split('~\r\n|[\r\n]~u', $this->_str));
}

/**
 * Return true if the string contains any of the substrings.
 *
 * @param string $sub,...
 * @return bool
 *
 * @code
 *
 * $s= STD\s("füßchen");
 *
 * $s->contains("ßch");      /// true
 * $s->contains("foo");      /// false
 * $s->contains("foo", "f"); /// true
 *
 * @endcode
 *
 */
function contains($sub) {
    foreach(func_get_args() as $sub) {
        $p = $this->find($sub);
        if(!is_null($p)) {
            return true;
        }
    }
    return false;
}

/**
 * Return true if the string ends with any of the substrings.
 *
 * @param string $sub,...
 * @return bool
 *
 * @code
 *
 * STD\s('foobar')->endswith('bar'); /// true
 * STD\s('foobar')->endswith('a', 'r'); /// true
 *
 * @endcode
 *
 * @test
 *
 * STD\s('abcd')->endswith('d'); /// true
 * STD\s('abcd')->endswith('x'); /// false
 *
 * STD\s('aböx')->endswith('öx'); /// true
 * STD\s('aböx')->endswith('xx', 'öx'); /// true

 * @endtest
 *
 */
function endswith($sub) {
    foreach(func_get_args() as $sub) {
        $s = STD\str($sub);
        $p = $this->find($s);
        if(!is_null($p) && count($s) + $p === count($this)) {
            return true;
        }
    }
    return false;
}

/**
 * Return true if the string starts with any of the substrings.
 *
 * @param string $sub,...
 * @return bool
 *
 * @code
 *
 * STD\s('abcd')->startswith('a'); /// true
 *
 * @endcode
 *
 *
 * @test
 *
 * STD\s('abcd')->startswith('x'); /// false
 * STD\s('abcd')->startswith('x', 'a'); /// true

 * @endtest
 *
 */
function startswith($sub) {
    foreach(func_get_args() as $sub) {
        $s = STD\str($sub);
        $p = $this->find($s);
        if($p === 0)
            return true;
    }
    return false;
}

/**
 * Count substrings in the string.
 *
 * @param string $sub
 * @return int
 *
 * @code
 *
 * $s= STD\s('abüxabüyabüz');
 * print $s->tally('abü'); /// 3
 * print $s->tally('abx'); /// 0
 *
 * @endcode
 */
function tally($sub) {
    return substr_count($this->_str, strval(str($sub)));
}

/**
 * Remove leading and trailing characters from the string.
 *
 * @param string $chars
 * @return String
 *
 * @code
 *
 * print STD\s(' abc ')->strip(); /// 'abc'
 * print STD\s('~!abc@~')->strip('~!@'); /// 'abc'
 *
 * @endcode
 *
 * @test
 *
 * print STD\s('~ßüxßaüxßbcßüxß~')->strip('~xüß'); /// 'aüxßbc'

 * @endtest
 *
 */
function strip($chars=null) {
    if(is_null($chars))
        return STD\str(trim($this->_str));
    $r = preg_quote(strval(str($chars)), '~');
    $r = "~^[$r]+|[$r]+$~u";
    return STD\str(preg_replace($r, '', $this->_str));
}


/**
 * Split the string by `$sep` and return a list (before, sep, after).
 *
 * @param $sep
 * @return Lst
 *
 * @code
 *
 * print STD\s('abcZZef')->partition('ZZ'); /// ["abc","ZZ","ef"]
 * print STD\s('ßüåßüß')->partition('åß');  /// ["ßü","åß","üß"]
 *
 * @endcode
 *
 *
 */
function partition($sep) {
    $sep = STD\str($sep);
    $p = $this->find($sep);
    if(is_null($p))
        return a($this, STD\str(''), STD\str(''));
    return a(
        $this[":$p"],
        $sep,
        $this[($p + count($sep)) . ":"]);
}

?>