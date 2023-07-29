<?php 

// src/Service/ForeignKeyManager.php

namespace App\Doctrine;

use Doctrine\DBAL\Connection;

class ForeignKeyManager
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function disableForeignKeys()
    {
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
    }

    public function enableForeignKeys()
    {
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }
}


?>
