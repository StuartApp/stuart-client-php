[ ![Codeship Status for StuartApp/stuart-client-php](https://app.codeship.com/projects/724b1210-3725-0135-3056-466529bde11a/status?branch=master)](https://app.codeship.com/projects/227364)

# Stuart PHP Client
For a complete documentation of all endpoints offered by the Stuart API, you can visit [Stuart API documentation](https://stuart.api-docs.io).

## Install
Via Composer:

``` bash
$ composer require stuartapp/stuart-client-php
```

## Usage

1. [Initialize Client](#initialize-client)
2. [Create a Job](#create-a-job)
    1. [Minimalist](#minimalist)
    2. [Complete](#complete)
        1. [With scheduling at pickup](#with-scheduling-at-pickup)
        2. [With stacking (multi-drop)](#with-stacking-multi-drop)
3. [Get a Job](#get-a-job)
4. [Cancel a Job](#cancel-a-job)
5. [Validate a Job](#validate-a-job)
6. [Cancel a delivery](#cancel-a-delivery)
7. [Get a pricing](#get-a-pricing)
8. [Get a job eta to pickup](#get-a-job-eta-to-pickup)
9. [Custom requests](#custom-requests)

### Initialize client

```php
$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '65176d7a1f4e734f6723hd690825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd1jdng8ef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

$httpClient = new \Stuart\Infrastructure\HttpClient($authenticator)

$client = new \Stuart\Client($httpClient);
```

You can also pass your own Guzzle client instance to the `\Stuart\HttpClient` constructor:

```php
$guzzleClient = new \Guzzle\Client();
$httpClient = new \Stuart\Infrastructure\HttpClient($authenticator, $guzzleClient);
```

This can be useful if you need to attach middlewares to the Guzzle client used by the Stuart client.

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

$pickupAt = new \DateTime('now', new DateTimeZone('Europe/London'));
$pickupAt->add(new \DateInterval('PT2H'));

$job->addPickup('46 Boulevard Barbès, 75018 Paris')
    ->setPickupAt($pickupAt);

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');
    
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
