<?php

namespace Onixcat\Bundle\RestApiBundle;

/**
 * REST api response document
 *
 * @see https://onixcat.atlassian.net/wiki/pages/viewpage.action?pageId=4718606
 *
 * @package Onixcat\Bundle\RestApiBundle
 */
class ResponseDocument
{
    /**
     * Data section of response document
     *
     * @var mixed
     */
    protected $data;

    /**
     * Third-party meta information of the response document
     *
     * @var mixed
     */
    protected $meta;

    /**
     * List info|error messages to the client
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Pagination information
     *
     * @var array
     */
    protected $page;

    /**
     * Additional documents by type
     *
     * @var array
     */
    protected $included;

    /**
     * Query time
     *
     * @var int
     */
    protected $t;

    /**
     * @param $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Add meta data
     *
     * @param string $key
     * @param mixed $data
     *
     * @return ResponseDocument
     */
    public function addMeta(string $key, $data): self
    {
        $this->meta[$key] = $data;

        return $this;
    }

    /**
     * Add response document message
     *
     * @param int $code
     * @param string $title
     * @param string|null $details
     * @return ResponseDocument
     */
    public function addMessage(int $code, string $title, string $details = null): self
    {
        $this->messages[] = [
            'code' => $code,
            'title' => $title,
            'detail' => $details,
        ];

        return $this;
    }

    /**
     * @param int $offset
     * @param int $count
     * @param int $total
     *
     * @return ResponseDocument
     */
    public function setPage(int $offset, int $count, int $total): self
    {
        $this->page = [
            'offset' => $offset,
            'count' => $count,
            'total' => $total,
        ];

        return $this;
    }

    /**
     * Add to list of included items
     *
     * @param string $type
     * @param array $items
     *
     * @return ResponseDocument
     */
    public function addIncluded(string $type, array $items): self
    {
        $this->included[$type] = $items;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return array
     */
    public function getPage(): ?array
    {
        return $this->page;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function getIncluded(): ?array
    {
        return $this->included;
    }

    /**
     * @param int|null $time
     */
    public function setT(int $time): void
    {
        $this->t = $time;
    }

    /**
     * @return int
     */
    public function getT(): ?int
    {
        return $this->t;
    }
}
