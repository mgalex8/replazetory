<?php

namespace App\Bundle\YamlReplacerParser\Traits;

use App\Bundle\Database\DBConnection;

/**
 * Trait ContentFiltratorSetup
 */
trait DatabaseSetup
{

    /**
     * @return void
     */
    protected function create_db()
    {
        $this->db = new DBConnection("mysql", 'user1', '1234', 'er2night_db');
    }
}