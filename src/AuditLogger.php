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
    const INTAKE_PATH = '/nexus/v1/actions';

    /**
     * @return AuditLogger
     */
    public static function make()
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

    public function areCredentialsSet()
    {
        return empty(AuditLogger::$credentals['token']) === false
            && empty(AuditLogger::$credentals['secret']) === false;
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
        $builder = AuditLogger::make()
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
        if ($this->areCredentialsSet() === false) {
            return [
                'status'   => 422,
                'response' => 'Credentials are not set.',
            ];
        }


        $client = new Client([
            'headers'         => [
                'NexusToken'   => AuditLogger::$credentals['token'],
                'NexusSecret'  => AuditLogger::$credentals['secret'],
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'connect_timeout' => 2,
            'timeout'         => 2,
        ]);

        try {
            $response = $client->post((getenv('ADMN_INTAKE_HOST') ?: AuditLogger::INTAKE_HOST) . AuditLogger::INTAKE_PATH, [
                'json' => [
                    'actor'   => $this->actor,
                    'action'  => $this->action,
                    'context' => $this->context,
                    'tags'    => $this->tags
                ]
            ]);
        } catch (\Exception $e) {
            return [
                'status'   => 422,
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
     * @param string $nonce
     */
    public function nonce(string $nonce)
    {
        $this->nonce = $nonce;
    }
}
