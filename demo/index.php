<?php
require __DIR__ . '/vendor/autoload.php';

print "Welcome to the Stuart PHP Library Demo\n";

print "Setting up sandbox credentials...\n";

$environment = \Stuart\Infrastructure\Environment::PRODUCTION;
$api_client_id = 'af8dc9697cc09849df942f1ac0bb43737907a3e57be187e02f87edebe43bd602';
$api_client_secret = 'efb1ef250b49c91a0582c24ce31ad4a9335de709daf10297e0d6e531a7e2a4a4';
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret, new \Stuart\Cache\DiskCache("stuart_cache.txt"));

$httpClient = new \Stuart\Infrastructure\HttpClient($authenticator);

$client = new \Stuart\Client($httpClient);

print "Creating a job...\n";
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$now = new DateTime();
$later = new DateTime();
$later = $later->modify('+15 minutes');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setEndCustomerTimeWindowStart(new DateTime())
    ->setEndCustomerTimeWindowEnd($later);

$createdJob = $client->createJob($job);
if ($createdJob instanceof \Stuart\Job) {
    print "\n" . "Job is created in Stuart. Its ID is: " . $createdJob->getId() . "\n";
} else {
    print "\n" . "There was an error creating the job" . "\n";
    print_r($createdJob);
}
