<?php

/**
 * @nodoc
 *
 * @test
 *
 * $a = STD\lst('abcdef');
 * $a[] = 'Z';
 * strval($a) /// '["a","b","c","d","e","f","Z"]'
 *
 * $a = STD\lst('abcdef');
 * $a[3] = 'X';
 * strval($a) /// '["a","b","c","X","e","f"]'
 *
 * $a = STD\lst('abcdef');
 * try {
 *    $a[100] = 'Y';
 * } catch(\std\IndexError $e) {
 *    $e->args[0]; /// 100
 * }
 *
 * $a = STD\lst('abcdef');
 * $a['1:1'] = array(5,6,7);
 * strval($a); /// '["a",5,6,7,"b","c","d","e","f"]'
 *
 * $a = STD\lst('abcdef');
 * $a['1:4'] = array(5,6,7);
 * strval($a); /// '["a",5,6,7,"e","f"]'
 *
 * $a = STD\lst('abcdef');
 * $a['1:4'] = 'XY';
 * strval($a); /// '["a","X","Y","e","f"]'
 *
 * $a = STD\lst('abcdef');
 * $a['1:6:2'] = '123';
 * strval($a); /// '["a","1","c","2","e","3"]'
 *
 * $a = STD\lst('abcdef');
 * $a['-1:2:-1'] = '123';
 * strval($a); /// '["a","b","c","3","2","1"]'
 *
 * $a = STD\lst('abcdef');
 * $a['-1:0:-2'] = '123';
 * strval($a); /// '["a","3","c","2","e","1"]'
 *
 * $a = STD\lst('abcdef');
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
 * $a = STD\lst('abcdef');
 * unset($a[1]);
 * strval($a); /// '["a","c","d","e","f"]'
 *
 * $a = STD\lst('abcdef');
 * unset($a[-2]);
 * strval($a); /// '["a","b","c","d","f"]'
 *
 * $a = STD\lst('abcdef');
 * try {
 *    unset($a[100]);
 * } catch(\std\IndexError $e) {
 *    $e->args[0]; /// 100
 * }
 *
 * $a = STD\lst('abcdef');
 * unset($a['1:4']);
 * strval($a); /// '["a","e","f"]'
 *
 * $a = STD\lst('abcdef');
 * unset($a['1:6:2']);
 * strval($a); /// '["a","c","e"]'
 *
 * $a = STD\lst('abcdef');
 * unset($a['-1:2:-1']);
 * strval($a); /// '["a","b","c"]'
 *
 * $a = STD\lst('abcdef');
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
