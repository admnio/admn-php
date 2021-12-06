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
     * @var string[]
     */
    static $credentals = ['token' => '', 'secret' => ''];

    /**
     *
     */
    const INTAKE_HOST = 'https://intake.auditit.app';

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
            'token'  => $token,
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
            ]
        ]);

        try {
            $response = $client->post((getenv('AUDITIT_INTAKE_URL') ?: AuditLogger::INTAKE_HOST), [
                'json' => [
                    'actor'    => $this->actor,
                    'action'   => $this->action,
                    'entities' => $this->entities,
                    'context'  => $this->context,
                    'tags'     => $this->tags
                ]
            ]);
        }catch(\Exception $e){
            return [
                'status'   => 500,
                'response' => $e->getMessage(),
            ];
        }

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
