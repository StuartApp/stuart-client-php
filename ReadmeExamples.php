<?php

namespace Stuart\Tests;


class ReadmeExamples extends \PHPUnit\Framework\TestCase
{
    private $client;
    private $httpClient;

    public function setUp()
    {
        $environment = \Stuart\Infrastructure\Environment::SANDBOX;
        $api_client_id = 'c6058849d0a056fc743203acb8e6a850dad103485c3edc51b16a9260cc7a7688'; // can be found here: https://admin-sandbox.stuart.com/client/api
        $api_client_secret = 'aa6a415fce31967501662c1960fcbfbf4745acff99acb19dbc1aae6f76c9c619'; // can be found here: https://admin-sandbox.stuart.com/client/api
        $authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

        $this->httpClient = new \Stuart\Infrastructure\HttpClient($authenticator);
        $this->client = new \Stuart\Client($this->httpClient);
    }

    public function test_minimalist()
    {
        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small');
        $this->client->createJob($job);
    }

    public function test_minimalist_ttype()
    {
        $job = new \Stuart\Job();
        $job->setTransportType('bike');
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris');
        $this->client->createJob($job);
    }

    public function test_complete()
    {
        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris')
            ->setComment('Wait outside for an employee to come.')
            ->setContactCompany('KFC Paris Barbès')
            ->setContactFirstName('Martin')
            ->setContactLastName('Pont')
            ->setContactPhone('+33698348756');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small')
            ->setComment('code: 3492B. 3e étage droite. Sonner à Durand.')
            ->setContactCompany('Durand associates.')
            ->setContactFirstName('Alex')
            ->setContactLastName('Durand')
            ->setContactPhone('+33634981209')
            ->setPackageDescription('Pizza box.')
            ->setClientReference(uniqid('php_client', true)); // Must be unique
        $this->client->createJob($job);
    }

    public function test_complete_pickup_at()
    {
        $job = new \Stuart\Job();
        $pickupAt = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $pickupAt->add(new \DateInterval('PT2H'));
        $job->addPickup('46 Boulevard Barbès, 75018 Paris')
            ->setPickupAt($pickupAt)
            ->setComment('Wait outside for an employee to come.')
            ->setContactCompany('KFC Paris Barbès')
            ->setContactFirstName('Martin')
            ->setContactLastName('Pont')
            ->setContactPhone('+33698348756');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small')
            ->setComment('code: 3492B. 3e étage droite. Sonner à Durand.')
            ->setContactCompany('Durand associates.')
            ->setContactFirstName('Alex')
            ->setContactLastName('Durand')
            ->setContactPhone('+33634981209')
            ->setPackageDescription('Pizza box.')
            ->setClientReference(uniqid('php_client', true)); // Must be unique
        $this->client->createJob($job);
    }

    public function test_complete_dropoff_at()
    {
        $job = new \Stuart\Job();
        $dropoffAt = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $dropoffAt->add(new \DateInterval('PT2H'));
        $job->addPickup('46 Boulevard Barbès, 75018 Paris')
            ->setComment('Wait outside for an employee to come.')
            ->setContactCompany('KFC Paris Barbès')
            ->setContactFirstName('Martin')
            ->setContactLastName('Pont')
            ->setContactPhone('+33698348756');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setDropoffAt($dropoffAt)
            ->setPackageType('small')
            ->setComment('code: 3492B. 3e étage droite. Sonner à Durand.')
            ->setContactCompany('Durand associates.')
            ->setContactFirstName('Alex')
            ->setContactLastName('Durand')
            ->setContactPhone('+33634981209')
            ->setPackageDescription('Pizza box.')
            ->setClientReference(uniqid('php_client', true)); // Must be unique
        $this->client->createJob($job);
    }

    public function test_complete_stacking()
    {
        $job = new \Stuart\Job();
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
            ->setPackageDescription('Red packet.')
            ->setClientReference(uniqid('php_client', true)); // Must be unique;
        $job->addDropOff('12 avenue claude vellefaux, 75010 Paris')
            ->setPackageType('small')
            ->setComment('code: 92A42. 2e étage gauche')
            ->setContactFirstName('Maximilien')
            ->setContactLastName('Lebluc')
            ->setContactPhone('+33632341209')
            ->setPackageDescription('Blue packet.')
            ->setClientReference(uniqid('php_client', true)); // Must be unique
        $this->client->createJob($job);
    }

    public function test_get_a_job()
    {
        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small');
        $new_job = $this->client->createJob($job);

        $jobId = $new_job->getId();
        $this->client->getJob($jobId);

        //
        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small');
        $jobWithRoute = $this->client->createJob($job);
        $jobWithRoute->getDeliveries();
    }

    public function test_custom_request()
    {
        $apiResponse = $this->httpClient->performGet('/v2/jobs?page=1');

        $apiResponse->success();
        $apiResponse->getBody();
    }

    public function test_get_a_pricing()
    {
        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small');

        $pricing = $this->client->getPricing($job);
        print_r($pricing->amount);
        print_r($pricing->currency);
    }

    public function test_get_an_eta()
    {
        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small');

        $pricing = $this->client->getEta($job);
    }

    public function test_cancel_a_job()
    {
        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small');

        $job = new \Stuart\Job();
        $job->addPickup('46 Boulevard Barbès, 75018 Paris');
        $job->addDropOff('156 rue de Charonne, 75011 Paris')
            ->setPackageType('small');
        $new_job = $this->client->createJob($job);

        $jobId = $new_job->getId();
        $this->client->cancelJob($jobId);
    }

    private function dropoff($address, $dropoffAtAsText)
    {
        $dropoff = new \Stuart\DropOff();
        $dropoff->setAddress($address)
            ->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', $dropoffAtAsText))
            ->setPackageType('small');
        return $dropoff;
    }
}
