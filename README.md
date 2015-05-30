RQL parser
==========

[![Build Status](https://travis-ci.org/mrix/rql-parser.svg?branch=master)](https://travis-ci.org/mrix/rql-parser)

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
"mrix/rql-parser": "*",
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


Encoding rules
--------------

### String values ###

In string values all non-alphanumeric characters must be encoded with a percent (%) sign followed by two hex digits.

Examples:

```
eq(string,2015%2D05%2D30T15%3A10%3A00Z)
in(string,(%2B1%2E5,%2D1%2E5))
in(string,(null%28%29,empty%28%29,true%28%29,false%28%29))
```

#### String encoding in PHP: ####

```php
function encodeString($value)
{
    return strtr(rawurlencode($value), [
        '-' => '%2D',
        '_' => '%5F',
        '.' => '%2E',
        '~' => '%7E',
    ]);
}
```

#### String encoding in JavaScript: ####

```js
function encodeString(value) {
    return encodeURIComponent(value).replace(/[\-_\.~!\\'\*\(\)]/g, function (char) {
        return '%' + char.charCodeAt(0).toString(16).toUpperCase();
    });
}
```


### Other values ###

Date, number and const-function values must not be encoded.

Examples:

```
eq(date,2015-05-30T15:10:00Z)
in(number,(+1.5,-1.5))
in(const,(null(),empty(),true(),false()))
```


Resources
---------
 * [RQL Rules](https://github.com/persvr/rql)
 * [RQL documentation](https://doc.apsstandard.org/2.1/spec/rql)