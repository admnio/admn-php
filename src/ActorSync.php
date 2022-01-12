<?php

namespace Admn\Admn;

use GuzzleHttp\Client;

/**
 *
 */
class ActorSync
{
    /**
     * @var array
     */
    protected $actor = [
        'display' => '',
        'identifiers' => []
    ];


    /**
     * @param array $actor
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    static function single(array $actor){
        return self::send('single',$actor);
    }

    /**
     * @param array $actorBatch
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    static function bulk(array $actorBatch){
        return self::send('batch',$actorBatch);
    }

    /**
     * @param string $type
     * @param array $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    static function send(string $type,array $data){
        $token  = AuditLogger::$credentals['token'];
        $secret = AuditLogger::$credentals['secret'];

        if (empty($token) || empty($secret)) {
            throw new \Exception('Missing AuditIt Credentials');
        }

        $client = new Client([
            'headers' => [
                'ApiToken'     => $token,
                'ApiSecret'    => $secret,
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'connect_timeout' => 2,
            'timeout' => 2,
        ]);

        $path = $type === 'single' ? '/api/actor-sync' : '/api/actor-batch-sync';

        $response = $client->post((getenv('ADMN_INTAKE_HOST') ?: AuditLogger::INTAKE_HOST) . $path, [
            'json' => $data
        ]);

        return $response->getBody()->getContents();
    }
}
