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

// authenticator
$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '65176d7a1f4e734f6a4d737190825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd82cbeeef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

// client
$stuartClient = new \Stuart\Client($authenticator);

$dropOffAt = new \DateTime('now', new DateTimeZone('Europe/London'));
$dropOffAt->add(new \DateInterval('PT2H'));

$job = new \Stuart\JobStacked();

$job->addPickup('46 Boulevard Barbès, 75018 Paris')
    ->setComment('Wait outside for an employee to come.')
    ->setContactCompany('KFC Paris Barbès')
    ->setContactFirstName('Martin')
    ->setContactLastName('Pont')
    ->setContactPhone('+33698348756');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setComment('code: 3492B. 3e étage droite. Sonner à Durand.')
    ->setContactCompany('Durand associates.')
    ->setContactFirstName('Alex')
    ->setContactLastName('Durand')
    ->setContactPhone('+33634981209')
    ->setClientReference('Order #' . mt_rand(10, 10000))
    ->setPackageDescription('Pizza box.')
    ->setPackageType('small')
    ->setDropOffAt($dropOffAt);


$stuartJob = $stuartClient->createStackedJob($job);

print_r($stuartJob);
