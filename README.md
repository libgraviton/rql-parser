RQL parser
==========

This is a RQL parsing library written in PHP.

This library consists of the following parts:
 - lexer for tokenization RQL code
 - parser for creating abstract syntax tree


Installation
------------

The preferred way to install library is through [composer](http://getcomposer.org/download/).

Either run

```
composer require mrix/rql-parser
```

or add

```json
"mrix/rql-parser": "~0.0.0",
```

to the require section of your `composer.json`.


Usage
-----

```php
<?php
require 'vendor/autoload.php';

// RQL code
$rql = '(eq(a,string:1)|lt(b,2)|(c<>3&d>=string:4&e=eq=boolean:1))&u!=5&not(or(u=6,ge(i,10)))&z=1&(a==2|b<-3|in(c,(2,float:3)))&select(a,b)&sort(+a,-b)&limit(1,2)';

// lexer
$lexer = new Mrix\Rql\Parser\Lexer();

// tokenize RQL
$tokens = $lexer->tokenize($rql);

// default parser contains all parsing strategies
$parser = Mrix\Rql\Parser\Parser::createDefault();

// parsing
var_dump($parser->parse($tokens));
```

See also [rql-command library](https://github.com/mrix/rql-command).
This is a console application to debug RQL lexing and parsing.


Current state
-------------

### Basic syntax ###

 - scalar operators
    - `eq(a,b)`
    - `ne(a,b)`
    - `lt(a,b)`
    - `gt(a,b)`
    - `le(a,b)`
    - `ge(a,b)`
 - array operators
    - `in(a,(b,c))`
    - `out(a,(b,c))`
 - logic operators
    - `and(eq(a,b),ne(c,d))`
    - `or(eq(a,b),ne(c,d))`
    - `not(eq(a,b))`

### Short logic syntax ###

 - `(eq(a,b)&ne(b,c))`
 - `(eq(a,b)|ne(b,c))`

### FIQL syntax ###

 - scalar operators
    - `a=eq=b`
    - `a=ne=b`
    - `a=lt=b`
    - `a=gt=b`
    - `a=le=b`
    - `a=ge=b`
 - array operators
    - `a=in=(b,c)`
    - `a=out=(b,c)`

### Simplified FIQL syntax ###

 - `a=b`
 - `a==b`
 - `a<>b`
 - `a!=b`
 - `a<b`
 - `a>b`
 - `a<=b`
 - `a>=b`

### Constants ###

 - `true`
 - `true()`
 - `false`
 - `false()`
 - `null`
 - `null()`
 - `empty()`

### Type casting ###

 - `string:1`
 - `boolean:0`
 - `integer:a`
 - `float:1`

### Other ###

 - `select(a,b,c)`
 - `sort(+a,-b)`
 - `limit(1,2)`

All syntax variations may be used together.


Resources
---------
 * [RQL Rules](https://github.com/persvr/rql)
 * [RQL documentation](https://doc.apsstandard.org/2.1/spec/rql)