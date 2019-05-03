<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Holds the PhpMyAdmin\Di\FactoryItem class
 *
 * @package PhpMyAdmin\Di
 */
declare(strict_types=1);

namespace PhpMyAdmin\Di;

/**
 * Factory manager
 *
 * @package PhpMyAdmin\Di
 */
class FactoryItem extends ReflectorItem
{

    /**
     * Construct an instance
     *
     * @param array $params Parameters
     *
     * @return mixed
     */
    public function get(array $params = [])
    {
        return $this->invoke($params);
    }
}
