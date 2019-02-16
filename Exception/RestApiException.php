<?php

namespace Onixcat\Bundle\RestApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RestApiException extends HttpException
{
    private $messages;

    public function __construct(array $messages, \Exception $previous = null, $code = 0)
    {
        $this->messages = $messages;
        parent::__construct(422, 'Invalid request. ', $previous, [], $code);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
