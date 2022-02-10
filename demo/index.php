<?php
require __DIR__ . '/vendor/autoload.php';

print "Welcome to the Stuart PHP Library Demo\n";

print "Setting up sandbox credentials...\n";

// Visit https://stuart.api-docs.io/v2/general-topics/getting-started for more information
$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = 'PUT_YOUR_CLIENT_ID_HERE';
$api_client_secret = 'PUT_YOUR_CLIENT_SECRET_HERE';
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret, new \Stuart\Cache\DiskCache("stuart_cache.txt"));

$httpClient = new \Stuart\Infrastructure\HttpClient($authenticator);

$client = new \Stuart\Client($httpClient);

print "Creating a job...\n";
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');
// $job->addPickup('46 Boulevard Barbès, 75018 Paris', 48.887279, 2.349656); // Check https://community.stuart.engineering/t/job-creation-with-lat-long-coordinates/1436

$now = new DateTime();
$later = new DateTime();
$later = $later->modify('+15 minutes');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setEndCustomerTimeWindowStart(new DateTime())
    ->setEndCustomerTimeWindowEnd($later);

// Adding an access code if needed.
//->addAccessCode('ABC-abc-1234', AccessCodesTypes::SCAN_BARCODE, 'A title', 'Instructions');

$createdJob = $client->createJob($job);
if ($createdJob instanceof \Stuart\Job) {
    print "\n" . "Job is created in Stuart. Its ID is: " . $createdJob->getId() . "\n";
} else {
    print "\n" . "There was an error creating the job" . "\n";
    print_r($createdJob);
}
