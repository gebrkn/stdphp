<?php

/**
 * @file Run doctests.
 *
 * A typical doctest looks like this:
 *
 * @code
 *
 * some_expression; /// expected_result
 *
 * @endcode
 *
 * `doxytest` evaluates expressions, compares actual results with expected,
 * and shouts if they don't match. This is it.
 *
 */

function _doxytest_write() {
    foreach(func_get_args() as $line) {
        if(!$line)
            $line = '-----------------';
        fwrite(STDOUT, "* $line\n");
    }
}

function _doxytest_parse($path, $lines, $opts) {
    $capture = false;
    $src = array();
    $test = array();
    foreach($lines as $n => $line) {
        $line = rtrim($line);
        if($opts['cpp_line_numbers'] && preg_match('~(.*?)#CPP path=\((.+?)\) line=\((.+)\)~', $line, $m)) {
            $line  = $m[1];
            $npath = $m[2];
            $nlnum = intval($m[3]);
        } else {
            $npath = $path;
            $nlnum = $n + 1;
        }
        if(!$capture) {
            $src []= $line;
            if(preg_match($opts['start'], $line))
                $capture = true;
            continue;
        }
        if(preg_match($opts['end'], $line)) {
            $src []= $line;
            $capture = false;
            continue;
        }
        $line = ltrim(trim($line), '*');
        if(preg_match('~^(.+?)(##|///)(.+)$~', $line, $m)) {
            $act = trim($m[1], " ;");
            $exp = trim($m[3], " ;");
            if(preg_match('~^print\s+(.+)~', $act, $m)) {
                $act = "strval({$m[1]})";
                $exp = trim($exp, "'");
                $exp = "'$exp'";
            }
            $line = "\\_doxytest_test($act,$exp,'$npath',$nlnum);";
        }
        $test []= $line;
    }
    return array(
        implode("\n", $src),
        implode("\n", $test));
}

function doxytest($path, $opts=null) {
    $defaults = array(
        'start'   => '/@(code|test)/',
        'end'     => '/@end(code|test)/',
        'verbose' => false,
        'path'    => $path,
        'temp_path' => '/tmp/_doxytest_.php',
        'cpp_line_numbers' => true,
        'remove_source' => false,
        'prepend' => '',
    );
    $opts = ($opts ? $opts : array()) + $defaults;
    list($src, $test) = _doxytest_parse($path, file($path), $opts);
    $test = sprintf("<?php namespace {
        %s;
        include '%s';
        \\_doxytest_init(%s);
        %s
        \\_doxytest_done();
    }?>", $opts['prepend'], __FILE__, var_export($opts, 1), $test);
    if($opts['remove_source'])
        $src = '';


    $tmp = $opts['temp_path'];
    file_put_contents($tmp, $src . $test);
    $cmd = PHP_BINARY . " $tmp 2>&1";

    exec($cmd, $out, $rc);
    if($rc)
        $out []= "* exit code: $rc";
    $out = implode("\n", $out) . "\n";

    unlink($tmp);
    return $out;
}

function _doxytest_init($opts) {
    global $_doxytest;

    $_doxytest = $opts;
    $_doxytest['total'] = 0;
    $_doxytest['failed'] = 0;
    $_doxytest['time'] = microtime(true);
    _doxytest_write(sprintf("BEGIN doxytest %s (php %s in %s)", $opts['path'], phpversion(), PHP_BINARY));

}

function _doxytest_test($actual, $expect, $path, $lineno) {
    global $_doxytest;

    $_doxytest['total']++;
    if($actual !== $expect) {
        $a = gettype($actual) . ' ' . var_export($actual, 1);
        $e = gettype($expect) . ' ' . var_export($expect, 1);
        _doxytest_write("FAILED  $path:$lineno", '', "expect: $e", '', "actual: $a", '');
        $_doxytest['failed']++;
    } else if($_doxytest['verbose']) {
        $a = gettype($actual)   . ' ' . var_export($actual, 1);
        _doxytest_write("passed $path:$lineno", '', "actual: $a", '');
    }
}

function _doxytest_done() {
    global $_doxytest;

    $ts = microtime(true) - $_doxytest['time'];
    $msg = sprintf("END doxytest %s, %s tests in %.3f sec", $_doxytest['path'], $_doxytest['total'], $ts);
    if($_doxytest['failed'])
        $msg .= ", {$_doxytest['failed']} FAILED";
    _doxytest_write($msg);
}
