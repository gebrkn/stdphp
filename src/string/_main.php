<?php

#include tools

/**
 * The string class.
 *
 * Strings support negative indexes and slicing:
 *
 * @code
 *
 * $a= STD\s('abcdef');
 * print $a[-2];     /// 'e'
 * print $a['1::3']; /// 'be'
 *
 * @endcode
 *
 * Strings are unicode-aware:
 *
 * @code
 *
 * $a= STD\s('füßchen');
 * print count($a); /// '7'
 *
 * @endcode
 *
 *
 * @test
 *
 * $a= STD\s('abcdef');
 * '-'.$a['1'];  /// '-b'
 * '-'.$a[-1];   /// '-f'
 *
 * try {
 *    $a[100];
 * } catch(std\IndexError $e) {
 *    $e->args[0]; /// 100
 * }
 *
 * $a= STD\s('füchßchen');
 * '-'.$a[1]; /// '-ü'
 * '-'.$a[4]; /// '-ß'
 * strval($a['1:5']); /// 'üchß'
 *
 *
 * @endtest
 *
 */
class String extends Sequence  implements \JsonSerializable
{
    protected $_len;
    protected $_str;

    function __construct($x) {
        $this->_str = strval($x);
        $this->_len = strlen($x);
    }

    function count() {
        return $this->_len;
    }
    
    function jsonSerialize() {
        return $this->_str;
    }
    
    function __toString() {
        return $this->_str;
    }

    function getIterator() {
        return new _ByteStringIterator($this->_str);
    }

    function at($index) {
        return STD\str($this->_str[$index]);
    }

    function _slice($start, $stop, $step) {
        $s = _apply_slice($this->_str, $start, $stop, $step);
        return STD\str(implode('', $s));
    }

    #include common

    #undef UNICODE
    #include ctype
    #include methods
}

class _UnicodeString extends String
{
    protected $_chars;

    function __construct($x) {
        $this->_str = strval($x);
        preg_match_all('~.~su', $this->_str, $m);
        $this->_chars = $m[0];
        $this->_len = count($this->_chars);
    }
    
    function getIterator() {
        return new _ListIterator($this->_chars);
    }

    function at($index) {
        return STD\str($this->_chars[$index]);
    }

    function _slice($start, $stop, $step) {
        $s = _apply_slice($this->_chars, $start, $stop, $step);
        return STD\str(implode('', $s));
    }

    #define UNICODE
    #include ctype
    #include methods
    #undef UNICODE

}


?>