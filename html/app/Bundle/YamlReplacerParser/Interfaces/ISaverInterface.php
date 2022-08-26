<?php
namespace App\Bundle\YamlReplacerParser\Interfaces;

interface ISaverInterface
{

    /**
     * @param array $inserts
     * @return void
     */
    public function saveToDatabase(array $inserts): void;

}