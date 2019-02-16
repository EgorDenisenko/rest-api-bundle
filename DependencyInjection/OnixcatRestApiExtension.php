<?php

namespace Onixcat\Bundle\RestApiBundle\DependencyInjection;

use Onixcat\Bundle\RestApiBundle\Resolver\RequestBinder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder, Reference
};
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Onixcat\Bundle\RestApiBundle\Serializer as OnixcatSerializer;
use Onixcat\Bundle\RestApiBundle\EventListener\RestResponseListener;
use Onixcat\Bundle\RestApiBundle\Normalizer\DatetimeNormalizer;

/**
 * Class OnixcatRestApiExtension
 * @package Onixcat\Bundle\RestApiBundle\DependencyInjection
 */
class OnixcatRestApiExtension extends Extension
{
    use ServicesPrefixAwareTrait;

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container
            ->register($this->concatPrefix('serializer.symfony'), SymfonySerializer::class)
            ->setPublic(false);

        $container
            ->register($this->concatPrefix('serializer'), OnixcatSerializer::class)
            ->setPublic(false)
            ->addArgument(new Reference($this->concatPrefix('serializer.symfony')));

        $container->setAlias('onixcat.serializer', $this->concatPrefix('serializer'));

        $container
            ->register($this->concatPrefix('request.binder'), RequestBinder::class)
            ->setPublic(false)
            ->addArgument(new Reference('validator'))
            ->addArgument(new Reference($this->concatPrefix('serializer')))
            ->addArgument(new Reference('annotation_reader'));

        $container
            ->register($this->concatPrefix('view_response_listener'), RestResponseListener::class)
            ->addArgument(new Reference($this->concatPrefix('serializer')))
            ->addArgument(new Reference($this->concatPrefix('request.binder')))
            ->addArgument($config['prefixes'])
            ->addArgument('%kernel.debug%')
            ->addTag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.view',
                    'method' => 'onKernelView',
                    'priority' => 30,
                ]
            )
            ->addTag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.controller',
                    'method' => 'onKernelController',
                ]
            )
            ->addTag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.exception',
                    'method' => 'onKernelException',
                ]
            );

        $container
            ->register($this->concatPrefix('normalizer.datetime'), DatetimeNormalizer::class)
            ->setPublic(false)
            ->addTag('serializer.normalizer', ['priority' => 30]);
    }
}
