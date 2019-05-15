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
 * @category  Middleware
 * @package   Jnjxp\Routing
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2019 Jake Johns
 * @license   http://jnj.mit-license.org/2019 MIT License
 * @link      http://jakejohns.net
 */

declare(strict_types=1);

namespace Jnjxp\Routing;

use Aura\Router\Matcher;
use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Route a request
 *
 * @category Middleware
 * @package  Jnjxp\Routing
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  https://jnj.mit-license.org/ MIT License
 * @link     https://jakejohns.net
 *
 * @see Middleware
 */
class RouteRequest implements Middleware
{
    /**
     * Matcher
     *
     * @var Matcher
     *
     * @access protected
     */
    protected $matcher;

    /**
     * FailHandler
     *
     * @var mixed
     *
     * @access protected
     */
    protected $failHandler;

    /**
     * __construct
     *
     * @param Matcher $matcher     Matcher
     * @param mixed   $failHandler Fail
     *
     * @access public
     */
    public function __construct(
        Matcher $matcher, $failHandler = FailHandler::class
    ) {
        $this->matcher = $matcher;
        $this->failHandler = $failHandler;
    }

    /**
     * Match a request and add route to arrtibutes
     *
     * @param Request $request Request
     * @param Handler $handler Next
     *
     * @return Response
     *
     * @access public
     */
    public function process(Request $request, Handler $handler) : Response
    {
        $route   = $this->matcher->match($request);
        $request = $route
            ? $this->route($route, $request)
            : $this->noRoute($request);

        return $handler->handle($request);
    }

    /**
     * Add failed route and failHandler to request
     *
     * @param Request $request Request
     *
     * @return Request
     *
     * @access protected
     */
    protected function noRoute(Request $request) : Request
    {
        $route = $this->matcher->getFailedRoute();
        return $request
            ->withAttribute(Route::class, $route)
            ->withAttribute(Handler::class, $this->failHandler);
    }

    /**
     * Add attributes, route and handler to request
     *
     * @param Route   $route   Route
     * @param Request $request Request
     *
     * @return Request
     *
     * @access protected
     */
    protected function route(Route $route, Request $request) : Request
    {
        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        return $request
            ->withAttribute(Route::class, $route)
            ->withAttribute(Handler::class, $route->handler);
    }
}
