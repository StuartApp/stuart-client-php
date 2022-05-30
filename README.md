[![Codeship Status for StuartApp/stuart-client-php](https://app.codeship.com/projects/aa042f30-b15a-0137-cdf6-4ac72e591a58/status?branch=master)](https://app.codeship.com/projects/363052)

# Stuart PHP Client

For a complete documentation of all endpoints offered by the Stuart API, you can visit [Stuart API documentation](https://stuart.api-docs.io).

### Changelog

Visit [Changelog](CHANGELOG.md)

### Running the demo

```shell script
cd demo
docker build -t stuartphpdemo .
export PATH_TO_PROJECT="/PUT/HERE/YOUR/PATH/TO/PROJECT/stuart-client-php"
docker run -v $PATH_TO_PROJECT/src:/app/vendor/stuartapp/stuart-client-php/src stuartphpdemo
```

## Install

Via Composer:

```bash
$ composer require stuartapp/stuart-client-php
```

## Usage

1. [Initialize Client](#initialize-client)
2. [Create a Job](#create-a-job)
   1. [Minimalist](#minimalist)
   2. [Complete](#complete)
      1. [With scheduling at pickup](#with-scheduling-at-pickup)
      1. [With scheduling at drop off](#with-scheduling-at-dropoff)
      1. [With stacking (multi-drop)](#with-stacking-multi-drop)
3. [Get a Job](#get-a-job)
4. [Cancel a Job](#cancel-a-job)
5. [Validate a Job](#validate-a-job)
6. [Cancel a delivery](#cancel-a-delivery)
7. [Get a pricing](#get-a-pricing)
8. [Get a job eta to pickup](#get-a-job-eta-to-pickup)
9. [Custom requests](#custom-requests)

### Import the library

If composer is not installed, install it:

```shell script
$ curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

Add the library to your project:

```shell script
$ composer require stuartapp/stuart-client-php
```

### Autoloading

In order to load all the classes from this library, just execute the autoload at the beginning of the Stuart code

```php
<?php
require __DIR__ . '/vendor/autoload.php';

$environment = \Stuart\Infrastructure\Environment::SANDBOX;
```

### Get credentials

For Sandbox (testing environment, couriers are bots)
https://dashboard.sandbox.stuart.com/settings/api

For Production (real world, real couriers)
https://dashboard.stuart.com/settings/api

### Initialize client

```php
$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '65176d7a1f4e734f6723hd690825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin.sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd1jdng8ef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin.sandbox.stuart.com/client/api
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

$httpClient = new \Stuart\Infrastructure\HttpClient($authenticator)

$client = new \Stuart\Client($httpClient);
```

You can also pass your own Guzzle client instance to the `\Stuart\HttpClient` constructor:

```php
$guzzleClient = new \Guzzle\Client();
$httpClient = new \Stuart\Infrastructure\HttpClient($authenticator, $guzzleClient);
```

This can be useful if you need to attach middlewares to the Guzzle client.

### Caching

It's highly recommended adding a caching mechanism for the authentication process.
To do so, simply extend the `Psr\SimpleCache\CacheInterface` class and implement your own version.

There's a cache based on disk available out of the box for you to use.
To use it, simply modify the Authentication class initialization and pass the cache implementation in the constructor:

```php
$diskCache = new \Stuart\Cache\DiskCache("stuart_cache.txt");
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret, $diskCache);
```

##### Debugging token cache issues

You can initialize the DiskCache, Authenticator and HttpClient classes by passing a `true` value to the last constructor parameter of these classes.

### Create a Job

**Important**: Even if you can create a Job with a minimal set of parameters, we **highly recommend** that you fill as many information as
you can in order to ensure the delivery process goes well.

#### Minimalist

##### Package size based

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');

$client->createJob($job);
```

##### Transport type based (France only)

```php
$job = new \Stuart\Job();

$job->setTransportType('bike');

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris');

$client->createJob($job);
```

#### Complete

##### Package size based

```php
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
    ->setClientReference('12345678ABCDE'); // Must be unique

$client->createJob($job);
```

##### Transport type based (France only)

```php
$job = new \Stuart\Job();

$job->setTransportType('bike');

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
    ->setPackageDescription('Pizza box.')
    ->setClientReference('12345678ABCDE'); // Must be unique

$client->createJob($job);
```

#### With scheduling at pickup

For more information about job scheduling you should [check our API documentation](https://stuart.api-docs.io/v2/jobs/scheduling-a-job).

```php
$job = new \Stuart\Job();

$pickupAt = new \DateTime('now', new DateTimeZone('Europe/Paris'));
$pickupAt->add(new \DateInterval('PT2H'));

$job->addPickup('46 Boulevard Barbès, 75018 Paris')
    ->setPickupAt($pickupAt);

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');

$client->createJob($job);
```

#### With scheduling at dropoff

For more information about job scheduling you should [check our API documentation](https://stuart.api-docs.io/v2/jobs/scheduling-a-job).

Please note that this feature can only be used with only one dropoff.

```php
$job = new \Stuart\Job();

$dropoffAt = new \DateTime('now', new DateTimeZone('Europe/Paris'));
$dropoffAt->add(new \DateInterval('PT2H'));

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setDropoffAt($dropoffAt);

$client->createJob($job);
```

#### With fleet targeting

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');

$job->setFleets(array(1));

$client->createJob($job);
```

#### With end customer time window information (used for metrics purposes only)

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$now = new DateTime();
$later = new DateTime();
$later = $later->modify('+15 minutes');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setEndCustomerTimeWindowStart(new DateTime())
    ->setEndCustomerTimeWindowEnd($later);

$client->createJob($job);
```

#### With stacking (multi-drop)

##### Package size based

```php
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
    ->setPackageDescription('Red packet.')
    ->setClientReference('12345678ABCDE'); // Must be unique;

$job->addDropOff('12 avenue claude vellefaux, 75010 Paris')
    ->setPackageType('small')
    ->setComment('code: 92A42. 2e étage gauche')
    ->setContactFirstName('Maximilien')
    ->setContactLastName('Lebluc')
    ->setContactPhone('+33632341209')
    ->setPackageDescription('Blue packet.')
    ->setClientReference('ABCDE213124'); // Must be unique

$client->createJob($job);
```

##### Transport type based (France only)

```php
$job = new \Stuart\Job();

$job->setTransportType('bike');

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
    ->setClientReference('12345678ABCDE'); // Must be unique;

$job->addDropOff('12 avenue claude vellefaux, 75010 Paris')
    ->setComment('code: 92A42. 2e étage gauche')
    ->setContactFirstName('Maximilien')
    ->setContactLastName('Lebluc')
    ->setContactPhone('+33632341209')
    ->setPackageDescription('Blue packet.')
    ->setClientReference('ABCDE213124'); // Must be unique

$client->createJob($job);
```

### Get a Job

Once you successfully created a Job you can retrieve it this way:

```php
$jobId = 126532;
$job = $client->getJob($jobId);
```

Or when you create a new Job:

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');

$jobWithRoute = $client->createJob($job);

$jobWithRoute->getDeliveries();
```

The Stuart API determine the optimal route on your behalf,
that's why the `getDeliveries()` method will return an empty
array when the Job has not been created yet. The `getDeliveries()`
method will return an array of `Delivery` as soon as the Job is created.

### Cancel a job

Once you successfully created a Job you can cancel it in this way:

```php
$jobId = 126532;
$result = $client->cancelJob($jobId);
```

The result will hold the boolean value `true` if the job was cancelled. If
there was an error, it will contain an error object.

For more details about how cancellation works, please refer to our [dedicated documentation section](https://stuart.api-docs.io/v2/jobs/job-cancellation).

### Validate a Job

Before creating a Job you can validate it (control delivery area & address format). Validating a Job is **optional** and does not prevent you from creating a Job.

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');

$result = $client->validateJob($job);
```

The result will hold the boolean value `true` if the job is valid. If
there was an error, it will contain an error object.

### Validate an address

We encourage to validate an address to find out if we can pickup / deliver there. Phone number is optional only for those places that the address is specific enough.

```php
$client->validatePickupAddress('Pau Claris, 08037 Barcelona', '+34677777777');
$client->validatePickupAddress('Pau Claris 186, 08037 Barcelona');
$client->validateDropoffAddress('Pau Claris, 08037 Barcelona', '+34677777777');
```

### Cancel a delivery

Once you successfully created a Delivery you can cancel it in this way:

```php
$deliveryId = 126532;
$result = $client->cancelDelivery($deliveryId);
```

### Get a pricing

Before creating a Job you can ask for a pricing. Asking for a pricing is **optional** and does not prevent you from creating a Job.

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');

$pricing = $client->getPricing($job);

$pricing->amount; // example: 11.5
$pricing->currency; // example: "EUR"
```

### Get a job ETA to pickup

Before creating a Job you can ask for an estimated time of arrival at the pickup location (expressed in seconds).
Asking for ETA is **optional** and does not prevent you from creating a job.

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');

$eta = $client->getEta($job);

$eta->eta; // example: 672
```

### Custom requests

You can also send requests on your own without relying on the `\Stuart\Client`.
It allows you to use endpoints that are not yet available on the `\Stuart\Client` and enjoy the `\Stuart\Authenticator`.

```php
$apiResponse = $httpClient->performGet('/v2/jobs?page=1');
$apiResponse->success();
$apiResponse->getBody();
```
