<?php

namespace Onixcat\Bundle\RestApiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Onixcat\Bundle\RestApiBundle\DependencyInjection\Compiler\SerializerPass;

class OnixcatRestApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SerializerPass);
    }
}
