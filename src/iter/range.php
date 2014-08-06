<?php

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
