<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-memcached.
 *
 * @link     https://github.com/Cheng-ZY/hyperf-memcached
 * @document https://github.com/Cheng-ZY/hyperf-memcached
 * @contact  cczzyy.cn@gmail.com
 * @license  https://github.com/Cheng-ZY/hyperf-memcached/blob/main/LICENSE
 */
namespace Czy\HyperfMemcached\Pool;

use Czy\HyperfMemcached\Frequency;
use Czy\HyperfMemcached\MemcachedConnection;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Pool;
use Hyperf\Utils\Arr;
use Psr\Container\ContainerInterface;

/**
 * MemcachedPool.
 */
class MemcachedPool extends Pool
{
    protected array $config;

    public function __construct(ContainerInterface $container, protected string $name = 'default')
    {
        $config = $container->get(ConfigInterface::class);
        $key = sprintf('memcached.%s', $this->name);
        if (! $config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }
        $this->config = $config->get($key);
        $options = Arr::get($this->config, 'pool', []);
        $this->frequency = make(Frequency::class);
        parent::__construct($container, $options);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function createConnection(): ConnectionInterface
    {
        return new MemcachedConnection($this->container, $this, $this->config);
    }
}
