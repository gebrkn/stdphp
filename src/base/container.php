<?php

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
     * $a = STD\a('a', 'b', 'c', 'd', 'e', 'f');
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
     * $a = STD\a('a', 'b', 'c', 'd', 'e', 'f');
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