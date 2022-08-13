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
     * @var string
     */
    protected string $display = '';

    /**
     * @param array $identifiers
     * @param array $details
     */
    public function __construct(array $identifiers = [], array $details = [], string $display = '')
    {
        foreach ($identifiers as $identifier => $value) {
            $this->setIdentifier($identifier, $value);
        }

        foreach ($details as $detail => $value) {
            $this->setDetail($detail, $value);
        }

        if (empty($display) === false) {
            $this->setDisplay($display);
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

    /**
     * @param string $display
     * @return $this
     */
    public function setDisplay(string $display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplay()
    {
        return $this->display;
    }
}
