<?php

namespace Onixcat\Bundle\RestApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Onixcat\Bundle\RestApiBundle\DependencyInjection\ServicesPrefixAwareTrait;

/**
 * Add new or modify existing serialization -related service(s) according to the needs of REST api implementation
 */
class SerializerPass implements CompilerPassInterface
{
    use ServicesPrefixAwareTrait, PriorityTaggedServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $objectNormalizerDefinition = $container->getDefinition('serializer.normalizer.object');

        // injecting special class info extractor in order to successfully deserialize into objects
        if (!$container->has($objectNormalizerDefinition->getArgument(3))) {
            $extractorId = $this->concatPrefix('serializer.php_doc_extractor');
            $container->register($extractorId, PhpDocExtractor::class)->setPublic(false);
            $objectNormalizerDefinition->replaceArgument(3, new Reference($extractorId));
        }

        // Looks for all the services tagged "serializer.normalizer" and adds them to our Symfony serializer service
        $normalizers = $this->findAndSortTaggedServices('serializer.normalizer', $container);

        if (empty($normalizers)) {
            throw new RuntimeException('You must tag at least one service as "serializer.normalizer" to use the Serializer service');
        }

        $container->getDefinition($this->concatPrefix('serializer.symfony'))->addArgument($normalizers);

        // Looks for all the services tagged "serializer.encoders" and adds them to our Symfony serializer service
        $encoders = $this->findAndSortTaggedServices('serializer.encoder', $container);

        if (empty($encoders)) {
            throw new RuntimeException('You must tag at least one service as "serializer.encoder" to use the Serializer service');
        }

        $container->getDefinition($this->concatPrefix('serializer.symfony'))->addArgument($encoders);
    }
}
