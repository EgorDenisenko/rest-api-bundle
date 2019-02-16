<?php

namespace Onixcat\Bundle\RestApiBundle\Resolver;

use Doctrine\Common\Annotations\Reader;
use Onixcat\Bundle\RestApiBundle\Annotation\DTOMap;
use Onixcat\Bundle\RestApiBundle\DTO\DTOInterface;
use Onixcat\Bundle\RestApiBundle\Exception\RestApiException;
use Onixcat\Bundle\RestApiBundle\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestBinder
 * @package AppBundle\Utils
 */
final class RequestBinder
{
    const KEY = 'requestObject';

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(ValidatorInterface $validator, Serializer $serializer, Reader $reader)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->reader = $reader;
    }

    /**
     *
     *
     * @param Request $request
     * @param callable $action
     */
    public function bind(Request $request, callable $action): void
    {
        $actionReflection = is_array($action)
            ? (new \ReflectionClass($action[0]))->getMethod($action[1])
            : new \ReflectionFunction($action);

        foreach ($this->reader->getMethodAnnotations($actionReflection) as $methodAnnotation) {
            if (!$methodAnnotation instanceof DTOMap) {
                continue;
            }

            foreach ($actionReflection->getParameters() as $argument) {
                if (!$this->isArgumentIsSubtypeOf($argument, DTOInterface::class)) {
                    continue;
                }

                /** @var DTOInterface $object */
                $object = $this->serializer->deserialize(
                    $request->getContent(),
                    $argument->getClass()->getName(),
                    'json'
                );

                if ($methodAnnotation->isEnableValidation()) {
                    $this->validateRequest($object);
                }
                $request->attributes->set($argument->name, $object);
                return;
            }
        }
    }

    /**
     * @param \ReflectionParameter $argument
     * @param $subtype
     * @return bool
     */
    private function isArgumentIsSubtypeOf(\ReflectionParameter $argument, $subtype): bool
    {
        if (!($className = $argument->getClass())) {
            return false;
        }

        return is_a($className->name, $subtype, true);
    }

    /**
     * @param DTOInterface $object
     */
    private function validateRequest(DTOInterface $object): void
    {
        $errors = $this->validator->validate($object);

        if ($errors->count()) {
            foreach ($errors as $err) {
                $messages[] = $err->getMessage();
            }

            throw new RestApiException($messages);
        }
    }
}
