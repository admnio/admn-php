# ADMN.io PHP SDK

A simple wrapper for [ADMN.io](https://admn.io) API written in PHP.

## Features

- Log action as entity (User, Customer, Employee, etc.)

## Requirements

- PHP 7+

## Installation

Via Composer.

Installation:

```bash 
composer require admn/admn-php
```

## Usage:

```php
use Admn\Admn\AuditLogger;
use Admn\Admn\Actor;

// Set API Token Globally
AuditLogger::setCredentials($token, $secret);

// Create Actor Identifier Object
$actor = (new Actor())->setIdentifier(email, 'john@doe.com')->setDisplay('John Doe';

// Send Action
 return AuditLogger::make($actor)
        )->setAction('Updated a user record')
            ->setTags(['user:123','user-update'])
            ->setContext([
               'key' => 'email',
               'original_value' => 'jane@google.com',
               'updated_value' => 'jane@doe.com',
            ])
            ->save();
```
