<?php

namespace controller;

use model\Database;

class Data
{
    public function execute()
    {
        $data = new Database();
        $data->createTables();
    }
}