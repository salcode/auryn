<?php
/**
 * Examples of using the injector with interfaces.
 *
 * Note: The same techniques can be used for abstract classes.
 */

use Auryn\Injector;

require __DIR__ . "/../vendor/autoload.php";

$injector = new Injector;

interface Vowel {
    public function display();
}

class E implements Vowel {
    public function display() { echo 'e'; }
}

class U implements Vowel {
    public function display() { echo 'u'; }
}

class Sound {
    public $vowel;

    public function __construct(Vowel $v) {
        $this->vowel = $v;
    }
}

/**
 * When a parameter type-hints an Interface (a.k.a. dynamic constructor parameter).
 *
 * If uncommented, the line below would throw a fatal error line because
 * class Sound requires a Vowel parameter but the injector can NOT instantiate
 * a Vowel because it is an interface and we don't know what class that
 * implements the interface would work.
 *
 * In much the same way, you could instantiate Sound with an instance of E or U
 * but you can not create an instance of Vowel.
 * new Sound( new E );      // Works.
 * new Sound( new U );      // Works.
 * new Sound( new Vowel );  // Does NOT work.
 */
//$sound0 = $injector->make(Sound::class);

/**
 * Define what class to use for a parameter.
 *
 * Since the injector can't determine what class to instantiate for our
 * instance of a Vowel, we define it before the make step.
 *
 * The first parameter is the name of the class we'll be injecting. The 
 * second parameter is key/value array where the key is the variable name in
 * the constructor and the value is the class that should be instantiated
 * for the parameter.
 */
$injector->define(Sound::class, ['v' => 'E']);
$sound1 = $injector->make(Sound::class);

$sound1->vowel->display();
echo PHP_EOL;

/**
 * Instead of defining the class to use ahead of time, we can define on injection.
 *
 * This is also called defining "on the fly"
 */
$sound2 = $injector->make(Sound::class, [ 'v' => 'U' ]);

$sound2->vowel->display();
echo PHP_EOL;

/**
 * Define an instance of a class to use.
 *
 * Instead of defining the class name to instantiate, we can define an existing
 * instance.  In this case, we define an instance called $myVowel which
 * implements the Vowel interface.
 *
 * Important Note:  While the variable name of the parameter is `v` in this
 * case we use the key `:v`.  The colon (:) indicates were are passing in a
 * raw value (the object) rather than the class name to be instantiated.
 */
class UserDefinedVowel implements Vowel {
    protected $vowel_letter;
    public function __construct(string $letter) {
        $this->vowel_letter = $letter;
    }
    public function display() { echo $this->vowel_letter; }
}

$myVowel = new UserDefinedVowel('Y');

$injector->define(Sound::class, [':v' => $myVowel ]);
$sound3 = $injector->make(Sound::class);

$sound3->vowel->display();
echo PHP_EOL;

/**
 * We can also define an instance to use at injection time (on the fly).
 *
 * Again, we use the colon (:) before the name of the constructor parameter
 * to indicate we are providing a raw value.
 */

$myVowel = new UserDefinedVowel('O');
$sound4 = $injector->make(Sound::class, [':v' => $myVowel]);

$sound4->vowel->display();
echo PHP_EOL;

/**
 * Raw values can be provided without a key.
 *
 * Raw values can be provided without a key, instead relying on parameter order.
 * This can be done with a call to the define method or on injection.
 */

$myVowel = new UserDefinedVowel('A');

$injector->define(Sound::class, [$myVowel]);
$sound5 = $injector->make(Sound::class);
$sound5->vowel->display();
echo PHP_EOL;

$sound6 = $injector->make(Sound::class, [$myVowel]);
$sound6->vowel->display();
echo PHP_EOL;

/**
 * Define mapping for all instances of an interface.
 *
 * Previously, we mapped an interface Vowel to a class (e.g. E) when in the
 * context of a parameter for a specific class (in this case Sound::class).
 * It would be tedious to repeatedly define that class E should be used when
 * the interface Vowel is referenced every time it is need.
 *
 * However, we can assign an alias so that anytime the injector comes across
 * a parameter that implements the interface Vowel the class E is used in
 * its place.
 */

$injector2 = new Injector;
$injector2->alias( 'Vowel', 'E' );
$sound7 = $injector2->make( Sound::class );

$sound7->vowel->display();
echo PHP_EOL;

class MachineNoise {
    public $noise;
    public function __construct( Vowel $vowl ) {
        $this->noise = $vowl;
    }
}

/**
 * Our alias on $injector2 applies to any parameter type-hinting the interface Vowel.
 */
$machineNoise1 = $injector2->make( MachineNoise::class );
echo "Machine noise is: ";
$machineNoise1->noise->display();
echo PHP_EOL;

/**
 * We've defined an alias for $injector2 ONLY.
 *
 * The following line would throw a fatal error.
 */
// $injector->make( MachineNoise::class );
