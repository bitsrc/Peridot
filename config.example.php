<?php

/* 
 * IDENT_LENGTH
 * Length to use for creating short url identifiers
 * Ex: 3 = http://url.short/ZZZ
 */
define('IDENT_LENGTH',3);

/*
 * APIKEY_LENGTH
 * Length to use for creating API keys
 */
define('APIKEY_LENGTH', 64);

/* 
 * Connection information
 * PDO connection string, and username and password for database.
 * host= defines host to connect to, dbname= defines target db upon the host.
 * Username and password really should be self-evident.
 */
define('CONN_STRING','mysql:host=localhost;dbname=peridot;charset=utf8');
define('CONN_USER', 'peridot');
define('CONN_PASSWORD', '');

/*
 * Allow public usage
 * Whether or not a user is allowed to create a redirect without an API key
 * false means an API key is required to create a redirect
 */
define('ALLOW_PUBLIC', false);

?>