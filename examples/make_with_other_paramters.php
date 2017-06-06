<?php
/**
 * Example of using the injector with other parameters.
 */

use Auryn\Injector;

require __DIR__ . "/../vendor/autoload.php";

$injector = new Injector;

/**
 * Raw values passed into the injector can be of any type.
 *
 * The injector excepts all values passed in to be classes, however by passing
 * a raw value using the leading colon (:), we can pass in a value of any type.
 */
class Cat {
    public $lives;
    public function __construct( int $lives ) {
        $this->lives = $lives;
    }
}

$cat = $injector->make( Cat::class, [':lives' => 9] );
var_dump( $cat );
echo PHP_EOL;

/**
 * An array can be passed in as a raw value.
 */
class Names {
    public $names;

    public function __construct( $name_list ) {
        $this->names = $name_list;
    }
}

// We use the colon (:) to indicate this is a raw value.
$names = $injector->make( Names::class, [
    ':name_list' => ['Tom', 'Dick', 'Harry']
]);
var_dump( $names );

/**
 * If the key is omitted, the values are assumed to be raw and in parameter order.
 */
$names2 = $injector->make( Names::class, [
    [ 'larry', 'moe', 'curly', 'shemp' ]
]);
var_dump( $names2 );
