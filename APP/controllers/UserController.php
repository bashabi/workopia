<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class UserController
{

    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * 
     * Show the login page
     * 
     * 
     * @return void
     */

    public function login()
    {
        loadView('users/login');
    }

    public function create()
    {
        loadView('users/create');
    }
}
