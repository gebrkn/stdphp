<?php
/**
 * Regexp match object.
 *
 * @test
 *
 * $pat = '([a-z]+)(?P<digits>\d+)([A-Z]+)';
 * $str = '...abc123XYZ...';
 *
 * $m = STD\re($pat)->match($str);
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
        $this->_groups = STD\lst();
        $this->_start = dict();
        $this->_end = dict();

        foreach($matches as $idx => $grp) {
            $val = STD\str($grp[0]);
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
