#### 3.6.10

1. Removes prints unless explicitly asking for them

#### 3.6.9

1. Allows the creation of jobs using Latitude and Longitude. More information about this [here](https://community.stuart.engineering/t/job-creation-with-lat-long-coordinates/1436)

#### 3.6.8

1. Fixes a race condition when writing the token in disk

#### 3.6.7

1. Add access code method when adding drop offs

#### 3.6.6

1. Support Guzzle v7
2. Bug-fixes in PHPUnit tests

#### 3.6.5

1. Allows to pass a phone to validate addresses

#### 3.6.4

1. Moved `private` methods to `protected` in `Authenticator` class to enabled better extending.

####3.6.3

1. Fixes typo in sandbox API url

####3.6.2

1. Updates sandbox API url

####3.6.1

1. Does not expose fleets object if array is empty.

####3.6.0

1. Adds Job#setFleets method
2. Adds DropOff#setEndCustomerTimeWindowStart method
3. Adds DropOff#setEndCustomerTimeWindowEnd method
4. Adds DiskCache class
5. Uses GuzzleHttp#getStatusCode instead of GuzzleHttp#success() as it's not available anymore
6. Bumps PHP version to 7.1
