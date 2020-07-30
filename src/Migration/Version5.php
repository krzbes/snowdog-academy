<?php

namespace Snowdog\Academy\Migration;

use Snowdog\Academy\Core\Database;

class Version5
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke()
    {
        $this->addForAdultFieldInBook();
        $this->addIsAdultInUser();
    }

    private function addForAdultFieldInBook(): void
    {
        $createQuery = <<<SQL
ALTER TABLE books 
add adult_only boolean NOT NULL default 0
SQL;
        $this->database->exec($createQuery);
    }

    private function addIsAdultInUser(): void
    {
        $createQuery = <<<SQL
alter table users 
add is_adult boolean NOT NULL default 1
SQL;
        $this->database->exec($createQuery);
    }
}
?>