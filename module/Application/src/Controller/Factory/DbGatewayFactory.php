<?php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;


class DbGatewayFactory implements FactoryInterface
{

    public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
    {
        $adapter = $container->get('dbSite');

        $controller = new $requestedName(
            $adapter
        );

        return $controller;
    }
}