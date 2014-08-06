<?php

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
 * $add = STD\operator('+');
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