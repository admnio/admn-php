<?php

namespace Admn\Admn;

/**
 *
 */
class Actor
{
    /**
     * @var array
     */
    protected array $identifiers = [];
    /**
     * @var array
     */
    protected array $details = [];

    /**
     * @param array $identifiers
     * @param array $details
     */
    public function __construct(array $identifiers = [], array $details = [])
    {
        foreach ($identifiers as $identifier => $value) {
            $this->setIdentifier($identifier, $value);
        }

        foreach ($details as $detail => $value) {
            $this->setDetail($detail, $value);
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setIdentifier(string $key, string $value)
    {
        $this->identifiers[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setDetail(string $key, string $value)
    {
        $this->details[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}
