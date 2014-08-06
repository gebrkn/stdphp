<?php

/**
 * Append items to the list.
 *
 * @param mixed $x,...
 * @return $this
 *
 * @code
 *
 * print STD\a('a', 'b', 'c')->append('d', 'e'); /// ["a","b","c","d","e"]
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
 * print STD\a(1,2,3)->extend(array(4,5), 'ab'); /// [1,2,3,4,5,"a","b"]
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
 * $a = STD\lst('abc');
 * $x = $a->pop();
 * print $a; /// ["a","b"]
 * print $x; /// 'c'
 *
 * $a = STD\lst('abc');
 * $x = $a->pop(1);
 * strval($a); /// '["a","c"]'
 * strval($x); /// 'b'
 *
 * $a = STD\lst('abc');
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
 * $a = STD\lst('abc');
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
 * STD\lst('abc')->contains('a'); /// true
 * STD\lst('abc')->contains('X'); /// false
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
 * STD\lst('abcd')->find('c'); /// 2
 * STD\lst('abcd')->find('x'); /// null
 *
 * @endcode
 *
 */
function find($x) {
    foreach($this->_items as $i => $v)
        if(STD\eq($v, $x))
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
 * $a = STD\lst('aXbXcXd');
 * $a->remove('X');
 * print $a; /// ["a","b","c","d"]
 *
 * $a = STD\lst('aXbXcXd');
 * $a->remove('X', 2);
 * print $a; /// ["a","b","c","X","d"]
 *
 * @endcode
 *
 */
function remove($x, $count=-1) {
    $b = array();
    foreach($this->_items as $v) {
        if(STD\eq($v, $x) && $count) {
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
 * $a = STD\a(1, 200, 21);
 * $a->sort();
 * strval($a); /// '[1,21,200]'
 *
 * $a = STD\a("1", "200", "21");
 * $a->sort();
 * strval($a); /// '["1","200","21"]'
 *
 * $a = STD\a(21, 200, 1);
 * $a->sort('strval');
 * strval($a); /// '[1,200,21]'
 *
 * $a = STD\a(1, 200, 21);
 * $a->sort(function($x) { return -$x; });
 * strval($a); /// '[200,21,1]'
 *
 * $a = STD\a("Apple9", "orange", "apple");
 * $a->sort();
 * strval($a); /// '["Apple9","apple","orange"]'
 *
 * $a = STD\a("Apple9", "orange", "apple");
 * $a->sort('strtoupper');
 * strval($a); /// '["apple","Apple9","orange"]'
 *
 * $a = STD\a(
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
 * $a = STD\lst("abc");
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
    return STD\str(implode(STD\str($sep), array_map('strval', $this->_items)));
}

/**
 * Call a function of every element and return a new list.
 *
 * @param callable $func
 * @return Lst
 *
 * @code
 *
 * $a = STD\a('aa', 'bb', 'cc');
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
    return STD\lst($a);
}

/**
 * Filter the list.
 *
 * @param $func
 * @return Lst
 *
 * @code
 *
 * $a = STD\a(1,2,3,4,5,6);
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
    return STD\lst($a);
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
 * $a = STD\a(1,2,3,4,5);
 * $add = function($x, $y) { return $x + $y; };
 * print $a->reduce($add); /// 15
 *
 * $a = STD\lst('abcd');
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
