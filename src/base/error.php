<?php

/**
 * Generic std exception.
 */
class Error extends \Exception {
    function __construct($message='') {
        $c = get_class($this);
        $p = strpos($c, '\\');
        if($p !== false)
            $c = substr($c, $p + 1);
        parent::__construct($c . ': ' . strval($message));
        $this->args = func_get_args();
    }
}

class KeyError extends Error {}

class IndexError extends Error {}

class TypeError extends Error {}

class ValueError extends Error {}

class NotFoundError extends Error {}

class NotImplemented extends Error {}

class UnicodeError extends ValueError {}

class StopIteration extends Error {}

class NameError extends Error {}
