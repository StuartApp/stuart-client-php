<?php
require __DIR__ . '/vendor/autoload.php';

use Stuart\AccessCodesTypes;

print "Welcome to the Stuart PHP Library Demo\n";

print "Setting up sandbox credentials...\n";

$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '85ff124a4536120d9f070f44956c2f8e37f4cf0f42204f36ed81a6e37e3cd802';
$api_client_secret = 'd50f52c3f7045fdd6625bb5e7b2f1a538c2f260b7d2411a4319dea81d97204ff';
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
    ->setEndCustomerTimeWindowEnd($later)
    ->addAccessCode('73764', AccessCodesTypes::SCAN_BARCODE, 'Some title', 'Some instructions');

$createdJob = $client->createJob($job);
if ($createdJob instanceof \Stuart\Job) {
    print "\n" . "Job is created in Stuart. Its ID is: " . $createdJob->getId() . "\n";
} else {
    print "\n" . "There was an error creating the job" . "\n";
    print_r($createdJob);
}
