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
 * @category  Router
 * @package   Jnjxp\Routing
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2019 Jake Johns
 * @license   http://jnj.mit-license.org/2019 MIT License
 * @link      http://jakejohns.net
 */

declare(strict_types=1);

namespace Jnjxp\Routing;

use Aura\Router\Map;

/**
 * MapBuilder
 *
 * @category Map
 * @package  Jnjxp\Routing
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  https://jnj.mit-license.org/ MIT License
 * @link     https://jakejohns.net
 */
class MapBuilder
{
    /**
     * Builders
     *
     * @var callable[]
     *
     * @access protected
     */
    protected $builders = [];

    /**
     * Build map
     *
     * @param Map $map Map
     *
     * @return void
     *
     * @access public
     */
    public function __invoke(Map $map) : void
    {
        foreach ($this->builders as $builder) {
            $builder($map);
        }
    }

    /**
     * Append callable
     *
     * @param callable $builder builder
     *
     * @return void
     *
     * @access public
     */
    public function append(callable $builder) : void
    {
        $this->builders[] = $builder;
    }
}
