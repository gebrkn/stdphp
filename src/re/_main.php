<?php

/**
 * Regular expression.
 */
class Re
{
    protected $_re;

    function __construct($pattern, $flags=null) {
        $pattern = strval($pattern);
        if(strpos($pattern, "\x01") !== false)
            $pattern = str_replace("\x01", '\\x01', $pattern);
        $this->_re = "\x01{$pattern}\x01{$flags}";
    }

    function __toString() {
        return $this->_re;
    }

    /**
     * Split a string by the regular expression.
     *
     * @param string $text
     * @param int $limit
     * @return Lst
     *
     * @code
     *
     * print STD\re('\W+')->split('aa...bb...cc'); /// ["aa","bb","cc"]
     *
     * @endcode
     *
     */
    function split($text, $limit=-1) {
        return _strlist(preg_split($this->_re, $text, $limit, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Search `$text` for the regexp and return `true` or `false`.
     *
     * @param string $text
     * @return bool
     *
     * @code
     *
     * STD\re('[a-z]')->test('123ab'); /// true
     * STD\re('[a-z]')->test('123XX'); /// false
     *
     * @endcode
     *
     */
    function test($text) {
        return preg_match($this->_re, strval($text)) === 1;
    }


    /**
     * Search `$text` for the regexp and return a `ReMatchObject` or `null`.
     *
     * @param string $text
     * @return null|ReMatchObject
     *
     * @code
     *
     * $m = STD\re('([a-z]+)(\d)')->match('..foo5..');
     * print $m->groups(); /// ["foo5","foo","5"]
     *
     * @endcode
     *
     */
    function match($text) {
        $r = preg_match($this->_re, strval($text), $m, PREG_OFFSET_CAPTURE);
        return $r ? new ReMatchObject($m) : null;

    }

    /**
     * Search `$text` for the regexp and return a list of matches.
     *
     * @param string $text
     * @return Lst
     *
     * @code
     *
     * $m = STD\re('([a-z]+)(\d)')->find('..foo5..');
     * print $m; /// ["foo5","foo","5"]
     *
     * @endcode
     */
    function find($text) {
        $r = preg_match($this->_re, strval($text), $ms);
        if(!$r) {
            return STD\lst();
        }
        return _strlist($ms);
    }


    /**
     * Search `$text` for all matches and return a list of `ReMatchObject`s.
     *
     * @param string $text
     * @return Lst
     *
     * @code
     *
     * $pat = '(?P<letters>[a-z]+)(\d+)';
     * $ms = STD\re($pat)->matchall('...ab12--cd34...');
     * print $ms[0]->groups(); /// ["ab12","ab","12"]
     * print $ms[1]->groups(); /// ["cd34","cd","34"]
     *
     * @endcode
     *
     */
    function matchall($text) {
        $r = preg_match_all($this->_re, strval($text), $ms, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
        if(!$r) {
            return STD\lst();
        }
        $a = STD\lst();
        foreach($ms as $m) {
            $a []= new ReMatchObject($m);
        }
        return $a;
    }


    /**
     * Search `$text` for all matches and return a list of strings or string arrays.
     *
     * @param string $text
     * @return Lst
     *
     * @code
     *
     * $ms = STD\re('([a-z]+)(\d+)')->findall('ab12--cd34');
     * print $ms; /// '[["ab","12"],["cd","34"]]'
     *
     * $ms = STD\re('[a-z]+')->findall('ab12--cd34');
     * print $ms; /// '["ab","cd"]'
     *
     * $ms = STD\re('[a-z]+(\d+)')->findall('ab12--cd34');
     * print $ms; /// '["12","34"]'
     *
     * @endcode
     *
     */
    function findall($text) {
        $r = preg_match_all($this->_re, strval($text), $ms, PREG_SET_ORDER);
        if(!$r) {
            return STD\lst();
        }
        $a = STD\lst();
        foreach($ms as $m) {
            $a []= (count($m) <= 2) ? STD\str(end($m)) : _strlist(array_slice($m, 1));
        }
        return $a;
    }

    /**
     * Perform search and replace.
     *
     * @param string $repl
     * @param string $text
     * @param int $count
     * @return String
     *
     * @code
     *
     * print STD\re('\W+')->sub('*', 'a,b,c'); /// 'a*b*c'
     * print STD\re('\W+')->sub('*', 'a,b,c', 1); /// 'a*b,c'
     *
     * @endcode
     *
     */
    function sub($repl, $text, $count=-1) {
        $text = preg_replace($this->_re, strval($repl), strval($text), $count);
        return STD\str($text);
    }

    /**
     * Perform search and replace using a callback.
     * The callback takes (number of groups + 1) string arguments.
     *
     * @param callable $func
     * @param string $text
     * @param int $count
     * @return String
     *
     * @code
     *
     * print STD\re('\w+')->subf('strtoupper', 'a,b,c'); /// 'A,B,C'
     *
     * $swap = function($match, $a, $b) { return $b . $a; };
     * $str  = '..ab12..cd45..';
     *
     * print STD\re('([a-z]+)(\d+)')->subf($swap, $str); /// '..12ab..45cd..'
     *
     *
     * @endcode
     *
     */
    function subf($func, $text, $count=-1) {
        return STD\str(preg_replace_callback($this->_re, function($ms) use($func) {
            $args = array();
            foreach($ms as $m)
                $args []= STD\str($m);
            return call_user_func_array($func, $args);
        }, strval($text), $count));
    }
}

#include matchobj

