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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Dispatch a request based on Handler attribute
 *
 * @category Middleware
 * @package  Jnjxp\Routing
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  https://jnj.mit-license.org/ MIT License
 * @link     https://jakejohns.net
 *
 * @see Middleware
 */
class DispatchRequest implements Middleware
{
    /**
     * Resolution helper
     *
     * @var null | callable
     *
     * @access protected
     */
    protected $resolve;

    /**
     * Create dispatch middleware
     *
     * @param callable $resolve Callable to resolve a Handler
     *
     * @access public
     */
    public function __construct(callable $resolve = null)
    {
        $this->resolve = $resolve;
    }

    /**
     * Process
     *
     * @param Request $request Request
     * @param Handler $handler next handler
     *
     * @return Response
     *
     * @access public
     */
    public function process(Request $request, Handler $handler) : Response
    {
        $spec    = $request->getAttribute(Handler::class, $handler);
        $handler = $this->getHandler($spec);
        return $handler->handle($request);
    }

    /**
     * Resolve a handler
     *
     * @param mixed $spec Handler or specification
     *
     * @return Handler
     *
     * @access protected
     */
    protected function getHandler($spec) : Handler
    {
        if ($spec instanceof Handler) {
            return $spec;
        }

        if ($this->resolve) {
            return ($this->resolve)($spec);
        }
    }
}
