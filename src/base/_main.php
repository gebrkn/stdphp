<?php

function _getclass($name) {
    if(!isset($GLOBALS['__std__classes__'][$name]))
        return "\\std\\$name";
    return $GLOBALS['__std__classes__'][$name];
}

function _setclass($name, $cls) {
    $GLOBALS['__std__classes__'][$name] = $cls;
}


#include slices
#include error
#include container
#include misc
