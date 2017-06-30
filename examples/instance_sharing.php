<?php
/**
 * Examples of instance sharing.
 */

use Auryn\Injector;

require __DIR__ . "/../vendor/autoload.php";

$injector = new Injector;

class SecretKeeper {
    public $secret;

    public function __construct( string $scrt ) {
        $this->secret = $scrt;
    }
}

/**
 * Both $vault1 and $vault2 are the same instance.
 *
 * Comment out the line: $injector->share(SecretKeeper::class);
 * and they will be two separate instances.
 */
$injector->share(SecretKeeper::class);
$vault1 = $injector->make( SecretKeeper::class, [
    ':scrt' => '12345',
]);
$vault2 = $injector->make( SecretKeeper::class, [
    ':scrt' => 'new secret',
]);

echo 'Vault1: ' . $vault1->secret;
echo PHP_EOL;
echo 'Vault2: ' . $vault2->secret;
echo PHP_EOL;

$vault1->secret = 'password';

echo 'Vault1: ' . $vault1->secret;
echo PHP_EOL;
echo 'Vault2: ' . $vault2->secret;
echo PHP_EOL;

/**
 * We can designate an existing instance to be used as shared whenever
 * an object of that type is needed.
 */


