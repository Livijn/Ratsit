# Ratsit/Checkbiz API wrapper

Fork of `jongotlin/ratsit`.

## Installation
```bash
$ composer require flivijn/ratsit
```

## Usage
```php
$token = '****';
$ratsit = new \livijn\Ratsit\Ratsit($token);
$ratsit->setHttpClient($client); // $client is a \Http\Client\HttpClient
$persons = $ratsit->searchPerson('Per Fredrik', 'EKEBY');
$person = $ratsit->findPersonBySocialSecurityNumber('194107081111');
```
