<?php

/**
 * Convert the argument to Iterator.
 *
 * @param $xs
 * @return Iterator
 *
 * @test
 *
 * $z = "";
 * foreach(STD\iter(array(1,2,3)) as $x) $z.="($x)";
 * $z; /// '(1)(2)(3)'
 *
 * $z = "";
 * foreach(STD\iter(array(8=>1,7=>2,6=>3)) as $x) $z.="($x)";
 * $z; /// '(1)(2)(3)'
 *
 * $z = "";
 * foreach(STD\iter("wüöl") as $x) $z.="($x)";
 * $z; /// '(w)(ü)(ö)(l)'
 *
 * $z = "";
 * $p = (object) array('a'=>11,'b'=>22,'c'=>33);
 * foreach(STD\iter($p) as $k => $v) $z .= "$k($v)";
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
 * print STD\lst(STD\xrange(5));     /// [0,1,2,3,4]
 * print STD\lst(STD\xrange(2,5));   /// [2,3,4]
 * print STD\lst(STD\xrange(2,8,2)); /// [2,4,6]
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
 * foreach(STD\pairs($q) as $k => $v)
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
 * print STD\lst(STD\repeat('a',5)); /// ["a","a","a","a","a"]
 * print STD\lst(STD\zip([1,2,3], STD\repeat(0))); /// [[1,0],[2,0],[3,0]]
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
 * $z = STD\zip('abc', 'XYZ');
 * print STD\lst($z); /// [["a","X"],["b","Y"],["c","Z"]]
 *
 * @endcode
 *
 *
 * @test
 *
 * $a = STD\zip(array(1,2,3), STD\d('x',55,'y',66,'z',77), "fuß");
 * strval(STD\lst($a)); /// '[[1,55,"f"],[2,66,"u"],[3,77,"ß"]]'
 *
 * $a = STD\zip(array(1,2,3), STD\a(66,77));
 * strval(STD\lst($a)); /// '[[1,66],[2,77]]'
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
 * $z = STD\map($addThree, $a, $b, $c);
 * print STD\lst($z); /// [741,852,963]
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
