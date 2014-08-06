<?php

/**
 * True if all characters in the string are alphanumeric.
 *
 * @return bool
 *
 * @code
 *
 * STD\s('abc1')->isalnum(); /// true
 * STD\s('füß2')->isalnum(); /// true
 * STD\s('abc?')->isalnum(); /// false
 *
 * @endcode
 *
 */
function isalnum() {
#ifdef UNICODE
    return _utf8_type($this->_str, 'L,N');
#else
    return ctype_alnum($this->_str);
#endif
}

/**
 * True if all characters in the string are letters.
 *
 * @return bool
 *
 * @code
 *
 * STD\s('fuß')->isalpha(); /// true
 *
 * @endcode
 *
 */
function isalpha() {
#ifdef UNICODE
    return _utf8_type($this->_str, 'L');
#else
    return ctype_alpha($this->_str);
#endif
}

/**
 * True if all characters in the string are digits.
 *
 * @return bool
 *
 * @code
 *
 * STD\s('123')->isdigit(); /// true
 *
 * @endcode
 *
 */
function isdigit() {
#ifdef UNICODE
    return _utf8_type($this->_str, 'Nd');
#else
    return ctype_digit($this->_str);
#endif
}

/**
 * True if all characters in the string are spaces.
 *
 * @return bool
 *
 * @code
 *
 * STD\s("\t\f\n")->isspace(); /// true
 *
 * @endcode
 *
 */
function isspace() {
#ifdef UNICODE
    return _utf8_type($this->_str, 'Zs');
#else
    return ctype_space($this->_str);
#endif
}

/**
 * True if all characters in the string are lower-case letters.
 *
 * @return bool
 *
 * @code
 *
 * STD\s('fuß')->islower(); /// true
 *
 * @endcode
 *
 */
function islower() {
#ifdef UNICODE
    return _utf8_type($this->_str, 'Ll');
#else
    return ctype_lower($this->_str);
#endif
}

/**
 * True if all characters in the string are upper-case letters.
 *
 * @return bool
 *
 * @code
 *
 * STD\s('FÜSS')->isupper(); /// true
 *
 * @endcode
 *
 */
function isupper() {
#ifdef UNICODE
    return _utf8_type($this->_str, 'Lu');
#else
    return ctype_upper($this->_str);
#endif
}

?>