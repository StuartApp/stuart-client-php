####3.6.1
1. Does not expose fleets object if array is empty.

####3.6.0
1. Adds Job#setFleets method
2. Adds DropOff#setEndCustomerTimeWindowStart method
3. Adds DropOff#setEndCustomerTimeWindowEnd method
4. Adds DiskCache class
5. Uses GuzzleHttp#getStatusCode instead of GuzzleHttp#success() as it's not available anymore
6. Bumps PHP version to 7.1