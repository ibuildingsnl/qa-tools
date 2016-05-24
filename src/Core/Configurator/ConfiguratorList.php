<?php

namespace Ibuildings\QaTools\Core\Configurator;

use ArrayIterator;
use Ibuildings\QaTools\Core\Assert\Assertion;
use IteratorAggregate;

final class ConfiguratorList implements IteratorAggregate
{
    /**
     * @var Configurator[]
     */
    private $configurators;

    public function __construct(array $configurators)
    {
        Assertion::allIsInstanceOf($configurators, Configurator::class);

        $this->configurators = $configurators;
    }

    /**
     * @param Configurator $configurator
     * @return bool
     */
    public function contains(Configurator $configurator)
    {
        return in_array($configurator, $this->configurators);
    }

    /**
     * @param Configurator $configurator
     * @return ConfiguratorList
     */
    public function append(Configurator $configurator)
    {
        return new self(array_merge($this->configurators, $configurator));
    }

    public function appendList(ConfiguratorList $other)
    {
        return new self(array_merge($this->configurators, $other->configurators));
    }

    public function getIterator()
    {
        return new ArrayIterator($this->configurators);
    }
}
