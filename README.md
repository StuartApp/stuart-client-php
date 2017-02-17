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

$stuartClient = new \Stuart\Client($environment, $api_client_id, $api_client_secret);
```

### Create a Job

```php
$origin = [
    'address'       => '18 rue sidi brahim, 75012 Paris',
    'company'       => 'WeSellWine Inc.',
    'first_name'    => 'Marcel',
    'last_name'     => 'Poisson',
    'phone'         => '0628739512'
];
$destination = [
    'address'       => '5 rue sidi brahim, 75012 Paris',
    'company'       => 'Jean-Marc SAS',
    'first_name'    => 'Jean-Marc',
    'last_name'     => 'Pinchu',
    'phone'         => '0628046934'
];
$package_size = 'small';

$job = new \Stuart\Job($origin, $destination, $package_size);
$stuartJob = $stuartClient->createJob($job);
```

### Create a Scheduled Job

```php
$origin = [
    'address'       => '18 rue sidi brahim, 75012 Paris',
    'company'       => 'WeSellWine Inc.',
    'first_name'    => 'Marcel',
    'last_name'     => 'Poisson',
    'phone'         => '0628739512'
];
$destination = [
    'address'       => '5 rue sidi brahim, 75012 Paris',
    'company'       => 'Jean-Marc SAS',
    'first_name'    => 'Jean-Marc',
    'last_name'     => 'Pinchu',
    'phone'         => '0628046934'
];
$package_size = 'small';

$pickupAt = new \DateTime('now', new DateTimeZone('Europe/London'));
$pickupAt->add(new \DateInterval('PT1H')); // one hour from now

$job = new \Stuart\Job($origin, $destination, $package_size, ['pickup_at' => $pickupAt]);

$stuartJob = $stuartClient->createJob($job);
```

### Get a Job

```php
$stuartJob = $stuartClient->getJob($stuartJobId);

$stuartJob->getId(); // 650034
$stuartJob->getOrigin()['address']; // 5 rue sidi brahim, 75012 Paris
$stuartJob->getDestination()['first_name']; // Jean-Marc
$stuartJob->getPackageSize(); // small
$stuartJob->getTrackingUrl(); // https://track-sandbox.stuart.com/tracking/40353/8be32c5160f7945ce1ec6484f0ee4e50
$stuartJob->getPickupAt();
```
