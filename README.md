Installation:

`composer require admn/admn-php`

Usage:

```
use Admn\Admn\AuditLogger;

// Set API Token
AuditLogger::setCredentials($token, $secret);

// Full method definition
AuditLogger::create($actor, $action, $tags, $context);

// For a simple action logging
AuditLogger::create('id:'.$user->id, $action);

// If you wish to add tags
AuditLogger::create('email:john@doe.com', $action, ['type:account_update','ip:123.123.123.123']]);

// If you wish to add context
AuditLogger::create('phone:123-123-1234', $action, [], $contextData]);

// If you wish to add tags and context
AuditLogger::create('phone:123-123-1234', $action, ['type:account_update'], $contextData]);

// Or for a more structured call
AuditLogger::new()
    ->actor('id:123')
    ->action('Updated contact details')
    ->tags(['ip:123.123.123.123'])
    ->context([
        'updated_contact_details' => [
            'first_name' => 'Bob',
            'email' => 'bob@builder.com'
        ]
    ])
    ->save();
```

Update/Add Actors

```
use Admn\Admn\ActorSync;
use Admn\Admn\AuditLogger;

// Set API Token
AuditLogger::setCredentials($token, $secret);

// Update or Create an actor in the system
ActorSync::single([
    'display' => 'John Doe',
    'identifiers' =>[
        [
            'key' => 'id',
            'value' => 123
        ],
        [
            'key' => 'email',
            'value' => 'john@doe.com'
        ]
    ]
]);

ActorSync::bulk([
    [
        'display'     => 'John Doe',
        'identifiers' => [
            [
                'key'   => 'id',
                'value' => 123,
            ],
            [
                'key'   => 'email',
                'value' => 'john@doe.com',
            ],
        ],
    ],
    [
        'display'     => 'Jane Doe',
        'identifiers' => [
            [
                'key'   => 'id',
                'value' => 456,
            ],
            [
                'key'   => 'phone',
                'value' => '123-456-7890',
            ],
        ],
    ],
]);
```