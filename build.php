<?php
// build the lib

$dir = __DIR__;

include "$dir/tools/cpp.php";

$cpp = new CPP();
$cpp->run("$dir/src/", "$dir/lib/stdl.php");

$cpp = new CPP(array('GLOBALFUNCS' => 1));
$cpp->run("$dir/src/", "$dir/lib/std.php");

$doxygen = '/Applications/Doxygen.app/Contents/Resources/doxygen';
`rm -fr $dir/../../doc && cd $dir/lib && $doxygen ../tools/doxygen.conf`;


?>