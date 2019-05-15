<?php
/**
 * Jnjxp\Routing
 *
 * PHP version 7
 *
 * Copyright (C) 2019 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  Container
 * @package   Jnjxp\Routing
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2019 Jake Johns
 * @license   http://jnj.mit-license.org/2019 MIT License
 * @link      http://jakejohns.net
 */

declare(strict_types=1);

namespace Jnjxp\Routing;

use Aura\Router\Map;
use Aura\Router\Matcher;
use Aura\Router\RouterContainer as Router;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;


/**
 * RoutingProvider
 *
 * @category Container
 * @package  Jnjxp\Routing
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  https://jnj.mit-license.org/ MIT License
 * @link     https://jakejohns.net
 *
 * @see ServiceProviderInterface
 */
class RoutingProvider implements ServiceProviderInterface
{

    /**
     * Factories
     *
     * @return callable[]
     *
     * @access public
     */
    public function getFactories()
    {
        return [
            DispatchRequest::class  => [$this, 'newDispatcher'],
            FailHandler::class      => [$this, 'newFailHandler'],
            MapBuilder::class       => [$this, 'newMapBuilder'],
            ResolutionHelper::class => [$this, 'newResolutionHelper'],
            RouteRequest::class     => [$this, 'newRouteRequest'],
        ];
    }

    /**
     * Extensions
     *
     * @return callable[]
     *
     * @access public
     */
    public function getExtensions()
    {
        return [Router::class => [$this, 'configRouter']];
    }

    /**
     * ConfigRouter
     *
     * @param Container $container Container
     * @param Router    $router    Router
     *
     * @return void
     *
     * @access public
     */
    public function configRouter(Container $container, Router $router)
    {
        $router->setMapBuilder($container->get(MapBuilder::class));
    }

    /**
     * NewDispatcher
     *
     * @param Container $container Container
     *
     * @return DispatchRequest
     *
     * @access public
     */
    public function newDispatcher(Container $container) : DispatchRequest
    {
        return new DispatchRequest($container->get(ResolutionHelper::class));
    }

    /**
     * NewFailHandler
     *
     * @param Container $container Container
     *
     * @return FailHandler
     *
     * @access public
     */
    public function newFailHandler(Container $container) : FailHandler
    {
        return new FailHandler($container->get(ResponseFactory::class));
    }

    /**
     * NewMapBuilder
     *
     * @return MapBuilder
     *
     * @access public
     */
    public function newMapBuilder() : MapBuilder
    {
        return new MapBuilder();
    }

    /**
     * NewResoluitonHelper
     *
     * @param Container $container Container
     *
     * @return ResolutionHelper
     *
     * @access public
     */
    public function newResolutionHelper(Container $container) : ResolutionHelper
    {
        return new ResolutionHelper($container);
    }

    /**
     * NewRouteRequest
     *
     * @param Container $container Container
     *
     * @return RouteRequest
     *
     * @access public
     */
    public function newRouteRequest(Container $container) : RouteRequest
    {
        return new RouteRequest(
            $container->get(Matcher::class),
            $container->get(FailHandler::class)
        );
    }
}
