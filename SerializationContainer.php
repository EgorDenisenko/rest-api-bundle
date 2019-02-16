<?php

namespace Onixcat\Bundle\RestApiBundle;

/**
 * Class SerializationContainer
 * @package Onixcat\Bundle\RestApiBundle
 */
class SerializationContainer
{
    /**
     * Data section of response document
     *
     * @var mixed
     */
    private $data;

    /**
     * Serialization groups
     *
     * @var array
     */
    protected $groups = [];

    public function __construct($data, array $groups = [])
    {
        $this->data = $data;
        $this->groups = $groups;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param string $group
     * @return SerializationContainer
     */
    public function addGroup(string $group): self
    {
        if (!in_array($group, $this->groups, true)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return ResponseDocument
     */
    public function getResponseDocument(): ResponseDocument
    {
        return $this->getData() instanceof ResponseDocument ? $this->getData() : new ResponseDocument($this->getData());
    }
}
