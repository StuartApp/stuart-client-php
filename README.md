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
        1. [With scheduling at drop off](#with-scheduling-at-dropoff)
        2. [With stacking (multi-drop)](#with-stacking-multi-drop)
3. [Get a Job](#get-a-job)
4. [Cancel a Job](#cancel-a-job)
5. [Validate a Job](#validate-a-job)
6. [Cancel a delivery](#cancel-a-delivery)
7. [Get a pricing](#get-a-pricing)
8. [Get a job eta to pickup](#get-a-job-eta-to-pickup)
9. [Custom requests](#custom-requests)
10. [Group orders to create Stacked Jobs](#group-orders-experimental)

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

This can be useful if you need to attach middlewares to the Guzzle client.

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

#### With scheduling at dropoff

For more information about job scheduling you should [check our API documentation](https://stuart.api-docs.io/v2/jobs/scheduling-a-job).

Please note that this feature can only be used with only one dropoff.

```php
$job = new \Stuart\Job();

$dropoffAt = new \DateTime('now', new DateTimeZone('Europe/London'));
$dropoffAt->add(new \DateInterval('PT2H'));

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setDropoffAt($dropoffAt);
    
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

### Experimental: group orders

Currently, the multi-drop feature available within the Stuart API allows you to send up to 8 dropoffs, and the Stuart platform will find the best route between these points. But it won't allow you to group orders.

That's what this **experimental** feature is trying to solve. Before creating jobs, you are able to call the `findRounds` methods, which will give you back Jobs and waste (dropoffs that haven't been assigned to a route).

Here you can find an example of how to use it:

```php
$pickup = (new \Stuart\Pickup())->setAddress('26 rue taine 75012 paris');

// You need to create an array of Dropoff, by specifying the address and the beginning of the delivery slot you are offering your customers.
$dropoffs = [
    (new \Stuart\Dropoff())->setAddress('23 rue de richelieu 75002 paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:40:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('3 rue d\'edimbourg 75008 paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:45:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('156 rue de charonne 75012 paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('8 rue sidi brahim 75012 paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 14:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('5 passage du chantier 75012 paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('Hôpital Saint-Louis, 75010 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 13:20:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('1 Rue des Deux Gares, 75010 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('137 Rue la Fayette, 75010 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('34 Rue Pierre Semard, 75009 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:00:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('46 Rue Lecourbe, 75015 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('178 Rue Lecourbe, 75015 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 13:00:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('43 Rue des Alouettes 75019 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('50 Rue Durantin, 75018 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 12:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('47-33 Rue des Abbesses, 75018 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 13:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('2 Boulevard de la Villette, 75019 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 14:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('172 Rue de Charonne, 75011 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 15:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('2-10 Passage Courtois, 75011 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 19:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('23 Rue Servan, 75011 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 20:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('71 Rue de la Fontaine au Roi, 75011 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 19:00:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('37 Rue Albert Thomas 75010 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 20:45:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('32-42 Rue du Faubourg Saint-Denis, 75010 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 19:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('12 Rue d\'Uzès, 75002 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 20:39:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('37-23 Rue Danielle Casanova')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 21:00:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('148 Rue de l\'Université, 75007 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 15:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('64-66 Avenue d\'Iéna, 75116 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 18:30:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('12 avenue claude vellefaux 75010 paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 19:00:00'))->setPackageType('small'),
    (new \Stuart\Dropoff())->setAddress('101 Avenue Victor Hugo, 75116 Paris')->setDropoffAt(\DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-25 19:30:00'))->setPackageType('small')
];

$config = array(
    'graphhopper_api_key' => 'your-graphhopper-api-key', // https://graphhopper.com/dashboard/#/api-keys
    'vehicle_count' => 10,
    'max_dropoffs' => 8,
    'slot_size_in_minutes' => 60,
    'max_distance' => 15000
);

$graphHopper = new \Stuart\Routing\GraphHopper($pickup, $dropoffs, $config);
$result = $graphHopper->findRounds();

$pricing = 0;
foreach ($result->jobs as $job) {
    $res = $this->client->getPricing($job); // TODO: Replace with a real call to \Stuart\Client.
    $pricing += $res->amount;
}
print_r('Total pricing with stacking is: ' . $pricing . ', Waste count is: ' . count($result->waste) . '. ');

$pricingNoStacking = 0;
foreach ($dropoffs as $dropoff) {
    $job = new \Stuart\Job();
    $job->pushPickup($pickup);
    $job->pushDropoff($dropoff);
    $res = $this->client->getPricing($job); // TODO: Replace with a real call to \Stuart\Client
    $pricingNoStacking += $res->amount;
}
print_r('Would have cost you: ' . $pricingNoStacking . ' without stacking/grouping');     
```

After running this example, it will display: `Total pricing with stacking is: 133.35, Waste count is: 1. Would have cost you: 306.25 without stacking/grouping`.
