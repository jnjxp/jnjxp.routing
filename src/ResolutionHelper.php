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

use Psr\Container\ContainerInterface as Container;

/**
 * ResolutionHelper
 *
 * @category Container
 * @package  Jnjxp\Routing
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  https://jnj.mit-license.org/ MIT License
 * @link     https://jakejohns.net
 */
class ResolutionHelper
{
    /**
     * Container
     *
     * @var Container
     *
     * @access protected
     */
    protected $container;

    /**
     * __construct
     *
     * @param Container $container container
     *
     * @access public
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve
     *
     * @param mixed $spec spec
     *
     * @return mixed
     *
     * @access public
     */
    public function __invoke($spec)
    {
        if (! is_string($spec)) {
            return $spec;
        }

        if ($this->container->has($spec)) {
            return $this->container->get($spec);
        }

        return new $spec;
    }
}
