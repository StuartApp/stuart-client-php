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
