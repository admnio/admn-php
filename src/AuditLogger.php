<?php

namespace Auditit\Auditit;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * @return AuditLogger
     */
    public static function new()
    {
        return (new self());
    }

    /**
     * @param $key
     * @param $secret
     */
    public static function setCredentials($key, $secret)
    {
        if(defined("AUDITIT_API_TOKEN") === false && defined("AUDITIT_API_SECRET") === false) {
            define("AUDITIT_API_TOKEN", $key);
            define("AUDITIT_API_SECRET", $secret);
        }
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
        $token  = getenv('AUDITIT_API_TOKEN') ?: (defined(AUDITIT_API_TOKEN) ? AUDITIT_API_TOKEN : null);
        $secret = getenv('AUDITIT_API_SECRET') ?: (defined(AUDITIT_API_SECRET) ? AUDITIT_API_SECRET : null);

        if (empty($token) || empty($secret)) {
            throw new \Exception('Missing AuditIt Credentials');
        }


        $client = new Client([
            'headers' => [
                'ApiToken'     => $token,
                'ApiSecret'    => $secret,
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        $response = $client->post((getenv('AUDITIT_API_HOST') ?: 'https://auditit.app') . '/api/intake', [
            'json' => [
                'actor'    => $this->actor,
                'action'   => $this->action,
                'entities' => $this->entities,
                'context'  => $this->context,
                'tags'     => $this->tags
            ]
        ]);

        return [
            'status'   => $response->getStatusCode(),
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
