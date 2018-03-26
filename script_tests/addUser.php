<?php

require_once __DIR__ . '/../loader.php';


DB::init();

// create a placeholder user
$user = (new User())
        -> setFirstname("Jane")
        -> setLastname("Doe")
        -> setEmail("jane.doe@skynet.com")
        -> setPassword("goodlookingjane")
        -> insert();

var_dump($user);
