<?php
/**
 * @file std.php
 *
 * @mainpage
 *
 * ###Standard library for php.
 *
 * @version 0.0.1
 * @date 2014
 * @author Georg Barikin <georg@thisveryfish.com>
 * @copyright MIT License
 * @see https://github.com/gebrkn/stdphp
 *
 *
 * --------------------------------
 *
 * @ref std.php "Classes and functions reference".
 *
 */

#ifdef GLOBALFUNCS
#define STD\
namespace {
#include globals
}
#else
#define STD\ \std\
#endif

/**
 * `std` namespace
 */
namespace std {
    #include base
    #include iter
    #include string
    #include list
    #include dict
    #include set
    #include re

    #ifndef GLOBALFUNCS
    #include globals
    #endif
}

?>