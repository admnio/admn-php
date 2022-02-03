<?php

namespace Admn\Admn;

use GuzzleHttp\Client;

/**
 *
 */
class AuditLogger
{
    /**
     * @var array
     */
    protected $actor = [];
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
     * @var array
     */
    protected $entities = [];

    /**
     * @var null
     */
    protected $nonce = null;

    /**
     * @var string[]
     */
    static $credentals = ['token' => '', 'secret' => ''];

    /**
     *
     */
    const INTAKE_HOST = 'https://hub.admn.io';

    /**
     *
     */
    const INTAKE_PATH = '/nexus/v1/intake';

    /**
     * @return AuditLogger
     */
    public static function new()
    {
        return (new self());
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
     * @param string|array $actor
     * @param $action
     * @param array $tags
     * @param array $context
     * @return array|void
     */
    public static function create($actor, $action, $tags = [], $context = [])
    {
        $builder = AuditLogger::new()
            ->action($action)
            ->actor($actor);


        foreach ($tags as $tag) {
            $builder->addTag($tag);
        }


        if (empty($context) === false) {
            $builder->context($context);
        }

        return $builder->save();
    }

    /**
     *
     */
    public function save()
    {
        $token = AuditLogger::$credentals['token'];
        $secret = AuditLogger::$credentals['secret'];

        if (empty($token) || empty($secret)) {
            throw new \Exception('Missing ADMN Credentials');
        }


        $client = new Client([
            'headers' => [
                'ApiToken' => $token,
                'ApiSecret' => $secret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'connect_timeout' => 2,
            'timeout' => 2,
        ]);

        try {
            $response = $client->post((getenv('ADMN_INTAKE_HOST') ?: AuditLogger::INTAKE_HOST) . AuditLogger::INTAKE_PATH, [
                'json' => [
                    'actor' => $this->actor,
                    'action' => $this->action,
                    'entities' => $this->entities,
                    'context' => $this->context,
                    'tags' => $this->tags
                ]
            ]);
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'response' => $e->getMessage(),
            ];
        }

        return [
            'status' => $response->getStatusCode(),
            'response' => $response->getBody()->getContents(),
        ];
    }

    /**
     * @param $actorSourceIdentifier
     * @param null $displayAs
     * @param string $type
     * @return $this
     */
    public function actor($actor)
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * @param $action
     * @return $this
     */
    public function action($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param array $context
     * @return $this
     */
    public function context(array $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @param $tag
     * @return $this
     */
    public function addTag($tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @param $entitySourceIdentifier
     * @param $displayAs
     * @param null $type
     * @return $this
     */
    public function addEntity($entity)
    {
        $this->entities[] = $entity;

        return $this;
    }

    /**
     * @param string $nonce
     */
    public function nonce(string $nonce)
    {
        $this->nonce = $nonce;
    }
}
