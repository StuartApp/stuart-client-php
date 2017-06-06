# Stuart PHP Client
For more details, visit [Stuart API documentation](https://docs.stuart.com).

## Install
Via Composer (**Note:** will be available when published to [packagist](https://packagist.org)).

``` bash
$ composer require stuartapp/stuart-client-php
```

## Usage

### Stuart Client

```php
$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '65176d7a1f4e734f6723hd690825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd1jdng8ef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

$client = new \Stuart\Client($authenticator);
```

### Create a Job

#### Simple
```php
$job = new \Stuart\Job();
$job->addPickup('46 Boulevard Barbès, 75018 Paris');
$job->addDropOff('156 rue de Charonne, 75011 Paris');

$client->createJob($job);
```

#### Complete

```php
$pickupAt = new \DateTime('now', new DateTimeZone('Europe/London'));
$pickupAt->add(new \DateInterval('PT2H'));

$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris')
    ->setPickupAt($pickupAt)
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
    ->setPackageType('small');
```