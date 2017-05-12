<?php
function _require_all($dir, $depth = 0)
{
    if ($depth > 10) {
        return;
    }
    // require all php files
    $scan = glob("$dir/*");
    foreach ($scan as $path) {
        if (preg_match('/\.php$/', $path)) {
            require_once $path;
        } elseif (is_dir($path)) {
            _require_all($path, $depth + 1);
        }
    }
}

require 'vendor/autoload.php';
_require_all('php');

$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '65176d7a1f4e734f6a4d737190825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd82cbeeef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

$stuartClient = new \Stuart\Client($authenticator);

$origin = [
    'address' => '18 rue sidi brahim, 75012 Paris',
    'company' => 'WeSellWine Inc.',
    'first_name' => 'Marcel',
    'last_name' => 'Poisson',
    'phone' => '0628739512'
];
$destination = [
    'address' => '8 rue sidi brahim 75012 paris',
    'company' => 'Jean-Marc SAS',
    'first_name' => 'Jean-Marc',
    'last_name' => 'Pinchu',
    'phone' => '0628046934'
];
$package_size = 'small';

$pickupAt = new \DateTime('now', new DateTimeZone('Europe/London'));
$pickupAt->add(new \DateInterval('PT1H'));

$options = ['pickup_at' => $pickupAt];
$job = new \Stuart\Job($origin, $destination, $package_size, $options);

$stuartJob = $stuartClient->createJob($job);

print_r($stuartJob);