<?php
/**
 * holds the PhpMyAdmin\Database\DatabaseList class
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

namespace PhpMyAdmin\Database;

use PhpMyAdmin\ListDatabase;

/**
 * holds the DatabaseList class
 *
 * @package PhpMyAdmin
 */
class DatabaseList
{
    /**
     * Holds database list
     *
     * @var ListDatabase
     */
    protected $databases = null;

    /**
     * magic access to protected/inaccessible members/properties
     *
     * @see https://secure.php.net/language.oop5.overloading
     *
     * @param string $param parameter name
     *
     * @return mixed
     */
    public function __get($param)
    {
        switch ($param) {
            case 'databases':
                return $this->getDatabaseList();
        }

        return null;
    }

    /**
     * Accessor to PMA::$databases
     *
     * @return ListDatabase
     */
    public function getDatabaseList(): ListDatabase
    {
        if (null === $this->databases) {
            $this->databases = new ListDatabase();
        }

        return $this->databases;
    }
}
