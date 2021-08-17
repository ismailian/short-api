<?php


/*
| Require database file
*/
require_once 'database.php';

/*
| use database
*/
use Core\Database;

/* 
| connect to database 
*/
$db = new Database("localhost", "root", "", "short_links");

/*
| Allow the use of API by anyone, aka: CORS
*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST');
header('Access-Control-Allow-Headers: *');