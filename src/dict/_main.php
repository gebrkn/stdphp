<?php

/**
 * Dictionary (associative array) class.
 *
 * dict keys can be scalars (always converted to string):
 *
 * @code
 *
 * $d = STD\dict();
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
 * $d = STD\d($a, 'A', $b, 'B');
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
     * $d= STD\d(1,11,2,22,3,33);
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
     * $a= STD\d('a', 1, 'b', 2, 'c', '3');
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
     * $a= STD\d('a', 1, 'b', 2, 'c', '3');
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
     * $a= STD\d('a', 1, 'b', 2, 'c', '3');
     * $a['a'] = 'Z';
     * strval($a) /// '{"a":"Z","b":2,"c":"3"}'
     *
     * $a= STD\d('a', 1, 'b', 2, 'c', '3');
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
     * $a= STD\d('a', 1, 'b', 2, 'c', '3');
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
     * $d= STD\d('a', 1, 'b', 2, 'c', 3);
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
            $a []= STD\lst($kv);
        return STD\lst($a);
    }

    /**
     * Return the list of dictionary keys.
     *
     * @return Lst
     *
     * @code
     *
     * $d= STD\d('a', 1, 'b', 2, 'c', 3);
     * print $d->keys(); /// ["a","b","c"]
     *
     * @endcode
     *
     */
    function keys() {
        $a = array();
        foreach($this->_a as $kv)
            $a []= $kv[0];
        return STD\lst($a);
    }

    /**
     * Return the list of dictionary values.
     *
     * @return Lst
     *
     * @code
     *
     * $d= STD\d('a', 1, 'b', 2, 'c', 3);
     * print $d->values(); /// [1,2,3]
     *
     * @endcode
     *
     */
    function values() {
        $a = array();
        foreach($this->_a as $kv)
            $a []= $kv[1];
        return STD\lst($a);
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
     * $d= STD\d('a', 1, 'b', 2, 'c', '3');
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
     * $d= STD\d('a', 1, 'b', 2, 'c', '3');
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
     * $a= STD\d('a', 1);
     * print $a->update('ABC'); /// '{"a":1,"0":"A","1":"B","2":"C"}'
     *
     * $a= STD\d('a', 1);
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
     * $d= STD\d('a', 1, 'b', 2, 'c', '3');
     * print $d->find(2); /// 'b'
     * is_null($d->find(9)); /// true
     *
     * @endcode
     *
     */
    function find($val) {
        foreach($this->_a as $kv) {
            if(STD\eq($val, $kv[1]))
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
     * $d= STD\d('a', 'x', 'b', 'y', 'c', 'z');
     * $e = $d->map('strtoupper');
     * print $e; /// '{"a":"X","b":"Y","c":"Z"}'
     *
     * $add = function ($x, $y) { return $x + $y; };
     *
     * $d= STD\d('a', 1, 'b', 3, 'c', 5);
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
     * $d= STD\d('a', 1, 'b', 3, 'c', 5);
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
