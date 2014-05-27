Bdcc_Status
====

`Bdcc_Status` contains constants for status codes used in Bradley Dyer projects. They are modelled on HTTP status codes as these are well defined and varied.

## Usage and Examples

1) Include class

Preferably use PSR-0 compatible notation in order to make sure class with similar name as not been already registered: `Bdcc_Status` instead of `Status` (that is quite vague).

```php
    use Bdcc\Status as Bdcc_Status;
```

2) Use Bdcc_Status constant to check your HTTP responses:

```php

    // Check response is successful
    if ($response == Bdcc_Status::HTTP_OK) {
        ...
    }

    // Check response is not server error
    elseif ($response != Bdcc_Status::HTTP_INTERNAL_SERVER_ERROR) {
        ...
    }
``''

3) Use Bdcc_Status to get response label

```php
    // Return `Not Found` response
    $responseCode = $respose->getResponseCode(); // 404

    return Bdcc_Status::getStatusLabel($responseCode);
```

4) Check for expected response type (range)

```php
    // Sending Api request and expecting
    // Either 200 - OK, or 201 - Created, or 202 - Accepted, or 204 - No Content
    // Depending on server response

    $responseCode = $response->getCode();

    // Now check if any Success status have been returned
    if (Bdcc_Status::isSuccess($responseCode)) {
        ...
    }

    // Now check for any error status have been returned
    // Client Errors: 4xx
    // Server Errors: 5xx
    elseif (Bdcc_Status::isClientError($responseCode) || Bdcc_Status::isServerError($responseCode)) {
        ...
    }
```
