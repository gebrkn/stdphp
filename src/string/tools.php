<?php

function _utf8_chr($c) {
    if ($c < 0x80)
        return \chr($c);
    if ($c < 0x800)
        return \chr(0xc0 | ($c >>  6)) . \chr(0x80 | ($c & 0x3f));
    if ($c < 0x10000)
        return \chr(0xe0 | ($c >> 12)) . \chr(0x80 | (($c >>  6) & 0x3f)) . \chr(0x80 | ($c & 0x3f));
    if($c < 0x200000)
        return \chr(0xf0 | ($c >> 18)) . \chr(0x80 | (($c >> 12) & 0x3f)) . \chr(0x80 | (($c >> 6) & 0x3f)) . \chr(0x80 | ($c & 0x3f));
    throw new UnicodeError($c);
}

function _is_ascii($s) {
    $re = '/\A[\x00-\x7F]*\z/';
    return preg_match($re, $s);
}

function _is_utf8($s) {
    return preg_match('~.~su', $s);
}

function _utf8_upper($s) {
    return mb_strtoupper($s, 'UTF-8');
}
function _utf8_lower($s) {
    return mb_strtolower($s, 'UTF-8');
}

function _to_utf8($s, $from_encoding=null) {
    $from_encoding = $from_encoding ? strtoupper($from_encoding) : 'UTF-8';
    if($from_encoding === 'UTF-8' || $from_encoding === 'UTF8') {
        if(_is_utf8($s))
            return $s;
        throw new UnicodeError($s);
    }
    return mb_convert_encoding($s, 'UTF-8', $from_encoding);
}

function _utf8_type($str, $props) {
    $props = str_replace(',', "}\\p{", $props);
    $re = "~[^\\p{" . $props . "}]~u";
    return !preg_match($re, $str);
}

function _regex($x, $a='', $b='') {
    return "~$a" . preg_quote(strval($x), '~') . "$b~u";
}
?>
