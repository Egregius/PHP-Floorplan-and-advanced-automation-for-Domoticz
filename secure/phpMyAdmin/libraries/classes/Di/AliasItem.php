<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Holds the PhpMyAdmin\Di\AliasItem class
 *
 * @package PhpMyAdmin\Di
 */
declare(strict_types=1);

namespace PhpMyAdmin\Di;

/**
 * Class AliasItem
 *
 * @package PhpMyAdmin\Di
 */
class AliasItem implements Item
{

    /** @var Container */
    protected $container;

    /** @var string */
    protected $target;

    /**
     * Constructor
     *
     * @param Container $container Container
     * @param string    $target    Target
     */
    public function __construct(Container $container, $target)
    {
        $this->container = $container;
        $this->target = $target;
    }

    /**
     * Get the target item
     *
     * @param array $params Parameters
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get(array $params = [])
    {
        return $this->container->get($this->target, $params);
    }
}
