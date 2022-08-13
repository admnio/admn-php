<?php

namespace Admn\Admn;

use GuzzleHttp\Client;

/**
 *
 */
class AuditLogger
{
    /**
     * @var Actor|null
     */
    protected $actor = null;
    /**
     * @var
     */
    protected $action;
    /**
     * @var array
     */
    protected $context = [];
    /**
     * @var array
     */
    protected $tags = [];
    /**
     * @var null
     */
    protected $nonce = null;

    /**
     * @var int
     */
    protected $timeout = 2;

    /**
     * @var string[]
     */
    static $credentals = ['token' => '', 'secret' => ''];
    /**
     *
     */
    const INTAKE_HOST = 'https://api.admn.io';
    /**
     *
     */
    const INTAKE_PATH = '/v1/actions';

    /**
     * @param Actor $actor
     */
    public function __construct(Actor $actor)
    {
        $this->actor = $actor;
    }

    /**
     * @return AuditLogger
     */
    public static function make(Actor $actor)
    {
        return (new self($actor));
    }

    /**
     * @param $token
     * @param $secret
     */
    public static function setCredentials($token, $secret)
    {
        AuditLogger::$credentals = [
            'token' => $token,
            'secret' => $secret
        ];
    }

    /**
     * @return bool
     */
    public function areCredentialsSet()
    {
        return empty(AuditLogger::$credentals['token']) === false
            && empty(AuditLogger::$credentals['secret']) === false;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @param $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @param $nonce
     * @return $this
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     *
     */
    public function save()
    {
        if ($this->areCredentialsSet() === false) {
            return [
                'status' => 422,
                'response' => 'Credentials are not set.',
            ];
        }


        $client = new Client([
            'headers' => [
                'X-Token' => AuditLogger::$credentals['token'],
                'X-Secret' => AuditLogger::$credentals['secret'],
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'connect_timeout' => $this->timeout,
            'timeout' => $this->timeout,
        ]);

        try {
            $response = $client->post((getenv('ADMN_INTAKE_HOST') ?: AuditLogger::INTAKE_HOST) . AuditLogger::INTAKE_PATH, [
                'json' => [
                    'actor' => [
                        'identifiers' => $this->actor->getIdentifiers(),
                        'details' => $this->actor->getDetails(),
                        'display' => $this->actor->getDisplay(),
                    ],
                    'action' => $this->action,
                    'context' => $this->context,
                    'tags' => $this->tags
                ]
            ]);
        } catch (\Exception $e) {
            return [
                'status' => 422,
                'response' => $e->getMessage(),
            ];
        }

        return [
            'status' => $response->getStatusCode(),
            'response' => $response->getBody()->getContents(),
        ];
    }

    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }
}
