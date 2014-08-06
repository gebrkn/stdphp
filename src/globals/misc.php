<?php

/**
 * Return `true` if all items of the iterable evaluate to `true`.
 *
 * @param Traversable $xs
 * @return bool
 *
 * @code
 *
 * STD\all(array(1,2,3)); /// true
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
 * STD\any(array(0,1,0)); /// true
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
 * STD\in(array(1,2,3), 2);     /// true
 * STD\in("abcdef",  "cd");     /// true
 * STD\in(STD\lst("abcdef"), "d");  /// true
 * STD\in(STD\d("a",1,"b",2), "a"); /// true
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
                if(STD\eq($what, $v))
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
 * STD\eq('1', '1');      /// true
 * STD\eq('1', 1);        /// false
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
 * STD\is('foo', 'string');      /// true
 * STD\is(STD\s('abc'), 'str');  /// true
 * STD\is(STD\d('a',1), 'dict'); /// true
 * STD\is(123, 'string', 'int'); /// true
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
