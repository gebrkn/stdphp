<?php
/**
 * @file std.php
 *
 * @mainpage
 *
 * ###Standard library for php.
 *
 * @version 0.0.1
 * @date 2014
 * @author Georg Barikin <georg@thisveryfish.com>
 * @copyright MIT License
 * @see https://github.com/gebrkn/stdphp
 *
 *
 * --------------------------------
 *
 * @ref std.php "Classes and functions reference".
 *
 */

namespace {


/**
 * Create a String object from the argument.
 *
 * @param $x
 * @param string $encoding
 * @return String
 *
 * @code
 *
 * $a = s("fuß!");
 * print $a; /// fuß!
 * $a = s("fu\xDF!", 'cp1252');
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
 * print char(0x44, 0xDF, 0x416); /// 'DßЖ'
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
 * print a(11, 22, 33, 44); /// [11,22,33,44]
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
 * print lst("abcd"); /// ["a","b","c","d"]
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
 * $a = dict(array('x'=>1, 'y'=>2));
 * print $a; /// '{"x":1,"y":2}'
 *
 * $b = dict($a);
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
 * print d('a', 1, 'b', 2); /// '{"a":1,"b":2}'
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
 * print pairdict($a); /// '{"foo":11,"bar":22}'
 *
 * @endcode
 *
 */
function pairdict($pairs) {
    $d = dict();
    foreach(pairs($pairs) as $k => $v) {
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
 * print keydict('abc', 9); /// '{"a":9,"b":9,"c":9}'
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
 * print set([1,2,3,2]); /// [1,2,3]
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

/**
 * Create a closure that picks an item from the argument.
 *
 * @param mixed $key
 * @param null $default
 * @return callable
 *
 */
function getter($key, $default=null) {
    return function($item) use($key, $default) {
        return isset($item[$key]) ? $item[$key] : $default;
    };
}

/**
 * An alias to `getter`.
 */
function by($key, $default=null) {
    return function($item) use($key, $default) {
        return isset($item[$key]) ? $item[$key] : $default;
    };
}

/**
 * Create a closure that picks an attribute from the argument.
 *
 * @param mixed $key
 * @param null $default
 * @return callable
 *
 */
function attr($key, $default=null) {
    return function($item) use($key, $default) {
        return isset($item->$key) ? $item->$key : $default;
    };
}

/**
 * Create a closure that sets a value in the argument.
 *
 * @param $key
 * @param $value
 * @return callable
 *
 */
function setter($key, $value) {
    return function($item) use($key, $value) {
        $item[$key] = $value;
        return $item;
    };
}

/**
 * Create a closure that calls a method on the argument.
 *
 * @param string $name
 * @return callable
 *
 */
function method($name) {
    $args = array_slice(func_get_args(), 1);
    return function($obj) use($name, $args) {
        return call_user_func_array(array($obj, $name), $args);
    };
}

/**
 * Create a closure that performs an operator.
 *
 * @param string $op
 * @return callable
 *
 * @code
 *
 * $add = operator('+');
 * print $add(2, 4); /// 6
 *
 * @endcode
 *
 */
function operator($op) {
    static $bin = array('+', '-', '*', '/', '%', '&', '|', '^', '>>', '<<', '&&', '||', 'and', 'or', 'xor');
    static $uno = array('_', '~', '!', 'not');
    static $fns = array();

    if(isset($fns[$op]))
        return $fns[$op];

    if(in_array($op, $bin)) {
        return $fns[$op] = create_function('$a,$b', "return \$a $op \$b;");
    }

    if(in_array($op, $uno)) {
        $fop = $op;
        if($op == '_')   $fop = '-';
        if($op == 'not') $fop = '!';

        return $fns[$op] = create_function('$a', "return $fop \$a;");
    }

    throw new ValueError('invalid operator', $op);

}

/**
 * Convert the argument to Iterator.
 *
 * @param $xs
 * @return Iterator
 *
 * @test
 *
 * $z = "";
 * foreach(iter(array(1,2,3)) as $x) $z.="($x)";
 * $z; /// '(1)(2)(3)'
 *
 * $z = "";
 * foreach(iter(array(8=>1,7=>2,6=>3)) as $x) $z.="($x)";
 * $z; /// '(1)(2)(3)'
 *
 * $z = "";
 * foreach(iter("wüöl") as $x) $z.="($x)";
 * $z; /// '(w)(ü)(ö)(l)'
 *
 * $z = "";
 * $p = (object) array('a'=>11,'b'=>22,'c'=>33);
 * foreach(iter($p) as $k => $v) $z .= "$k($v)";
 * $z; /// 'a(11)b(22)c(33)'
 *
 * @endtest
 *
 */
function iter($xs) {
    $it = \std\_iter($xs);
    if(is_null($it)) {
        throw new \std\TypeError('object is not iterable', $xs);
    }
    return $it;
}

/**
 * Return the next item from the iterator.
 *
 * @param $it
 * @param null $default
 * @return mixed|null
 * @throws TypeError
 * @throws StopIteration
 *
 */
function succ($it, $default=null) {
    if(!$it instanceof \Iterator) {
        throw new \std\TypeError('object is not an iterator', $it);
    }
    if($it->valid()) {
        $x = $it->current();
        $it->next();
        return $x;
    }
    if(func_num_args() === 2)
        return $default;
    throw new \std\StopIteration();
}

/**
 * Make a range iterator.
 *
 * @param int $a Start or stop value.
 * @param int $b Stop value.
 * @param int $c Step.
 * @return Iterator
 *
 * @code
 *
 * print lst(xrange(5));     /// [0,1,2,3,4]
 * print lst(xrange(2,5));   /// [2,3,4]
 * print lst(xrange(2,8,2)); /// [2,4,6]
 *
 * @endcode
 *
 */
function xrange($a, $b=null, $c=null) {
    $e = is_null($b) ? \std\_parse_range(null, $a, null) : \std\_parse_range($a, $b, $c);
    if(is_null($e)) {
        throw new \std\TypeError('invalid range', $a, $b, $c);
    }
    return new \std\_RangeIterator($e[0], $e[1], $e[2]);
}

/**
 * Make a key/value iterator from a sequence of pairs.
 *
 * @param Traversable $xs
 * @return Iterator
 *
 * @code
 *
 * $z = '';
 * $q = ['ab', 'cd', 'ef'];
 * foreach(pairs($q) as $k => $v)
 *      $z .= "$k($v)";
 * print $z;   /// a(b)c(d)e(f)
 *
 * @endcode
 *
 */
function pairs($xs) {
    return new \std\_PairsIterator($xs);
}

/**
 * Make an iterator that repeats the value count times.
 * If no `count` given, iterate endlessly.
 *
 * @param mixed $val
 * @param int|null $count
 * @return Iterator
 *
 * @code
 *
 * print lst(repeat('a',5)); /// ["a","a","a","a","a"]
 * print lst(zip([1,2,3], repeat(0))); /// [[1,0],[2,0],[3,0]]
 *
 * @endcode
 *
 */
function repeat($val, $count=null) {
    return new \std\_RepeatIterator($val, $count);
}

/**
 * Make an iterator that zips given iterators.
 *
 * @param Traversable $xs,...
 * @return Iterator
 *
 * @code
 *
 * $z = zip('abc', 'XYZ');
 * print lst($z); /// [["a","X"],["b","Y"],["c","Z"]]
 *
 * @endcode
 *
 *
 * @test
 *
 * $a = zip(array(1,2,3), d('x',55,'y',66,'z',77), "fuß");
 * strval(lst($a)); /// '[[1,55,"f"],[2,66,"u"],[3,77,"ß"]]'
 *
 * $a = zip(array(1,2,3), a(66,77));
 * strval(lst($a)); /// '[[1,66],[2,77]]'
 *
 * @endtest
 *
 *
 */
function zip($xs) {
    return zipargs(func_get_args());
}

/**
 * The same as `zip`, but accepts an array of arguments.
 *
 * @param array $args
 * @return Iterator
 *
 */
function zipargs($args, $fill=null) {
    $its = array();
    foreach($args as $xs)
        $its []= iter($xs);
    $opts = array();
    if(func_num_args() > 1)
        $opts['fill'] = $fill;
    return new \std\_ZipIterator($its, $opts);
}

/**
 * Make an iterator that maps a function to iterators.
 *
 * @param Callable $func
 * @param Traversable $xs,...
 * @return Iterator
 *
 * @code
 *
 * $addThree = function ($x, $y, $z) { return $x + $y + $z; };
 *
 * $a = array(1,2,3);
 * $b = array(40,50,60);
 * $c = array(700,800,900);
 *
 * $z = map($addThree, $a, $b, $c);
 * print lst($z); /// [741,852,963]
 *
 * @endcode
 *
 */
function map($func, $xs) {
    return mapargs($func, array_slice(func_get_args(), 1));
}

/**
 * The same as `map`, but accepts an array of arguments.
 *
 * @param Callable $func
 * @param array $args
 * @return Iterator
 *
 */
function mapargs($func, $args, $fill=null) {
    $its = array();
    foreach($args as $xs)
        $its []= iter($xs);
    $opts = array('func' => $func);
    if(func_num_args() > 2)
        $opts['fill'] = $fill;
    return new \std\_ZipIterator($its, $opts);
}

/**
 * Make an iterator from elements of iterable for which function returns true.
 *
 * @param $func
 * @param Traversable $xs
 * @return Iterator
 *
 */
function filter($func, $xs) {
    return new \std\_FilterIterator($func, $xs);
}

/**
 * Return `true` if all items of the iterable evaluate to `true`.
 *
 * @param Traversable $xs
 * @return bool
 *
 * @code
 *
 * all(array(1,2,3)); /// true
 *
 * @endcode
 *
 */
function all($xs) {
    foreach(iter($xs) as $x)
        if(!$x)
            return false;
    return true;
}

/**
 * Return `true` if any item of the iterable evaluates to `true`.
 *
 * @param Traversable $xs
 * @return bool
 *
 * @code
 *
 * any(array(0,1,0)); /// true
 *
 * @endcode
 *
 */
function any($xs) {
    foreach(iter($xs) as $x)
        if($x)
            return true;
    return false;
}

/**
 * Return `true` if `$where` contains `$what`.
 *
 * @param $where
 * @param $what
 * @return bool
 *
 * @code
 *
 * in(array(1,2,3), 2);     /// true
 * in("abcdef",  "cd");     /// true
 * in(lst("abcdef"), "d");  /// true
 * in(d("a",1,"b",2), "a"); /// true
 *
 * @endcode
 *
 */
function in($where, $what) {
    switch(gettype($where)) {
        case 'string':
            return s($where)->contains($what);
        case 'array':
            foreach($where as $v) {
                if(eq($what, $v))
                    return true;
            }
            return false;
        case 'object':
            return method_exists($where, 'contains') ?
                $where->contains($what) : false;
    }
    return false;
}


/**
 * Return `true` if two values are equal.
 * Scalars are compared strictly, for objects, attempt to call `$x->equals($y)`,
 * if there's no such thing, compare loosely.
 * `eq` is used in methods like `list->find`.
 *
 * @param mixed $x
 * @param mixed $y
 * @return bool
 *
 * @code
 *
 * eq('1', '1');      /// true
 * eq('1', 1);        /// false
 *
 * @endcode
 *
 */
function eq($x, $y) {
    if(is_object($x)) {
        if(method_exists($x, 'equals')) {
            return $x->equals($y);
        }
        return $x == $y;
    }
    return $x === $y;
}


/**
 * Return `true` if the value has the type.
 *
 * @param mixed $x
 * @param string $type, ...
 * @return bool
 *
 * @code
 *
 * is('foo', 'string');      /// true
 * is(s('abc'), 'str');  /// true
 * is(d('a',1), 'dict'); /// true
 * is(123, 'string', 'int'); /// true
 *
 * @endcode
 *
 */
function is($x, $type) {
    foreach(array_slice(func_get_args(), 1) as $type) {
        if(\std\_is($x, strval($type))) {
            return true;
        }
    }
    return false;
}
}

/**
 * `std` namespace
 */
namespace std {
    
    function _getclass($name) {
        if(!isset($GLOBALS['__std__classes__'][$name]))
            return "\\std\\$name";
        return $GLOBALS['__std__classes__'][$name];
    }
    
    function _setclass($name, $cls) {
        $GLOBALS['__std__classes__'][$name] = $cls;
    }
    
    
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
    
    /**
     * Generic std exception.
     */
    class Error extends \Exception {
        function __construct($message='') {
            $c = get_class($this);
            $p = strpos($c, '\\');
            if($p !== false)
                $c = substr($c, $p + 1);
            parent::__construct($c . ': ' . strval($message));
            $this->args = func_get_args();
        }
    }
    
    class KeyError extends Error {}
    
    class IndexError extends Error {}
    
    class TypeError extends Error {}
    
    class ValueError extends Error {}
    
    class NotFoundError extends Error {}
    
    class NotImplemented extends Error {}
    
    class UnicodeError extends ValueError {}
    
    class StopIteration extends Error {}
    
    class NameError extends Error {}
    
    /**
     * Generic container type.
     *
     */
    class Container implements \ArrayAccess, \Countable, \IteratorAggregate
    {
        function offsetExists($index) {
            return false;
        }
    
        function offsetGet($index) {
            return null;
        }
    
        function offsetSet($index, $val) {
            throw new NotImplemented();
        }
    
        function offsetUnset($index) {
            throw new NotImplemented();
        }
    
        /**
         * Functional form in the index operator.
         * `$foo->get($bar)` is the same as `$foo[$bar]`.
         *
         * @param $index
         * @param null $default
         * @return mixed
         */
        function get($index, $default=null) {
            return isset($this[$index]) ? $this[$index] : $default;
        }
    
        function count() {
            return 0;
        }
    
        function getIterator() {
            throw new NotImplemented();
        }
    }
    
    /**
     * Generic sliceable sequence.
     */
    class Sequence extends Container
    {
        /**
         *
         * @nodoc
         *
         * @param mixed $index
         * @return mixed
         * @throws IndexError
         *
         * @test
         *
         * $a = a('a', 'b', 'c', 'd', 'e', 'f');
         * $a[0];    /// 'a'
         * $a['1'];  /// 'b'
         * $a[-1];   /// 'f'
         * $a[-2];   /// 'e'
         * $a['-3']; /// 'd'
         *
         * try {
         *    $a[100];
         * } catch(std\IndexError $e) {
         *    $e->args[0]; /// 100
         * }
         *
         * strval($a['0:4']);   /// '["a","b","c","d"]'
         * strval($a['1:4']);   /// '["b","c","d"]'
         * strval($a['1:-2']);  /// '["b","c","d"]'
         * strval($a['-4:-1']); /// '["c","d","e"]'
         *
         * strval($a['2:']);   /// '["c","d","e","f"]'
         * strval($a['-3:']);  /// '["d","e","f"]'
         *
         * strval($a[':2']);   /// '["a","b"]'
         * strval($a[':-3']);  /// '["a","b","c"]'
         *
         * strval($a['0:4:1']);   /// '["a","b","c","d"]'
         * strval($a['0:6:2']);   /// '["a","c","e"]'
         * strval($a['0:6:3']);   /// '["a","d"]'
         *
         * strval($a['4:0:-1']);   /// '["e","d","c","b"]'
         * strval($a['6:0:-2']);   /// '["f","d","b"]'
         * strval($a['6:0:-3']);   /// '["f","c"]'
         *
         * @endtest
         *
         */
        function offsetGet($index) {
            $len = $this->count();
            $idx = _parse_int($index);
            if(!is_null($idx)) {
                if(!is_null($idx = _parse_index($idx, $len)))
                    return $this->at($idx);
            } else if(!is_null($slice = _parse_slice($index, $len))) {
                return $this->_slice($slice[0], $slice[1], $slice[2]);
            }
            throw new IndexError($index);
        }
    
        /**
         * @nodoc
         *
         * @param mixed $index
         * @return bool
         *
         * @test
         *
         * $a = a('a', 'b', 'c', 'd', 'e', 'f');
         *
         * isset($a[0]); /// true
         * isset($a[6]); /// false
         *
         * isset($a[-1]); /// true
         * isset($a[-6]); /// true
         * isset($a[-7]); /// false
         *
         * @endtest
         *
         */
        function offsetExists($index) {
            $len = $this->count();
            $idx = _parse_int($index);
            return is_null($idx) ? !is_null(_parse_slice($index, $len)) : !is_null(_parse_index($idx, $len));
        }
    
        /**
         * Return the item at given index.
         *
         * @param $index
         * @return mixed
         *
         */
        function at($index) {
            throw new NotImplemented();
        }
    
        /**
         * Slice the sequence at given points.
         *
         * @param $start
         * @param null $stop
         * @param null $step
         * @return Sequence
         *
         */
        function slice($start, $stop=null, $step=null) {
            $slice = _parse_slice3($start, $stop, $step, count($this));
            if(is_null($slice)) {
                throw new IndexError($start, $stop, $step);
            }
            return $this->_slice($slice[0], $slice[1], $slice[2]);
        }
    
        function _slice($start, $stop, $step) {
            throw new NotImplemented();
        }
    }
    
    /**
     * Generic mutable sequence.
     */
    class MutableSequence extends Sequence
    {
        function offsetSet($index, $val) {
            return $this->set($index, $val);
        }
    
        function offsetUnset($index) {
            return $this->del($index);
        }
    
        /**
         * Set the value at the index or slice.
         *
         * @param $index
         * @param $val
         * @return mixed
         */
        function set($index, $val) {
            throw new NotImplemented();
        }
    
        /**
         * Delete the value at the index or slice.
         *
         * @param $index
         */
        function del($index) {
            throw new NotImplemented();
        }
    
    
    }
    
    function _strlist($xs) {
        $a = lst();
        foreach($xs as $x) {
            $a []= str($x);
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
    
    function _iter($xs) {
        switch(gettype($xs)) {
            case 'array':
                return new _ArrayIterator($xs);
            case 'string':
                return str($xs)->getIterator();
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
    
    
    class _FilterIterator implements \Iterator
    {
        function __construct($func, $iter) {
            $this->it = iter($iter);
            $this->func = $func;
            $this->n = 0;
            $this->val = null;
        }
    
        function _getval() {
            while($this->it->valid()) {
                $x = $this->it->current();
                if(call_user_func($this->func, $x)) {
                    $this->val = $x;
                    return true;
                }
                $this->it->next();
            }
            return false;
        }
    
        public function current() {
            return $this->val;
        }
    
        public function key() {
            return $this->n;
        }
    
        public function next() {
            $this->it->next();
            $this->n++;
        }
    
        public function rewind() {
            $this->it->rewind();
            $this->n = 0;
        }
    
        public function valid() {
            return $this->_getval();
        }
    }
    
    /**
     * @private
    
     * @test
     *
     * $it = new std\_ListIterator(array(11,22,33));
     * $a = array();
     * foreach($it as $x) $a []= $x;
     * implode(',', $a); /// '11,22,33'
     *
     * @endtest
     *
     */
    class _ListIterator implements \Iterator
    {
        protected $a;
        protected $len;
        protected $n;
    
        function __construct($x) {
            $this->a = $x;
            $this->n = 0;
            $this->len = count($this->a);
        }
        public function current() {
            return $this->a[$this->n];
        }
        public function key() {
            return $this->n;
        }
        public function next() {
            $this->n++;
        }
        public function rewind() {
            $this->n = 0;
        }
        public function valid() {
            return $this->n < $this->len;
        }
    }
    
    class _ArrayIterator extends _ListIterator {
        function __construct($x) {
            parent::__construct(_is_list($x) ? $x : array_values($x));
        }
    }
    
    class _ByteStringIterator extends _ListIterator
    {
        function __construct($x) {
            parent::__construct($x);
            $this->len = strlen($x);
        }
    }
    
    /**
     * @private
     *
     * @test
     *
     * $it = new std\_PairsIterator(array(array('a',11),array('b',22),array('c',33)));
     * $z = "";
     * foreach($it as $k=>$v) $z .= "$k($v)";
     * $z; /// 'a(11)b(22)c(33)'
     *
     * @endtest
     *
     */
    class _PairsIterator implements \Iterator
    {
        protected $it;
    
        function __construct($x) {
            $this->it = iter($x);
            $this->val = null;
        }
    
        public function current() {
            $this->val = $this->it->current();
            return $this->val[1];
        }
    
        public function key() {
            return $this->val[0];
        }
    
        public function next() {
            $this->it->next();
        }
    
        public function rewind() {
            $this->it->rewind();
        }
    
        public function valid() {
            return $this->it->valid();
        }
    }
    
    /**
     * @private
     *
     * @test
     *
     * $it = new std\_AssocIterator(array('a'=>11,'b'=>22,'c'=>33));
     * $z = "";
     * foreach($it as $k=>$v) $z .= "$k($v)";
     * $z; /// 'a(11)b(22)c(33)'
     *
     * @endtest
     *
     */
    class _AssocIterator implements \Iterator
    {
        protected $a;
        protected $len;
        protected $n;
    
        function __construct($x) {
            $this->a = $x;
            $this->keys = array_keys($x);
            $this->n = 0;
            $this->len = count($this->a);
        }
        public function current() {
            return $this->a[$this->keys[$this->n]];
        }
        public function key() {
            return $this->keys[$this->n];
        }
        public function next() {
            $this->n++;
        }
        public function rewind() {
            $this->n = 0;
        }
        public function valid() {
            return $this->n < $this->len;
        }
    }
    
    class _RangeIterator implements \Iterator
    {
        protected $start;
        protected $stop;
        protected $step;
        protected $n;
        protected $p;
    
        function __construct($start, $stop, $step) {
            $this->start = $start;
            $this->stop  = $stop;
            $this->step  = $step;
            $this->rewind();
        }
        public function current() {
            return $this->p;
        }
        public function key() {
            return $this->n;
        }
        public function next() {
            $this->p += $this->step;
        }
        public function rewind() {
            $this->n = 0;
            $this->p = $this->start;
        }
        public function valid() {
            return $this->step > 0 ? $this->p < $this->stop : $this->p > $this->stop;
        }
    }
    
    /**
     * Validate range bounds.
     * @private
     */
    function _parse_range($start, $stop, $step) {
    
        if(!is_null($start) && is_null($start = _parse_int($start))) return null;
        if(!is_null($stop)  && is_null($stop  = _parse_int($stop)))  return null;
        if(!is_null($step)  && is_null($step  = _parse_int($step)))  return null;
    
        if(is_null($start)) $start = 0;
        if(is_null($stop))  return null;
        if(is_null($step))  $step = 1;
        if($step === 0)     return null;
    
        if($step > 0 && $start <= $stop)
            $size = ($stop - $start - 1) / $step + 1;
        else if($step < 0 && $start >= $stop)
            $size = ($stop - $start + 1) / $step + 1;
        else
            return null;
    
        return array($start, $stop, $step, intval($size));
    }
    
    class _RepeatIterator implements \Iterator
    {
        protected $value;
        protected $count;
        protected $n;
    
        function __construct($value, $count=null) {
            $this->value = $value;
            $this->count = $count;
            $this->rewind();
        }
        public function current() {
            return $this->value;
        }
        public function key() {
            return $this->n;
        }
        public function next() {
            $this->n++;
        }
        public function rewind() {
            $this->n = 0;
        }
        public function valid() {
            return is_null($this->count) || $this->n < $this->count;
        }
    }
    
    class _ZipIterator implements \Iterator
    {
        function __construct($iters, $opts=null) {
            $this->iters = $iters;
            $this->opts = $opts ? $opts : null;
            $this->n = 0;
            $this->curr = null;
        }
        public function current() {
            if(!isset($this->opts['func']))
                return lst($this->curr);
            return call_user_func_array($this->opts['func'], $this->curr);
        }
        public function key() {
            return $this->n;
        }
        public function next() {
            $this->n++;
        }
        public function rewind() {
            foreach($this->iters as $it)
                $it->rewind();
            $this->n = 0;
        }
        public function valid() {
            $this->curr = array();
            $v = false;
            foreach($this->iters as $it) {
                if($it->valid()) {
                    $this->curr []= $it->current();
                    $it->next();
                    $v = true;
                } else if(isset($this->opts['fill'])) {
                    $this->curr []= $this->opts['fill'];
                } else
                    return false;
            }
            return $v;
        }
    }
    
    
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
    
    /**
     * The string class.
     *
     * Strings support negative indexes and slicing:
     *
     * @code
     *
     * $a= s('abcdef');
     * print $a[-2];     /// 'e'
     * print $a['1::3']; /// 'be'
     *
     * @endcode
     *
     * Strings are unicode-aware:
     *
     * @code
     *
     * $a= s('füßchen');
     * print count($a); /// '7'
     *
     * @endcode
     *
     *
     * @test
     *
     * $a= s('abcdef');
     * '-'.$a['1'];  /// '-b'
     * '-'.$a[-1];   /// '-f'
     *
     * try {
     *    $a[100];
     * } catch(std\IndexError $e) {
     *    $e->args[0]; /// 100
     * }
     *
     * $a= s('füchßchen');
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
            return str($this->_str[$index]);
        }
    
        function _slice($start, $stop, $step) {
            $s = _apply_slice($this->_str, $start, $stop, $step);
            return str(implode('', $s));
        }
    
        
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
         * $s= s('fuß');
         * print $s->replace('ß', 'ss'); /// 'fuss'
         *
         * $s= s('abcabcabc');
         * print $s->replace("bc", 'X', 2); /// 'aXaXabc'
         *
         * @endcode
         *
         */
        function replace($old, $new, $count=-1) {
            $old = str($old);
            $new = str($new);
            $s = preg_replace(_regex($old), strval($new), $this->_str, $count);
            return str($s);
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
         * $a= s('ab|cd|ef');
         * print $a->split('|'); /// ["ab","cd","ef"]
         *
         * $a= s("  ab   cd      ef  \n\n\r   gh   ");
         * print $a->split(); /// ["ab","cd","ef","gh"]
         *
         * @endcode
         *
         */
        function split($sep=null, $limit=-1) {
            if(is_null($sep)) {
                $r = preg_split('~\\s+~', $this->_str, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $sep = str($sep);
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
         * $a= s("
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
         * $s= s("füßchen");
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
         * s('foobar')->endswith('bar'); /// true
         * s('foobar')->endswith('a', 'r'); /// true
         *
         * @endcode
         *
         * @test
         *
         * s('abcd')->endswith('d'); /// true
         * s('abcd')->endswith('x'); /// false
         *
         * s('aböx')->endswith('öx'); /// true
         * s('aböx')->endswith('xx', 'öx'); /// true
        
         * @endtest
         *
         */
        function endswith($sub) {
            foreach(func_get_args() as $sub) {
                $s = str($sub);
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
         * s('abcd')->startswith('a'); /// true
         *
         * @endcode
         *
         *
         * @test
         *
         * s('abcd')->startswith('x'); /// false
         * s('abcd')->startswith('x', 'a'); /// true
        
         * @endtest
         *
         */
        function startswith($sub) {
            foreach(func_get_args() as $sub) {
                $s = str($sub);
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
         * $s= s('abüxabüyabüz');
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
         * print s(' abc ')->strip(); /// 'abc'
         * print s('~!abc@~')->strip('~!@'); /// 'abc'
         *
         * @endcode
         *
         * @test
         *
         * print s('~ßüxßaüxßbcßüxß~')->strip('~xüß'); /// 'aüxßbc'
        
         * @endtest
         *
         */
        function strip($chars=null) {
            if(is_null($chars))
                return str(trim($this->_str));
            $r = preg_quote(strval(str($chars)), '~');
            $r = "~^[$r]+|[$r]+$~u";
            return str(preg_replace($r, '', $this->_str));
        }
        
        
        /**
         * Split the string by `$sep` and return a list (before, sep, after).
         *
         * @param $sep
         * @return Lst
         *
         * @code
         *
         * print s('abcZZef')->partition('ZZ'); /// ["abc","ZZ","ef"]
         * print s('ßüåßüß')->partition('åß');  /// ["ßü","åß","üß"]
         *
         * @endcode
         *
         *
         */
        function partition($sep) {
            $sep = str($sep);
            $p = $this->find($sep);
            if(is_null($p))
                return a($this, str(''), str(''));
            return a(
                $this[":$p"],
                $sep,
                $this[($p + count($sep)) . ":"]);
        }
        
    
        
        /**
         * True if all characters in the string are alphanumeric.
         *
         * @return bool
         *
         * @code
         *
         * s('abc1')->isalnum(); /// true
         * s('füß2')->isalnum(); /// true
         * s('abc?')->isalnum(); /// false
         *
         * @endcode
         *
         */
        function isalnum() {
            return ctype_alnum($this->_str);
        }
        
        /**
         * True if all characters in the string are letters.
         *
         * @return bool
         *
         * @code
         *
         * s('fuß')->isalpha(); /// true
         *
         * @endcode
         *
         */
        function isalpha() {
            return ctype_alpha($this->_str);
        }
        
        /**
         * True if all characters in the string are digits.
         *
         * @return bool
         *
         * @code
         *
         * s('123')->isdigit(); /// true
         *
         * @endcode
         *
         */
        function isdigit() {
            return ctype_digit($this->_str);
        }
        
        /**
         * True if all characters in the string are spaces.
         *
         * @return bool
         *
         * @code
         *
         * s("\t\f\n")->isspace(); /// true
         *
         * @endcode
         *
         */
        function isspace() {
            return ctype_space($this->_str);
        }
        
        /**
         * True if all characters in the string are lower-case letters.
         *
         * @return bool
         *
         * @code
         *
         * s('fuß')->islower(); /// true
         *
         * @endcode
         *
         */
        function islower() {
            return ctype_lower($this->_str);
        }
        
        /**
         * True if all characters in the string are upper-case letters.
         *
         * @return bool
         *
         * @code
         *
         * s('FÜSS')->isupper(); /// true
         *
         * @endcode
         *
         */
        function isupper() {
            return ctype_upper($this->_str);
        }
        
        
        /**
         * Return the index of substring in the string.
         *
         * @param string $sub
         * @return int|null
         *
         * @code
         *
         * $s= s("abcdabcd");
         * $s->find("bcd"); /// 1
         * $s->find("xyz"); /// null
         *
         * @endcode
         *
         * @test
         *
         * $s= s("füßchen");
         * $s->find("c"); /// 3
         * $s->find("ßch"); /// 2
         *
         * $s= s("ü123");
         * $s->find("ü"); /// 0
        
         * @endtest
         *
         */
        function find($sub) {
            $p = strpos($this->_str, strval(str($sub)));
            if($p === false)
                return null;
            return $p;
        }
        
        /**
         * Capitalize the string.
         */
        function capitalize() {
            $s = ucfirst(strtolower($this->_str));
            return str($s);
        }
        
        /**
         * Convert the stirng to upper case.
         */
        function upper() {
            $s = strtoupper($this->_str);
            return str($s);
        }
        
        /**
         * Convert the stirng to lower case.
         */
        function lower() {
            $s = strtolower($this->_str);
            return str($s);
        }
        
        /**
         * Return a reversed copy of the stirng.
         *
         * @code
         *
         * print s('füße')->reverse(); /// 'eßüf'
         *
         * @endcode
         *
         *
         */
        function reverse() {
            $s = strrev($this->_str);
            return str($s);
        }
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
            return str($this->_chars[$index]);
        }
    
        function _slice($start, $stop, $step) {
            $s = _apply_slice($this->_chars, $start, $stop, $step);
            return str(implode('', $s));
        }
    
        
        /**
         * True if all characters in the string are alphanumeric.
         *
         * @return bool
         *
         * @code
         *
         * s('abc1')->isalnum(); /// true
         * s('füß2')->isalnum(); /// true
         * s('abc?')->isalnum(); /// false
         *
         * @endcode
         *
         */
        function isalnum() {
            return _utf8_type($this->_str, 'L,N');
        }
        
        /**
         * True if all characters in the string are letters.
         *
         * @return bool
         *
         * @code
         *
         * s('fuß')->isalpha(); /// true
         *
         * @endcode
         *
         */
        function isalpha() {
            return _utf8_type($this->_str, 'L');
        }
        
        /**
         * True if all characters in the string are digits.
         *
         * @return bool
         *
         * @code
         *
         * s('123')->isdigit(); /// true
         *
         * @endcode
         *
         */
        function isdigit() {
            return _utf8_type($this->_str, 'Nd');
        }
        
        /**
         * True if all characters in the string are spaces.
         *
         * @return bool
         *
         * @code
         *
         * s("\t\f\n")->isspace(); /// true
         *
         * @endcode
         *
         */
        function isspace() {
            return _utf8_type($this->_str, 'Zs');
        }
        
        /**
         * True if all characters in the string are lower-case letters.
         *
         * @return bool
         *
         * @code
         *
         * s('fuß')->islower(); /// true
         *
         * @endcode
         *
         */
        function islower() {
            return _utf8_type($this->_str, 'Ll');
        }
        
        /**
         * True if all characters in the string are upper-case letters.
         *
         * @return bool
         *
         * @code
         *
         * s('FÜSS')->isupper(); /// true
         *
         * @endcode
         *
         */
        function isupper() {
            return _utf8_type($this->_str, 'Lu');
        }
        
        
        /**
         * Return the index of substring in the string.
         *
         * @param string $sub
         * @return int|null
         *
         * @code
         *
         * $s= s("abcdabcd");
         * $s->find("bcd"); /// 1
         * $s->find("xyz"); /// null
         *
         * @endcode
         *
         * @test
         *
         * $s= s("füßchen");
         * $s->find("c"); /// 3
         * $s->find("ßch"); /// 2
         *
         * $s= s("ü123");
         * $s->find("ü"); /// 0
        
         * @endtest
         *
         */
        function find($sub) {
            $p = strpos($this->_str, strval(str($sub)));
            if($p === false)
                return null;
            $n = 0;
            while($p > 0) {
                $p -= strlen($this->_chars[$n++]);
            }
            return $p === 0 ? $n : null;
        }
        
        /**
         * Capitalize the string.
         */
        function capitalize() {
            $s = _utf8_upper($this->_chars[0]) . _utf8_lower(substr($this->_str, strlen($this->_chars[0])));
            return str($s);
        }
        
        /**
         * Convert the stirng to upper case.
         */
        function upper() {
            $s = _utf8_upper($this->_str);
            return str($s);
        }
        
        /**
         * Convert the stirng to lower case.
         */
        function lower() {
            $s = _utf8_lower($this->_str);
            return str($s);
        }
        
        /**
         * Return a reversed copy of the stirng.
         *
         * @code
         *
         * print s('füße')->reverse(); /// 'eßüf'
         *
         * @endcode
         *
         *
         */
        function reverse() {
            $s = implode('', array_reverse($this->_chars));
            return str($s);
        }
    
    }
    
    
    
    /**
     * The list class.
     *
     * Lists support slicing like in python:
     *
     * @code
     *
     * $xs = lst('0123456789');
     * print $xs['1::3']; /// ["1","4","7"]
     *
     * $xs['1::2'] = repeat('x', 5);
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
            return lst(_apply_slice($this->_items, $start, $stop, $step));
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
    
        
        /**
         * @nodoc
         *
         * @test
         *
         * $a = lst('abcdef');
         * $a[] = 'Z';
         * strval($a) /// '["a","b","c","d","e","f","Z"]'
         *
         * $a = lst('abcdef');
         * $a[3] = 'X';
         * strval($a) /// '["a","b","c","X","e","f"]'
         *
         * $a = lst('abcdef');
         * try {
         *    $a[100] = 'Y';
         * } catch(\std\IndexError $e) {
         *    $e->args[0]; /// 100
         * }
         *
         * $a = lst('abcdef');
         * $a['1:1'] = array(5,6,7);
         * strval($a); /// '["a",5,6,7,"b","c","d","e","f"]'
         *
         * $a = lst('abcdef');
         * $a['1:4'] = array(5,6,7);
         * strval($a); /// '["a",5,6,7,"e","f"]'
         *
         * $a = lst('abcdef');
         * $a['1:4'] = 'XY';
         * strval($a); /// '["a","X","Y","e","f"]'
         *
         * $a = lst('abcdef');
         * $a['1:6:2'] = '123';
         * strval($a); /// '["a","1","c","2","e","3"]'
         *
         * $a = lst('abcdef');
         * $a['-1:2:-1'] = '123';
         * strval($a); /// '["a","b","c","3","2","1"]'
         *
         * $a = lst('abcdef');
         * $a['-1:0:-2'] = '123';
         * strval($a); /// '["a","3","c","2","e","1"]'
         *
         * $a = lst('abcdef');
         * try {
         *    $a['-1:0:-2'] = '1234';
         * } catch(\std\ValueError $e) {
         *    1; /// 1
         * }
         *
         * @endtest
         *
         */
        function set($index, $val) {
            if(!strlen($index)) {
                $this->_items []= $val;
                return;
            }
        
            $len = count($this->_items);
        
            $idx = _parse_int($index);
            if(!is_null($idx)) {
                $e = _parse_index($idx, $len);
                if(is_null($e))
                    throw new IndexError($index);
                $this->_items[$e] = $val;
                return;
            }
        
            $e = _parse_slice($index, $len);
            if(is_null($e))
                throw new IndexError($index);
        
            list($start, $stop, $step, $size) = $e;
        
            if(_is_list($val)) {
                array_splice($this->_items, $start, $size, $val);
                return;
            }
        
            $it = iter($val);
        
            if($step === 1) {
                array_splice($this->_items, $start, $size, _read_iter($it));
                return;
            }
        
            $buf = _read_iter($it, $size);
            if(is_null($buf))
                throw new ValueError("iterable of size {$size} expected");
        
            if($step > 0) {
                for($i = $e[0], $j = 0; $i < $e[1]; $i += $step) {
                    $this->_items[$i] = $buf[$j++];
                }
            } else {
                for($i = $e[0], $j = 0; $i > $e[1]; $i += $step) {
                    $this->_items[$i] = $buf[$j++];
                }
            }
        }
        
        /**
         * @nodoc
         *
         * @test
         *
         * $a = lst('abcdef');
         * unset($a[1]);
         * strval($a); /// '["a","c","d","e","f"]'
         *
         * $a = lst('abcdef');
         * unset($a[-2]);
         * strval($a); /// '["a","b","c","d","f"]'
         *
         * $a = lst('abcdef');
         * try {
         *    unset($a[100]);
         * } catch(\std\IndexError $e) {
         *    $e->args[0]; /// 100
         * }
         *
         * $a = lst('abcdef');
         * unset($a['1:4']);
         * strval($a); /// '["a","e","f"]'
         *
         * $a = lst('abcdef');
         * unset($a['1:6:2']);
         * strval($a); /// '["a","c","e"]'
         *
         * $a = lst('abcdef');
         * unset($a['-1:2:-1']);
         * strval($a); /// '["a","b","c"]'
         *
         * $a = lst('abcdef');
         * unset($a['-1:0:-2']);
         * strval($a); /// '["a","c","e"]'
         *
         * @endtest
         */
        function del($index) {
            $len = count($this->_items);
        
            $idx = _parse_int($index);
            if(!is_null($idx)) {
                $e = _parse_index($idx, $len);
                if(is_null($e))
                    throw new IndexError($index);
                array_splice($this->_items, $e, 1);
                return;
            }
        
            $e = _parse_slice($index, $len);
            if(is_null($e))
                throw new IndexError($index);
        
            list($start, $stop, $step, $size) = $e;
        
            if($step === 1) {
                array_splice($this->_items, $start, $size);
                return;
            }
        
            if($step > 0) {
                for($i = $start; $i < $stop; $i += $step) {
                    unset($this->_items[$i]);
                }
            } else {
                for($i = $start; $i > $stop; $i += $step) {
                    unset($this->_items[$i]);
                }
            }
            $this->_items = array_values($this->_items);
            return $this;
        }
        
        /**
         * Append items to the list.
         *
         * @param mixed $x,...
         * @return $this
         *
         * @code
         *
         * print a('a', 'b', 'c')->append('d', 'e'); /// ["a","b","c","d","e"]
         *
         * @endcode
         *
         */
        function append($x) {
            foreach(func_get_args() as $x)
                $this->_items []= $x;
            return $this;
        }
        
        /**
         * The same as `append`.
         */
        function push($x) {
            foreach(func_get_args() as $x)
                $this->_items []= $x;
            return $this;
        }
        
        
        /**
         * Append each item of the iterable.
         *
         * @param Traversable $xs,...
         * @return $this
         *
         * @code
         *
         * print a(1,2,3)->extend(array(4,5), 'ab'); /// [1,2,3,4,5,"a","b"]
         *
         * @endcode
         *
         */
        function extend($xs) {
            foreach(func_get_args() as $xs) {
                foreach(iter($xs) as $x)
                    $this->_items []= $x;
            }
            return $this;
        }
        
        /**
         * Remove an item and return it.
         *
         * @param mixed $index
         * @return mixed
         *
         * @test
         *
         * $a = lst('abc');
         * $x = $a->pop();
         * print $a; /// ["a","b"]
         * print $x; /// 'c'
         *
         * $a = lst('abc');
         * $x = $a->pop(1);
         * strval($a); /// '["a","c"]'
         * strval($x); /// 'b'
         *
         * $a = lst('abc');
         * $x = $a->pop(-2);
         * strval($a); /// '["a","c"]'
         * strval($x); /// 'b'
         *
         * @endtest
         */
        function pop($index=-1) {
            $x = $this[$index];
            unset($this[$index]);
            return $x;
        }
        
        /**
         * Insert an item.
         *
         * @param mixed $index
         * @param mixed $val
         * @return $this
         *
         * @code
         *
         * $a = lst('abc');
         * $a->insert(0, 'x');
         * print $a->join(); /// xabc
         * $a->insert(2, 'y');
         * print $a->join(); /// xaybc
         *
         * @endcode
         *
         */
        function insert($index, $val) {
            array_splice($this->_items, $index, 0, array($val));
            return $this;
        }
        
        
        
        
        /**
         * Return true if the list contains the value.
         *
         * @param mixed $x
         * @return bool
         *
         * @code
         *
         * lst('abc')->contains('a'); /// true
         * lst('abc')->contains('X'); /// false
         *
         * @endcode
         */
        function contains($x) {
            return !is_null($this->find($x));
        }
        
        /**
         * Find a value in the list and return its index, or `null`.
         *
         * @param $x
         * @return int|null
         *
         * @code
         *
         * lst('abcd')->find('c'); /// 2
         * lst('abcd')->find('x'); /// null
         *
         * @endcode
         *
         */
        function find($x) {
            foreach($this->_items as $i => $v)
                if(eq($v, $x))
                    return $i;
            return null;
        }
        
        /**
         * Remove the given value from the list.
         *
         * @param mixed $x
         * @return $this
         *
         * @code
         *
         * $a = lst('aXbXcXd');
         * $a->remove('X');
         * print $a; /// ["a","b","c","d"]
         *
         * $a = lst('aXbXcXd');
         * $a->remove('X', 2);
         * print $a; /// ["a","b","c","X","d"]
         *
         * @endcode
         *
         */
        function remove($x, $count=-1) {
            $b = array();
            foreach($this->_items as $v) {
                if(eq($v, $x) && $count) {
                    $count--;
                } else {
                    $b []= $v;
                }
            }
            $this->_items = $b;
            return $this;
        }
        
        /**
         * Sort the list in place.
         * Use the `key` function to extract the comparison key from list items.
         *
         * @param callable|null $key
         * @return $this
         *
         * @test
         *
         * $a = a(1, 200, 21);
         * $a->sort();
         * strval($a); /// '[1,21,200]'
         *
         * $a = a("1", "200", "21");
         * $a->sort();
         * strval($a); /// '["1","200","21"]'
         *
         * $a = a(21, 200, 1);
         * $a->sort('strval');
         * strval($a); /// '[1,200,21]'
         *
         * $a = a(1, 200, 21);
         * $a->sort(function($x) { return -$x; });
         * strval($a); /// '[200,21,1]'
         *
         * $a = a("Apple9", "orange", "apple");
         * $a->sort();
         * strval($a); /// '["Apple9","apple","orange"]'
         *
         * $a = a("Apple9", "orange", "apple");
         * $a->sort('strtoupper');
         * strval($a); /// '["apple","Apple9","orange"]'
         *
         * $a = a(
         *      ['code' => '300', 'serial' => 999, 'age' =>  21],
         *      ['code' => '32',  'serial' => 888, 'age' =>  20],
         *      ['code' => '300', 'serial' => 777, 'age' => 200],
         *      ['code' => '31',  'serial' => 777, 'age' =>   5]
         * );
         * $a->sort(function($x) { return [$x['code'], $x['age']]; });
         * $a[0]['code'].$a[0]['age']; /// "30021"
         * $a[1]['code'].$a[1]['age']; /// "300200"
         * $a[2]['code'].$a[2]['age']; /// "315"
         * $a[3]['code'].$a[3]['age']; /// "3220"
         *
         * @endcode
         *
         */
        function sort($key=null) {
            $keys = array();
            foreach($this->_items as $v)
                $keys []= $key ? call_user_func($key, $v) : $v;
            _cvt_sort_keys($keys);
            foreach($keys as $i => $k)
                $keys[$i] = array($k, $i);
            sort($keys);
            $b = array();
            $i = 0;
            foreach($keys as $k)
                $b[$i++] = $this->_items[$k[1]];
            $this->_items = $b;
            return $this;
        }
        /**
         * Reverse the list.
         *
         * @return $this
         *
         * @test
         *
         * $a = lst("abc");
         * $a->reverse();
         * strval($a); /// '["c","b","a"]'
         *
         * @endtest
         *
         */
        function reverse() {
            $this->_items = array_reverse($this->_items);
            return $this;
        }
        
        /**
         * Join elements with a string.
         *
         * @param string $sep
         * @return String
         *
         */
        function join($sep='') {
            return str(implode(str($sep), array_map('strval', $this->_items)));
        }
        
        /**
         * Call a function of every element and return a new list.
         *
         * @param callable $func
         * @return Lst
         *
         * @code
         *
         * $a = a('aa', 'bb', 'cc');
         * print $a->map('strtoupper'); /// '["AA","BB","CC"]'
         *
         * @endcode
         *
         */
        function map($func) {
            $a = array();
            $args = array_slice(func_get_args(), 0);
            foreach($this->_items as $x) {
                $args[0] = $x;
                $a []= call_user_func_array($func, $args);
            }
            return lst($a);
        }
        
        /**
         * Filter the list.
         *
         * @param $func
         * @return Lst
         *
         * @code
         *
         * $a = a(1,2,3,4,5,6);
         * $even = function($x) { return $x % 2 === 0; } ;
         * print $a->filter($even); /// [2,4,6]
         *
         * @endcode
         *
         */
        function filter($func) {
            $a = array();
            $args = array_slice(func_get_args(), 0);
            foreach($this->_items as $x) {
                $args[0] = $x;
                $t = call_user_func_array($func, $args);
                if($t)
                    $a []= $x;
            }
            return lst($a);
        }
        
        /**
         * Reduce the list.
         *
         * @param callable $func
         * @param null $init
         * @return mixed
         *
         * @code
         *
         * $a = a(1,2,3,4,5);
         * $add = function($x, $y) { return $x + $y; };
         * print $a->reduce($add); /// 15
         *
         * $a = lst('abcd');
         * $add = function($x, $y) { return "$x|$y"; };
         * print $a->reduce($add, 'x'); /// x|a|b|c|d
         *
         * @endcode
         *
         */
        function reduce($func, $val=null) {
            $k = 0;
            $len = count($this->_items);
            if(func_num_args() < 2) {
                $val = $this->_items[$k++];
            }
            while($k < $len) {
                $val = call_user_func($func, $val, $this->_items[$k++]);
            }
            return $val;
        }
    }
    
    /**
     * Dictionary (associative array) class.
     *
     * dict keys can be scalars (always converted to string):
     *
     * @code
     *
     * $d = dict();
     * $d["123"] = 'foo';
     * $d['xyz'] = 'bar';
     * $d[123]   = 'new';
     * print $d->values(); /// ["new","bar"]
     *
     * @endcode
     *
     * or objects that have the `hash` method:
     *
     * @code
     *
     * class X {
     *      function __construct($x) {
     *          $this->x = $x;
     *      }
     *      function hash() {
     *          return md5($this->x);
     *      }
     * }
     *
     * $a = new X(11);
     * $b = new X(22);
     *
     * $d = d($a, 'A', $b, 'B');
     * print $d->values(); /// ["A","B"]
     *
     * $d[new X(22)] = 'new';
     * print $d->values(); /// ["A","new"]
     *
     * @endcode
     *
     */
    class Dict extends Container implements \JsonSerializable
    {
        protected $_a; // this is array(hash=>array(key,value))
    
        function _hash($x) {
            if(is_scalar($x))
                return strval($x);
            if(is_object($x) && method_exists($x, 'hash'))
                return $x->hash();
            throw new TypeError('unhashable type', gettype($x));
        }
    
        function __construct($xs=null) {
            $this->_a = array();
            if($xs)
                $this->update($xs);
        }
    
        function count() {
            return count($this->_a);
        }
    
        /**
         * @nodoc
         *
         * @test
         *
         * $d= d(1,11,2,22,3,33);
         * $z = '';
         * foreach($d as $k => $v) $z .= "$k($v)";
         * print $z; /// '1(11)2(22)3(33)'
         *
         * @endtest
         *
         */
        function getIterator() {
            return new _PairsIterator(array_values($this->_a));
        }
    
        /**
         * @nodoc
         *
         * @test
         *
         * $a= d('a', 1, 'b', 2, 'c', '3');
         *
         * isset($a['a']); /// true
         * isset($a['x']); /// false
         *
         * @endtest
         *
         */
        function offsetExists($key) {
            $h = $this->_hash($key);
            return isset($this->_a[$h]);
        }
    
        /**
         * @nodoc
         *
         * @test
         *
         * $a= d('a', 1, 'b', 2, 'c', '3');
         * $a['a'];    /// 1
         *
         * try {
         *      $a['x'];
         * } catch(\std\KeyError $e) {
         *      $e->getMessage(); /// 'KeyError: x'
         * }
         *
         * @endtest
         *
         */
        function offsetGet($key) {
            $h = $this->_hash($key);
            if(!isset($this->_a[$h])) {
                throw new KeyError($key);
            }
            return $this->_a[$h][1];
        }
    
        /**
         * @nodoc
         *
         * @test
         *
         * $a= d('a', 1, 'b', 2, 'c', '3');
         * $a['a'] = 'Z';
         * strval($a) /// '{"a":"Z","b":2,"c":"3"}'
         *
         * $a= d('a', 1, 'b', 2, 'c', '3');
         * $a['x'] = 'Z';
         * strval($a) /// '{"a":1,"b":2,"c":"3","x":"Z"}'
         *
         * @endtest
         *
         */
        function offsetSet($key, $val) {
            $h = $this->_hash($key);
            $this->_a[$h] = array($key, $val);
            return $val;
        }
    
        /**
         * @nodoc
         *
         * @test
         *
         * $a= d('a', 1, 'b', 2, 'c', '3');
         * unset($a['a']);
         * strval($a); /// '{"b":2,"c":"3"}'
         *
         * @endtest
         */
        function offsetUnset($key) {
            $h = $this->_hash($key);
            unset($this->_a[$h]);
        }
    
        function __toString() {
            return json_encode($this->jsonSerialize());
        }
    
        function jsonSerialize() {
            $a = array();
            foreach($this->_a as $kv)
                $a[$kv[0]]= $kv[1];
            return $a;
        }
    
        /**
         * Return `true` if the dict has a `key`.
         *
         * @param mixed $key
         * @return bool
         */
        function contains($key) {
            return isset($this[$key]);
        }
    
        /**
         * Return the list of key-value pairs.
         *
         * @return Lst
         *
         * @code
         *
         * $d= d('a', 1, 'b', 2, 'c', 3);
         * $z = '';
         * foreach($d->items() as $x) $z .= " $x[0]=$x[1] ";
         * print $z; /// ' a=1  b=2  c=3 '
         *
         * @endcode
         *
         */
        function items() {
            $a = array();
            foreach($this->_a as $kv)
                $a []= lst($kv);
            return lst($a);
        }
    
        /**
         * Return the list of dictionary keys.
         *
         * @return Lst
         *
         * @code
         *
         * $d= d('a', 1, 'b', 2, 'c', 3);
         * print $d->keys(); /// ["a","b","c"]
         *
         * @endcode
         *
         */
        function keys() {
            $a = array();
            foreach($this->_a as $kv)
                $a []= $kv[0];
            return lst($a);
        }
    
        /**
         * Return the list of dictionary values.
         *
         * @return Lst
         *
         * @code
         *
         * $d= d('a', 1, 'b', 2, 'c', 3);
         * print $d->values(); /// [1,2,3]
         *
         * @endcode
         *
         */
        function values() {
            $a = array();
            foreach($this->_a as $kv)
                $a []= $kv[1];
            return lst($a);
        }
    
        /**
         * Remove the key and return its value. If the key is not in the dict, return `default`.
         *
         * @param mixed $key
         * @param mixed $default
         * @return mixed
         * @throws KeyError
         *
         *
         * @code
         *
         * $d= d('a', 1, 'b', 2, 'c', '3');
         * print $d->pop('a'); /// '1'
         * print $d->pop('zzz', 'oops'); /// 'oops'
         *
         * @endcode
         *
         */
        function pop($key, $default=null) {
            if(isset($this[$key])) {
                $val = $this[$key];
                unset($this[$key]);
                return $val;
            }
            if(func_num_args() === 1)
                throw new KeyError($key);
            return $default;
        }
    
    
        /**
         * If the key is in the dict, return its value, otherwise insert a new key/value.
         *
         * @param mixed $key
         * @param mixed $value
         * @return mixed
         *
         * @code
         *
         * $d= d('a', 1, 'b', 2, 'c', '3');
         * print $d->setdefault('a', 5); /// '1'
         * print $d->setdefault('x', 6); /// '6'
         * print $d; /// '{"a":1,"b":2,"c":"3","x":6}'
         *
         * @endcode
         *
         */
        function setdefault($key, $value) {
            if(!isset($this[$key]))
                $this[$key] = $value;
            return $this[$key];
        }
    
        /**
         * Update the dict from the key/value pairs from `other`.
         *
         * @param Traversable $other
         * @return $this
         *
         * @code
         *
         * $a= d('a', 1);
         * print $a->update('ABC'); /// '{"a":1,"0":"A","1":"B","2":"C"}'
         *
         * $a= d('a', 1);
         * print $a->update(array('x'=>11, 'y'=>22)); /// '{"a":1,"x":11,"y":22}'
         *
         * @endcode
         *
         *
         */
        function update($other) {
            if(is_array($other))
                $other = new _AssocIterator($other);
            foreach(iter($other) as $k => $v)
                $this[$k] = $v;
            return $this;
        }
    
        /**
         * Find a value in the dict and return its key (or `null`).
         *
         * @param $val
         * @return null|mixed
         *
         * @code
         *
         * $d= d('a', 1, 'b', 2, 'c', '3');
         * print $d->find(2); /// 'b'
         * is_null($d->find(9)); /// true
         *
         * @endcode
         *
         */
        function find($val) {
            foreach($this->_a as $kv) {
                if(eq($val, $kv[1]))
                    return $kv[0];
            }
            return null;
        }
    
        /**
         * Apply a function to dictionary values and return a new dict.
         *
         * @param callable $func
         * @param mixed $args, ...
         * @return Dict
         *
         * @code
         *
         * $d= d('a', 'x', 'b', 'y', 'c', 'z');
         * $e = $d->map('strtoupper');
         * print $e; /// '{"a":"X","b":"Y","c":"Z"}'
         *
         * $add = function ($x, $y) { return $x + $y; };
         *
         * $d= d('a', 1, 'b', 3, 'c', 5);
         * $e = $d->map($add, 5);
         * print $e; /// '{"a":6,"b":8,"c":10}'
         *
         * @endcode
         *
         */
        function map($func) {
            $a = array();
            $args = array_slice(func_get_args(), 0);
            foreach($this->_a as $kv) {
                $args[0] = $kv[1];
                $a []= array($kv[0], call_user_func_array($func, $args));
            }
            return pairdict($a);
        }
    
    
        /**
         * Apply a filter to dictionary values and return a new dict.
         *
         * @param callable $func
         * @param mixed $args, ...
         * @return Dict
         *
         * @code
         *
         * $less = function ($x, $y) { return $x < $y; };
         *
         * $d= d('a', 1, 'b', 3, 'c', 5);
         * $e = $d->filter($less, 5);
         * print $e; /// '{"a":1,"b":3}'
         *
         * @endcode
         *
         */
        function filter($func) {
            $a = array();
            $args = array_slice(func_get_args(), 0);
            foreach($this->_a as $kv) {
                $args[0] = $kv[1];
                $t = call_user_func_array($func, $args);
                if($t)
                    $a []= array($kv[0], $kv[1]);
            }
            return pairdict($a);
        }
    }
    
    /**
     * The set class.
     *
     */
    class Set extends Container
    {
        protected $_dct;
    
        function __construct($xs=null) {
            $this->_dct = dict();
            if($xs)
                $this->update($xs);
        }
    
        function count() {
            return count($this->_dct);
        }
    
        function getIterator() {
            return new _ListIterator($this->_dct->keys());
        }
    
        function offsetExists($key) {
            return isset($this->_dct[$key]);
        }
    
        function offsetGet($key) {
            return isset($this->_dct[$key]);
        }
    
        function offsetUnset($key) {
            unset($this->_dct[$key]);
        }
    
        function offsetSet($key, $val) {
            if(!strlen($key)) {
                $this->_dct[$val] = true;
                return $val;
            }
            throw new NotImplemented();
        }
    
        function __toString() {
            return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE);
        }
    
        function jsonSerialize() {
            return $this->_dct->keys()->jsonSerialize();
        }
    
        /**
         * Return true if `$x` is in the set.
         *
         * @param mixed $x
         * @return bool
         *
         * @code
         *
         * $s= set([1,2,3]);
         * $s->contains(1); /// true
         * $s->contains(9); /// false
         *
         * @endcode
         *
         */
        function contains($x) {
            return $this->_dct->contains($x);
        }
    
        /**
         * Add an element to the set.
         *
         * @param mixed $x
         * @return $this
         *
         * @code
         *
         * $s= set([1,2,3]);
         * $s->add(9);
         * $s->add(1);
         * print $s; /// [1,2,3,9]
         *
         * @endcode
         *
         */
        function add($x) {
            $this->_dct[$x] = true;
            return $this;
        }
    
        /**
         * Remove an element from the set.
         *
         * @param mixed $x
         * @return $this
         *
         * @code
         *
         * $s= set([1,2,3]);
         * $s->remove(1);
         * $s->remove(1);
         * print $s; /// [2,3]
         *
         * @endcode
         *
         */
        function remove($x) {
            unset($this->_dct[$x]);
            return $this;
        }
    
        /**
         * Add all elements from the iterable to the set.
         *
         * @param Traversable $xs
         * @return $this
         *
         * @code
         *
         * $s= set([1,2,3]);
         * $s->update(array(1,3,5,7));
         * print $s; /// [1,2,3,5,7]
         *
         * @endcode
         *
         */
        function update($xs) {
            foreach(iter($xs) as $x)
                $this->_dct[$x] = true;
            return $this;
        }
    
    
        /**
         * Compute the union of this set and all others.
         *
         * @param Traversable $other,...
         * @return Set
         *
         * @code
         *
         * $a= set([1,2,3]);
         * $b= set([4,5,6]);
         * $c = array(7,8);
         * $d = 'abc';
         * $u = $a->union($b, $c, $d);
         * print $u;  /// [1,2,3,4,5,6,7,8,"a","b","c"]
         *
         * @endcode
         *
         *
         */
        function union($other) {
            $a= set($this->_dct->keys());
            foreach(func_get_args() as $x) {
                $a->update($x);
            }
            return $a;
    
        }
    
        /**
         * Compute the intersection of this set and all others.
         *
         * @param Traversable $other,...
         * @return Set
         *
         * @code
         *
         * $s= set([1,2,3]);
         * $b= set([2,4,3]);
         * $c = array(3,2,1);
         *
         * $u = $a->intersection($b, $c);
         * print $u; /// [2,3]
         *
         * @endcode
         *
         */
        function intersection($other) {
            $cc = dict();
            foreach($this->_dct as $k => $v)
                $cc[$k] = 1;
            foreach(func_get_args() as $xs) {
                foreach(iter($xs) as $x)
                    if(isset($cc[$x]))
                        $cc[$x] += 1;
            }
            $a = array();
            $n = func_num_args() + 1;
            foreach($cc as $k => $c)
                if($c === $n)
                    $a []= $k;
            return set($a);
        }
    
        /**
         * Compute the difference of this set and all others.
         *
         * @param Traversable $other,...
         * @return Set
         *
         * @code
         *
         * $a= set([1,2,3,4,5]);
         * $b= set([2,4,6]);
         * $b= set([2,4,6]);
         * $c = array(3,7);
         * $u = $a->difference($b, $c);
         * print $u; /// [1,5]
         *
         * @endcode
         *
         */
        function difference($other) {
            $a = dict();
            foreach($this->_dct as $k => $v)
                $a[$k] = 1;
            foreach(func_get_args() as $x) {
                foreach(iter($x) as $v)
                    unset($a[$v]);
            }
            return set($a->keys());
        }
    
        /**
         * Return `true` if the set has no common elements with `$other`.
         *
         * @param Traversable $other
         * @return bool
         *
         * @code
         *
         * set('abc')->isdisjoint(set('def')); /// true
         * set('abc')->isdisjoint(set('dea')); /// false
         *
         * @endcode
         *
         */
        function isdisjoint($other) {
            foreach(set($other) as $e)
                if(isset($this[$e]))
                    return false;
            return true;
        }
    
        /**
         * Return `true` if the set is a subset of `$other`.
         *
         * @param Traversable $other
         * @return bool
         *
         * @code
         *
         * set('abc')->issubset(set('abcd')); /// true
         * set('abc')->issubset(set('abc'));  /// true
         * set('abc')->issubset(set('aXc'));  /// false
         *
         * @endcode
         *
         */
        function issubset($other) {
            $other= set($other);
            foreach($this->_dct as $e => $_)
                if(!isset($other[$e]))
                    return false;
            return true;
        }
        /**
         * Return `true` if the set is a superset of `$other`.
         *
         * @param Traversable $other
         * @return bool
         *
         * @code
         *
         * set('abc')->issuperset(set('ab')); /// true
         * set('abc')->issuperset(set('aX')); /// false
         *
         * @endcode
         *
         */
        function issuperset($other) {
            return set($other)->issubset($this);
        }
    
    
    }
    
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
         * print re('\W+')->split('aa...bb...cc'); /// ["aa","bb","cc"]
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
         * re('[a-z]')->test('123ab'); /// true
         * re('[a-z]')->test('123XX'); /// false
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
         * $m = re('([a-z]+)(\d)')->match('..foo5..');
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
         * $m = re('([a-z]+)(\d)')->find('..foo5..');
         * print $m; /// ["foo5","foo","5"]
         *
         * @endcode
         */
        function find($text) {
            $r = preg_match($this->_re, strval($text), $ms);
            if(!$r) {
                return lst();
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
         * $ms = re($pat)->matchall('...ab12--cd34...');
         * print $ms[0]->groups(); /// ["ab12","ab","12"]
         * print $ms[1]->groups(); /// ["cd34","cd","34"]
         *
         * @endcode
         *
         */
        function matchall($text) {
            $r = preg_match_all($this->_re, strval($text), $ms, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
            if(!$r) {
                return lst();
            }
            $a = lst();
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
         * $ms = re('([a-z]+)(\d+)')->findall('ab12--cd34');
         * print $ms; /// '[["ab","12"],["cd","34"]]'
         *
         * $ms = re('[a-z]+')->findall('ab12--cd34');
         * print $ms; /// '["ab","cd"]'
         *
         * $ms = re('[a-z]+(\d+)')->findall('ab12--cd34');
         * print $ms; /// '["12","34"]'
         *
         * @endcode
         *
         */
        function findall($text) {
            $r = preg_match_all($this->_re, strval($text), $ms, PREG_SET_ORDER);
            if(!$r) {
                return lst();
            }
            $a = lst();
            foreach($ms as $m) {
                $a []= (count($m) <= 2) ? str(end($m)) : _strlist(array_slice($m, 1));
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
         * print re('\W+')->sub('*', 'a,b,c'); /// 'a*b*c'
         * print re('\W+')->sub('*', 'a,b,c', 1); /// 'a*b,c'
         *
         * @endcode
         *
         */
        function sub($repl, $text, $count=-1) {
            $text = preg_replace($this->_re, strval($repl), strval($text), $count);
            return str($text);
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
         * print re('\w+')->subf('strtoupper', 'a,b,c'); /// 'A,B,C'
         *
         * $swap = function($match, $a, $b) { return $b . $a; };
         * $str  = '..ab12..cd45..';
         *
         * print re('([a-z]+)(\d+)')->subf($swap, $str); /// '..12ab..45cd..'
         *
         *
         * @endcode
         *
         */
        function subf($func, $text, $count=-1) {
            return str(preg_replace_callback($this->_re, function($ms) use($func) {
                $args = array();
                foreach($ms as $m)
                    $args []= str($m);
                return call_user_func_array($func, $args);
            }, strval($text), $count));
        }
    }
    
    /**
     * Regexp match object.
     *
     * @test
     *
     * $pat = '([a-z]+)(?P<digits>\d+)([A-Z]+)';
     * $str = '...abc123XYZ...';
     *
     * $m = re($pat)->match($str);
     *
     * strval($m[0]); /// 'abc123XYZ'
     * strval($m[2]); /// '123'
     * strval($m['digits']); /// '123'
     *
     * strval($m->group()); /// 'abc123XYZ'
     * strval($m->group('digits')); /// '123'
     * strval($m->group(2)); /// '123'
     *
     * $m->start();  /// 3
     * $m->start(2); /// 6
     *
     * $m->end();  /// 12
     * $m->end(1); /// 6
     *
     * @endtest
     *
     *
     */
    class ReMatchObject extends Container
    {
        protected $_groups;
        protected $_groupdict;
        protected $_start;
        protected $_end;
    
        function __construct($matches) {
            $this->_groups = lst();
            $this->_start = dict();
            $this->_end = dict();
    
            foreach($matches as $idx => $grp) {
                $val = str($grp[0]);
                $this->_start[$idx] = $grp[1];
                $this->_end[$idx] = $this->_start[$idx] + count($val);
    
                if(is_int($idx)) {
                    $this->_groups []= $val;
                } else {
                    $this->_groupdict[$idx] = $val;
                }
            }
        }
    
        function offsetExists($key) {
            return isset($this->_groups[$key]) || isset($this->_groupdict[$key]);
        }
    
        function offsetGet($key) {
            return is_int($key) ? $this->_groups[$key] : $this->_groupdict[$key];
        }
    
        function count() {
            return count($this->_groups);
        }
    
        /**
         * Return the list of matched groups.
         *
         * @return Lst
         */
        function groups() {
            return $this->_groups;
        }
    
        /**
         * Return the dict of matched named groups.
         *
         * @return Dict
         */
        function groupdict() {
            return $this->_groupdict;
        }
    
        /**
         * Return the group by index or name.
         *
         * @param mixed $key
         * @return string
         *
         */
        function group($key=0) {
            return $this[$key];
        }
    
        /**
         * Return the group start position by index or name.
         *
         * @param mixed $key
         * @return int
         *
         */
        function start($key=0) {
            return $this->_start[$key];
        }
    
        /**
         * Return the group end position by index or name.
         *
         * @param mixed $key
         * @return int
         *
         */
        function end($key=0) {
            return $this->_end[$key];
        }
    
    }
    

}

?>