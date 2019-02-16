<?php

namespace Onixcat\Bundle\RestApiBundle\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class DTOMap
{
    /** @var bool */
    private $enableValidation;

    public function __construct(array $data)
    {
        $this->enableValidation = array_key_exists('enableValidation', $data) ? $data['enableValidation'] : true;
    }

    /**
     * @return bool
     */
    public function isEnableValidation(): bool
    {
        return $this->enableValidation;
    }
}
