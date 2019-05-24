stdphp
======

Standard library for php.

`php` is not my primary language, but when I use it occasionally, I always miss a consistent standard library. Therefore I've created this one for my needs.

`stdphp` provides a set of basic classes and utility functions. It's mostly modeled after python, with some additions from javascript and ruby.

### Show Me What You Got

Some examples of what `stdphp` looks like.

##### Sort words in a string

```php
print str('foo bar baz')->split()->map('strtoupper')->sort()->join(','); /// BAR,BAZ,FOO
```

##### Unicode fun

```php
$str = str('Gänsefüßchen');
print count($str);           /// 12
print $str['5:8'];           /// 'füß'
print $str->upper();         /// 'GÄNSEFÜßCHEN'
```

##### Remove duplicates from a list

```php
$dupes = [4,1,3,2,4,1,4,2,3,3,1,2];
print set($dupes); /// [4,1,3,2]
```

##### Sum every second number

```php
$numbers = a(0,11,22,33,44,55,66,77,88,99);
print $numbers['::2']->reduce(operator('+')); /// 220
```

##### FizzBuzz

```php
$x = lst(xrange(31));

$x['::3']  = repeat('Fizz');
$x['::5']  = repeat('Buzz');
$x['::15'] = repeat('FizzBuzz');

print $x['1:']; /// [1,2,"Fizz",4,"Buzz","Fizz",7,8,"Fizz","Buzz",11,"Fizz",13,14,"FizzBuzz",16,17,"Fizz",19,"Buzz","Fizz",22,23,"Fizz","Buzz",26,"Fizz",28,29,"FizzBuzz"]
```

##### Chunkify a list

If you know python, this is `zip(*[iter(xs)]*n)`:

```php
$a = [1,2,3,11,22,33,101,102,103];
print lst(zipargs(repeat(iter($a), 3))); /// [[1,2,3],[11,22,33],[101,102,103]]
```

### Usage

