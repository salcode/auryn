<?php

use Auryn\Injector;

require __DIR__ . "/../vendor/autoload.php";

$injector = new Injector;

class A {
    public $std;

    public function __construct(stdClass $std) {
        $this->std = $std;
    }
}

/**
 * Why `new` doesn't work in this scenario.
 *
 * If uncommented, the line below would throw a fatal error line because
 * class A requires a stdClass parameter.
 */

// $a0 = new A();

/**
 * Using the injector instead of `new`
 *
 * This is the same thing as `new A()` EXCEPT since the parameter is missing
 * instead of throwing a fatal error the injector does its magic and
 * instantiates an instance of stdClass and passes it as the missing parameter.
 *
 * This is the same as $a1 = new A( new stdClass );
 */
$a1 = $injector->make('A');

echo '$a1 = ';
var_dump( $a1 );
echo PHP_EOL;

/**
 * Using the class constant `::class`
 *
 * PHP 5.5.0 introduced the class constant `::class`, which we can use
 * in place of the class's name.
 * See http://php.net/manual/en/language.oop5.constants.php#example-184
 *
 * This is the same as $a2 = $injector->make('A');
 */
$a2 = $injector->make(A::class);

echo '$a2 = ';
var_dump( $a2 );
echo PHP_EOL;

/**
 * What if we don't want our stdClass parameter to be empty?
 *
 * In other words we want to do something like the following but using
 * our injector instead of `new`.
 * $stdClass = new stdClass;
 * $stdClass->foo = "foobar";
 * $a3 = new A( $stdClass );
 *
 * We can do this by passing our parameters in as a key/value array, where
 * the key is the parameter variable name and the value is our object.
 */
$stdClass = new stdClass;
$stdClass->foo = "foobar";

$a3 = $injector->make(A::class, [
    ":std" => $stdClass,
]);

echo '$a3 = ';
var_dump( $a3 );
echo PHP_EOL;

/**
 * Recursive Dependency Injection
 *
 * Class Z requires an instance of class A which requires an instance of stdClass.
 * Our injector will recurse through these requirements instantiating the
 * instances we need.
 */
class Z {
    public $a;

    public function __construct(A $a ) {
        $this->a = $a;
    }
}

$z = $injector->make(Z::class);

echo '$z = ';
var_dump( $z );
echo PHP_EOL;
