<?php

    error_reporting(E_ERROR | E_PARSE);

    /*
        ==========================
        ========Error Code========
        ==========================


        Database error codes are in range between 0 to 20 where > 10 means some sort of error with the php script
        and < 10 some sort of natural error
    */

    // Holds all error codes that can occure when something fails on the webpages
    $ERROR_CODES = array(
        "ERROR_DB_CONNECT" => 0,       // If the database connection failes
        "ERROR_DB_QUERY" => 20,        // If a prepared query failed to load an query
        "ERROR_DB_QUERY_BIND" => 19,   // If a prepared query failed pass the correct bind string
        "ERROR_DB_QUERY_EXEC" => 18,   // If a prepared query failed to execute
        "ERROR_DB_RESULT" => 17        // If a returned result is invalid
    );
?>