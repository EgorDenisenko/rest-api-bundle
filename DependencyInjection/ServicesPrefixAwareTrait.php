<?php

namespace Onixcat\Bundle\RestApiBundle\DependencyInjection;

trait ServicesPrefixAwareTrait
{
    /**
     * Add bundle services prefix to service id
     *
     * @param string $serviceId
     *
     * @return string
     */
    private function concatPrefix(string $serviceId): string
    {
        return 'onixcat_rest_api.' . $serviceId;
    }
}
