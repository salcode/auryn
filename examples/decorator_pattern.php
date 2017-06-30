<?php

require __DIR__ . "/../vendor/autoload.php";

interface AInterface {
	public function render();
}

class Decorator implements AInterface {
	private $instance;

	public function __construct( AInterface $instance ) {
		$this->instance = $instance;
	}

	public function render() {
		return sprintf( '! %s !', $this->instance->render() );
	}
}

class A implements AInterface {
    private $a;
    private $b;

    public function __construct(stdClass $a, stdClass $b) {
        $this->a = $a;
        $this->b = $b;
    }

    public function render() {
		return sprintf( '%s || %s',
			$this->a->foo,
			$this->b->foo
		);
    }
}

$injector = new Auryn\Injector;

$injector->define(A::class, [
    "+a" => function () {
        $std = new stdClass;
        $std->foo = "foo";
        return $std;
    },
    "+b" => function () {
        $std = new stdClass;
        $std->foo = "bar";
        return $std;
    },
]);

/**
 * The "long" version is to use the injector to instantiate $a and the use that
 * as the parameter when instantiating the Decorator (without the injector).
 */
// $a = $injector->make(A::class);
// $a_prime = new Decorator( $a );
// echo $a_prime->render() . "\n";

/**
 * Here we are using the injector to instantiate the decorator and defining
 * the parameter for Decorator should be an instance of A (and the define()
 * method we call earlier assigns the parameters necessary).
 */
$a_prime = $injector->make(Decorator::class, [ 'instance' => A::class ]);
echo $a_prime->render() . "\n";
