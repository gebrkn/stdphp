<?php
// run doctests

$dir = __DIR__;

include "$dir/tools/doxytest.php";
include "$dir/tools/cpp.php";

// run tests in global mode
$cpp = new CPP(array('_LINE_NUMBERS_' => 1, 'GLOBALFUNCS' => 1));
$tmp = '/tmp/__std.php';
$cpp->run("$dir/src/", $tmp);
echo doxytest($tmp, array(
    'cpp_line_numbers' => true
));
unlink($tmp);

// run tests in local mode
$cpp = new CPP(array('_LINE_NUMBERS_' => 1));
$tmp = '/tmp/__stdl.php';
$cpp->run("$dir/src/", $tmp);
echo doxytest($tmp, array(
    'cpp_line_numbers' => true
));
unlink($tmp);

// test README code examples
echo doxytest("$dir/README.md", array(
    'start' => '/```/',
    'end' => '/```/',
    'remove_source' => true,
    'prepend' => "include '$dir/lib/std.php';",
));
