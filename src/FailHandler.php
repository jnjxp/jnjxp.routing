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
 * @category  RequestHandler
 * @package   Jnjxp\Routing
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2019 Jake Johns
 * @license   http://jnj.mit-license.org/2019 MIT License
 * @link      http://jakejohns.net
 */

declare(strict_types=1);

namespace Jnjxp\Routing;

use Aura\Router\Route;
use Aura\Router\Rule;
use Fig\Http\Message\StatusCodeInterface as Code;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Handle failed to route
 *
 * @category Handler
 * @package  Jnjxp\Routing
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  https://jnj.mit-license.org/ MIT License
 * @link     https://jakejohns.net
 *
 * @see Handler
 */
class FailHandler implements Handler
{
    /**
     * Response Factory
     *
     * @var ResponseFactory
     *
     * @access protected
     */
    protected $responseFactory;

    /**
     * Rule to responders array
     *
     * @var callable[]
     *
     * @access protected
     */
    protected $responders = [];

    /**
     * Create handler
     *
     * @param ResponseFactory $responseFactory responseFactory
     *
     * @access public
     */
    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;

        $this->responders = [
            Rule\Allows::class  => [$this, 'notAllowed'],
            Rule\Accepts::class => [$this, 'notAcceptable'],
            Rule\Host::class    => [$this, 'notFound'],
            Rule\Path::class    => [$this, 'notFound'],
        ];
    }

    /**
     * Set responder for a failed rul
     *
     * @param string   $rule    name of rule
     * @param callable $handler handler
     *
     * @return void
     *
     * @access public
     */
    public function setResponder(string $rule, callable $handler) : void
    {
        $this->responders[$rule] = $handler;
    }

    /**
     * Process a failed route
     *
     * @param Request $request request
     *
     * @return Response
     *
     * @access public
     */
    public function handle(Request $request) : Response
    {
        $route   = $request->getAttribute(Route::class);
        $respond = $this->getResponder($route);
        return $respond($request);
    }

    /**
     * Get callable responder based on failed route rule
     *
     * @param Route $route failed route
     *
     * @return callable
     *
     * @access protected
     */
    protected function getResponder(Route $route) : callable
    {
        $rule = $route->failedRule;
        return isset($this->responders[$rule])
            ? $this->responders[$rule]
            : [$this, 'error'];
    }

    /**
     * Not allowed responder
     *
     * @param Request $request request
     *
     * @return Response
     *
     * @access public
     */
    public function notAllowed(Request $request) : Response
    {
        $route = $request->getAttribute(Route::class);
        return $this->responseFactory
            ->createResponse(Code::STATUS_METHOD_NOT_ALLOWED)
            ->withHeader('Allow', implode(', ', $route->allows));
    }

    /**
     * Not acceptable responder
     *
     * @return Response
     *
     * @access public
     */
    public function notAcceptable() : Response
    {
        return $this->responseFactory
            ->createResponse(Code::STATUS_NOT_ACCEPTABLE);
    }

    /**
     * Not found responder
     *
     * @return Response
     *
     * @access public
     */
    public function notFound() : Response
    {
        return $this->responseFactory
            ->createResponse(Code::STATUS_NOT_FOUND);
    }

    /**
     * Error responder
     *
     * @return Response
     *
     * @access public
     */
    public function error() : Response
    {
        return $this->responseFactory
            ->createResponse(Code::STATUS_INTERNAL_SERVER_ERROR);
    }
}
