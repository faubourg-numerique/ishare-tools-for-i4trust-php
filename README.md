# iShare Tools For i4Trust PHP

This repository is a PHP library designed to manage [iShare](https://dev.ishare.eu/index.html) compliant tokens and requests.<br>
It has been developped by FIWARE iHub / DIH Faubourg Numérique, in the context of the CO2-Mute experimentation, supported by the H2020 project [i4Trust](https://i4trust.org/).

## Getting Started

### Prerequisites

- PHP with curl
- Composer

## Usage

### Generate iShare JWT

```php
$iShareJWT = IShareToolsForI4Trust::generateIShareJWT($config);
```

Structure of the **config** object:

| Name       | Type         | Example                                                     |
|------------|--------------|-------------------------------------------------------------|
| issuer     | string       | EU.EORI.FR00000000000000                                    |
| subject    | string       | EU.EORI.FR00000000000000                                    |
| audience   | string/array | EU.EORI.FR00000000000000                                    |
| x5c        | array        | ["...", "...", "..."]                                       |
| privateKey | string       | -----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY----- |


### Get access token

```php
$accessToken = IShareToolsForI4Trust::getAccessToken($config);
```

Structure of the **config** object:

| Name       | Type   | Example                      |
|------------|--------|------------------------------|
| arTokenURL | string | https://example/oauth2/token |
| clientId   | string | EU.EORI.FR00000000000000     |
| iShareJWT  | string | ...                          |

### Get delegation token

```php
$delegationToken = IShareToolsForI4Trust::getDelegationToken($config);
```

Structure of the **config** object:

| Name              | Type   | Example                           |
|-------------------|--------|-----------------------------------|
| arDelegationURL   | string | https://example.com/ar/delegation |
| delegationRequest | object | { delegationRequest: ... }        |
| accessToken       | string | ...                               |

<hr>

### Create policy

```php
IShareToolsForI4Trust::getDelegationToken($config);
```

Structure of the **config** object:

| Name               | Type   | Example                       |
|--------------------|--------|-------------------------------|
| arPolicyURL        | string | https://example.com/ar/policy |
| delegationEvidence | object | { delegationEvidence: ... }   |
| accessToken        | string | ...                           |

### Decode JWT

```php
$decodedJWT = IShareToolsForI4Trust::decodeJWT($encodedJWT, $privateKey);
```
