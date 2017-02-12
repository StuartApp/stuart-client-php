# Stuart PHP Client
For more details, visit [Stuart API documentation](https://docs.stuart.com).

## Install
Via Composer

To be updated once published to [packagist](https://packagist.org/)
``` bash
$ composer require stuartapp/stuart-client-php
```

## Usage
### Authenticator

To be updated with new `Authenticator` and new static method to return Stuart API URL.
```php
$api_client_id = '65176d7a1f4e734f6723hd690825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd1jdng8ef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api

$authenticator = new \Stuart\Infrastructure\Authenticator(Environment::SANDBOX, $api_client_id, $api_client_secret);
```

### HttpClient
```php
$httpClient = new \Stuart\Infrastructure\HttpClient(authenticator);
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

$repository = new \Stuart\Repository\JobRepository($httpClient);
$stuartJobId = $repository.save($job);
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

$job = new \Stuart\Job($origin, $destination, $package_size);
$pickupAt = new \DateTime('now');
$pickupAt->add(new \DateInterval('PT1H')); // one hour from now
$job->schedulePickupAt($pickupAt);

$repository = new \Stuart\Repository\JobRepository($httpClient);
$stuartJobId = $repository.save($job);
```

### Get a Job

```php
$stuartJob = repository.get($stuartJobId);

$stuartJob->getId(); // 650034
$stuartJob->getOrigin()['address']; // 5 rue sidi brahim, 75012 Paris
$stuartJob->getDestination()['first_name']; // Jean-Marc
$stuartJob->getPackageSize(); // small
$stuartJob->getTrackingUrl(); // https://track-sandbox.stuart.com/tracking/40353/8be32c5160f7945ce1ec6484f0ee4e50
$stuartJob->getPickupAt();
```
