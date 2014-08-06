<?php

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
     * $s= STD\set([1,2,3]);
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
     * $s= STD\set([1,2,3]);
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
     * $s= STD\set([1,2,3]);
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
     * $s= STD\set([1,2,3]);
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
     * $a= STD\set([1,2,3]);
     * $b= STD\set([4,5,6]);
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
        $a= STD\set($this->_dct->keys());
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
     * $s= STD\set([1,2,3]);
     * $b= STD\set([2,4,3]);
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
     * $a= STD\set([1,2,3,4,5]);
     * $b= STD\set([2,4,6]);
     * $b= STD\set([2,4,6]);
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
     * STD\set('abc')->isdisjoint(STD\set('def')); /// true
     * STD\set('abc')->isdisjoint(STD\set('dea')); /// false
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
     * STD\set('abc')->issubset(STD\set('abcd')); /// true
     * STD\set('abc')->issubset(STD\set('abc'));  /// true
     * STD\set('abc')->issubset(STD\set('aXc'));  /// false
     *
     * @endcode
     *
     */
    function issubset($other) {
        $other= STD\set($other);
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
     * STD\set('abc')->issuperset(STD\set('ab')); /// true
     * STD\set('abc')->issuperset(STD\set('aX')); /// false
     *
     * @endcode
     *
     */
    function issuperset($other) {
        return STD\set($other)->issubset($this);
    }


}