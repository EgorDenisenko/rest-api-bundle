Simple and flexible Rest API solution. 

## Prerequisites

- PHP 7.1+

## Installation

> composer require onixcat/rest-api-bundle

Add bundle to your project`s AppKernel.php:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Onixcat\Bundle\RestApiBundle\RestApiBundle(),
            // ...
        );

        // ...
    }
}
```