The whole library is one single php file, [`std.php`](https://raw.githubusercontent.com/gebrkn/stdphp/master/lib/std.php):

    include 'std.php';

    $someList = lst("abc");

There's also a "local" version [`stdl.php`](https://raw.githubusercontent.com/gebrkn/stdphp/master/lib/stdl.php), which keeps `stdphp` functions namespaced:

    include 'stdl.php';

    $someList = std\lst("abc");

`stdphp` requires php 5.4+

### Features

#### Factory functions

Objects, like strings or lists, are created with factories:

```php
$someList   = lst([1,2,3]);
$someString = str("abc");
```

This has two advantages: conciseness and ability to override default classes with inherited ones:


    class MyList extends std\Lst
    {
        function myNewMethod() { return $this->join(' me '); }
    }

    std\_setclass('Lst', 'MyList');

    // now, all library functions that return a list, will return MyList:

    print str('foo bar')->split()->myNewMethod(); /// foo me bar


#### Iterables and iterators

`stdphp` follows the pythonic concept of "iterables". Functions that iterate through their arguments don't enforce a particular type. Therefore you can pass any "iterable", including `stdphp` objects, native php arrays and strings, and generic `Traversable` objects:

```php
$a = a(0,1,2,3);
$b = "abcd";
$c = array(9,8,7,6);

print lst(zip($a, $b, $c)); /// [[0,"a",9],[1,"b",8],[2,"c",7],[3,"d",6]]
```

There are several built-in iterator constructors for different purposes:

- `filter`
- `map`
- `repeat`
- `xrange`
- `zip`

The following creates a "lazy" list of palindromic numbers. Since `filter`, `map` and `xrange` are iterators, this list uses constant memory:

```php
$xs = filter(
    function($s) { return $s == $s['::-1']; },
    map('str',
        xrange(300000)));
```

Another example is an "endless" `repeat` iterator that plays nicely with `zip`:

```php
print lst(zip('abcd', repeat('-'))); /// [["a","-"],["b","-"],["c","-"],["d","-"]]
```

#### Mapping and higher-order functions

The higher-order function `getter` returns a closure that picks an item from a string or associative array.

```php
// fetch the 2nd char from each string:

print a('ab','cd','ef')->map(getter(1)); /// ["b","d","f"]
```

`getter` (alias `by`) is especially useful with `sort`, which, like in python, uses a functional `key` argument to extract a comparison key.

```php
$popularNames = [
     ['name' => 'Alfie'   ,'rank' => 7],
     ['name' => 'Charlie' ,'rank' => 4],
     ['name' => 'Harry'   ,'rank' => 1],
     ['name' => 'Jack'    ,'rank' => 3],
     ['name' => 'Jacob'   ,'rank' => 5],
     ['name' => 'Oliver'  ,'rank' => 2],
     ['name' => 'Riley'   ,'rank' => 8],
     ['name' => 'Thomas'  ,'rank' => 6],
     ['name' => 'William' ,'rank' => 9]
];

// print top 3 names by rank

print lst($popularNames)->sort(by('rank'))->map(getter('name'))->get(':3'); /// ["Harry","Oliver","Jack"]
```

Similarly, `attr` picks an attribute from an object argument:

```php
class User {
    function __construct($name) {
        $this->name = $name;
    }
}

$a = [new User("Harry"), new User("Oliver"), new User("Jack")];
print lst($a)->map(attr('name'))->join(','); /// Harry,Oliver,Jack
```

`method` creates a closure that calls a specific method on the argument:

```php
// call `String::reverse` on each element:

print s("Hello Foo Bar")->split()->map(method('reverse')); /// ["olleH","ooF","raB"]
```

### stdphp classes

Here's a brief overview, for a complete list of methods refer to the [API documentation](http://merribithouse.net/stdphp/doc/html/).

#### List

Lists are created using constructors `a()` or `lst()` and provide the usual repertoire of methods:

```php
print a('foo', 'bar', 'baz')->map('strtoupper')->join('-'); /// FOO-BAR-BAZ
```

Lists support python-alike slicing:

```php
$a = a(0,1,2,3,4,5,6,7);
print $a[-3];     /// 5
print $a['1::3']; /// [1,4,7]
```

and slice assignments:

```php
$a = a(0,1,2,3,4,5);
$a['2:4'] = 'abcd';
print $a; /// [0,1,"a","b","c","d",4,5]
```

#### String

Strings are unicode-aware (source encoding is assumed to be utf8, unless specified otherwise):

```php
$a = s('fuß');
print count($a); /// 3

$b = s("fu\xDF", 'latin1');
$a == $b; /// true
```

Slicing works too, but not slice assignments: strings are immutable! Note: `mb` extension is required for encoding and case conversions.

#### Dict

Dicts are associative arrays. There are several `dict` constructors:

```php
print d('a', 1, 'b', 2);            /// {"a":1,"b":2}
print dict(['a' => 1, 'b' => 2]);   /// {"a":1,"b":2}
print pairdict([['a',1], ['b',2]]); /// {"a":1,"b":2}
print keydict('abc', 42);           /// {"a":42,"b":42,"c":42}

```

In addition to standard pythonic methods, dicts can be also mapped and filtered:

```php
$a = dict(['a'=>'foo', 'b'=>'bar']);
print $a->map('strtoupper'); /// {"a":"FOO","b":"BAR"}
```

#### Set

Sets are just like python sets:

```php
$a = set([1,2,3,4,5]);
print $a->intersection([5,3,9,1]); /// [1,3,5]
```

#### Re

`re` creates a regular expression object. Unlike php, delimiters are not required, and flags can be passed as a second argument:

```php
$r = re('[a-z]+', 'i');
print $r->sub('*', 'a1B2c3'); /// *1*2*3
```

`find` and `findall` return strings:

```php
print re('[a-z]+')->findall('ab cd ef'); /// ["ab","cd","ef"]
```

`match` and `matchall` return "match objects":

```php
print re('([a-z]+)(\d+)')->match('abc123')->group(2); /// 123
```

### Build system

`std.php` is generated from the `src` directory using small c-alike preprocessor `CPP` (in `tools` dir). `doxytest` runs tests which are embedded in the code and enclosed in `@code...@endcode` or `@test...@endtest`.

### Todos

- wrappers for all php array/string functions
- iterable files and streams, as in `foreach (textfile('foo.txt', 'utf8')) as $line)...`
- `itertools` methods like `product` or `combinations`
