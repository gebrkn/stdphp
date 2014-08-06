<?php
/**
 * @file Preprocessor
 *
 * Rudimentary preprocessor, supports C-alike directives:
 *
 * #include path.php
 * #include dir (=include dir/_main.php)
 * #define ident expr
 * #if ident
 * #ifdef ident
 * #ifndef ident
 * #else
 * #endif
 *
 */

class CPP
{
    public $paths = array();
    public $ifs   = array();
    public $defs  = array();

    function __construct($defs=null) {
        if(is_array($defs))
            $this->defs = $defs;
    }

    function parse($path) {
        return implode("\n", $this->_include($path));
    }

    function run($in, $out) {
        file_put_contents($out, $this->parse($in));
    }

    function _parse_lines($in) {
        $out = array();
        foreach($in as $n => $line) {
            $line = rtrim($line, "\n");
            if(preg_match('~^\s*#(else|endif)~', $line, $m)) {
                if(!count($this->ifs))
                    user_error("misplaced {$m[1]}");
                if($m[1] == 'else')
                    $this->ifs []= !array_pop($this->ifs);
                if($m[1] == 'endif')
                    array_pop($this->ifs);
                continue;
            }
            if(count($this->ifs) && !end($this->ifs))
                continue;
            if(preg_match('~^(\s*)#(include|define|ifdef|ifndef|undef|if)\b(.*)~', $line, $m)) {
                $var = trim($m[3]);
                switch($m[2]) {
                    case 'include':
                        $indent = $m[1];
                        foreach($this->_include($var, $indent) as $r)
                            $out []= $r;
                        break;
                    case 'define':
                        preg_match('~^(\S+)(.*)~', $var, $n);
                        $this->defs[$n[1]] = trim($n[2]);
                        break;
                    case 'if':
                        $this->ifs []= isset($this->defs[$var]) && (bool) $this->defs[$var];
                        break;
                    case 'ifdef':
                        $this->ifs []= isset($this->defs[$var]);
                        break;
                    case 'ifndef':
                        $this->ifs []= !isset($this->defs[$var]);
                        break;
                    case 'undef':
                        unset($this->defs[$var]);
                        break;
                }
                continue;
            }
            if(count($this->paths) > 1 && $this->_is_delim($line))
                continue;
            $line = str_replace(array_keys($this->defs), array_values($this->defs), $line);
            if(isset($this->defs['_LINE_NUMBERS_']))
                $line .= sprintf('#CPP path=(%s) line=(%d)', end($this->paths), $n + 1);
            $out []= $line;
        }
        return $out;
    }

    function _include($path, $indent=null) {
        if($path[0] != '/' && count($this->paths))
            $path = dirname(end($this->paths)) . "/$path";
        if(is_dir($path))
            $path = rtrim($path, '/') . '/_main.php';
        if(preg_match('~/\w+$~', $path))
            $path .= '.php';

        $lines = file($path);
        if($indent) {
            foreach($lines as &$line) {
                if(!$this->_is_delim($line))
                    $line = $indent . $line;
            }
        }

        $this->paths []= $path;
        $r = $this->_parse_lines($lines);
        array_pop($this->paths);
        return $r;
    }

    function _is_delim($line) {
        return preg_match('~(^<\?)|(\?>$)~', $line);
    }
}
?>