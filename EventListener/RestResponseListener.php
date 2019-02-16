<?php

namespace Onixcat\Bundle\RestApiBundle\EventListener;

use Onixcat\Bundle\RestApiBundle\{
    Serializer, ResponseDocument, SerializationContainer, Exception\RestApiException, Resolver\RequestBinder
};
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\{
    FilterControllerEvent, GetResponseEvent, GetResponseForControllerResultEvent, GetResponseForExceptionEvent
};
use Symfony\Component\HttpFoundation\{
    JsonResponse, Request, Response
};

/**
 * Class RestResponseListener
 * @package Onixcat\Bundle\RestApiBundle\EventListener
 */
final class RestResponseListener
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $apiPrefixes;

    /**
     * If in debug app mode
     *
     * @var bool
     */
    private $isDebug;

    /**
     * @var RequestBinder
     */
    private $binder;

    /**
     * RestResponseListener constructor.
     * @param Serializer $serializer
     * @param RequestBinder $binder
     * @param array $prefixes
     * @param bool $isDebug
     */
    public function __construct(Serializer $serializer, RequestBinder $binder, array $prefixes, bool $isDebug)
    {
        $this->serializer = $serializer;
        $this->apiPrefixes = $prefixes;
        $this->isDebug = $isDebug;
        $this->binder = $binder;
    }

    /**
     * Build resource document response according to REST api specification format
     *
     * @see https://onixcat.atlassian.net/wiki/pages/viewpage.action?pageId=4718606#RESTAPISpecification[RFC]-Resourceobject
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$this->isApiRequest($event->getRequest())) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        if ($controllerResult instanceof SerializationContainer) {
            $this->setJsonResponse($event, $controllerResult->getResponseDocument(), $controllerResult->getGroups());
        } else {
            $document = $controllerResult instanceof ResponseDocument
                ? $controllerResult
                : new ResponseDocument($controllerResult);
            $this->setJsonResponse($event, $document, $document->getMessages() ? ['messages'] : []);
        }
    }

    /**
     * Hook to catch request body with application/json headers and handle to DTO
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $contentType = $event->getRequest()->headers->get('Content-Type');
        if ($this->isApiRequest($event->getRequest()) && strpos($contentType, 'application/json') === 0) {
            $this->binder->bind($event->getRequest(), $event->getController());
        }
    }

    /**
     * Build error response according to REST api specification format
     *
     * @see https://onixcat.atlassian.net/wiki/pages/viewpage.action?pageId=4718606#RESTAPISpecification[RFC]-Errors
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        if (!$this->isApiRequest($event->getRequest())) {
            return;
        }

        $exception = $event->getException();
        $response = new JsonResponse;
        $document = new ResponseDocument;

        if ($exception instanceof HttpExceptionInterface) {
            $response->headers->replace($exception->getHeaders());
            $response->setStatusCode($exception->getStatusCode());
            $this->addDocumentErrors($document, $response, $exception);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $document->addMessage(
                $response->getStatusCode(),
                $exception->getMessage(),
                $this->getTraceErrors($exception)
            );
        }

        $response->setJson($this->serializer->toJson($document, ['messages']));
        $event->setResponse($response);
    }

    /**
     * @param \Exception $exception
     * @return null|string
     */
    private function getTraceErrors(\Exception $exception):?string
    {
        return $this->isDebug ? (string)$exception : null;
    }

    /**
     * @param ResponseDocument $doc
     * @param Response $response
     * @param HttpExceptionInterface $exception
     * @return ResponseDocument
     */
    private function addDocumentErrors(ResponseDocument $doc, Response $response, HttpExceptionInterface $exception)
    {
        if ($exception instanceof RestApiException) {
            foreach ($exception->getMessages() as $message) {
                $doc->addMessage($response->getStatusCode(), $message, $this->getTraceErrors($exception));
            }
        } else {
            $doc->addMessage($response->getStatusCode(), $exception->getMessage(), $this->getTraceErrors($exception));
        }

        return $doc;
    }

    /**
     * @param GetResponseEvent $event
     * @param ResponseDocument $document
     * @param array $groups
     */
    private function setJsonResponse(GetResponseEvent $event, ResponseDocument $document, array $groups = []): void
    {
        $time = $event->getRequest()->query->getInt('t');
        if ($event->getRequest()->getMethod() === "GET" && $time) {
            $document->setT($time);
        }

        $response = new JsonResponse;
        $response->setJson($this->serializer->toJson($document, $groups));
        $event->setResponse($response);
    }

    /**
     * If current request is under our api scope
     *
     * @param Request $request
     *
     * @return bool
     */
    private function isApiRequest(Request $request): bool
    {
        foreach ($this->apiPrefixes as $prefix) {
            if (strpos($request->getPathInfo(), $prefix) === 0) {
                return true;
            }
        }

        return false;
    }
}
