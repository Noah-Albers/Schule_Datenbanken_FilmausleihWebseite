<?php
    // Loads the database-configs
    $CONFIG = parse_ini_file(__DIR__."/../config.ini");

    // The database connection
    $DB_CON = false;

    /**
     * Connects to the database using the loaded credentials
     * If the connection fails, the global error gets set.
     * Also returns true if the connection was successful
     */
    function connectToDatabase(){
        global $CONFIG,$DB_CON;

        // Checks if the connection is already established
        if($DB_CON && !$DB_CON->connect_errno)
            return true;
        
        // Connects to the database
        $DB_CON = new mysqli($CONFIG["server"],$CONFIG["username"],$CONFIG["password"], $CONFIG["database"]);

        // Checks if the connection failed
        if($DB_CON->connect_errno)
            return false;


        return true;
    }

    function sendQuery($query,$params){
        global $DB_CON;

        // Ensures a connection to the database
        if(!connectToDatabase())
            throw new Exception("ERROR_DB_CONNECT");

        // Prepares the query
        if(!($stmt = $DB_CON->prepare($query)))
            throw new Exception("ERROR_DB_QUERY");

        // Binds the parameters if there are any
        if(count($params) > 0 && !$stmt->bind_param(str_repeat("s",count($params)), ...$params))
            throw new Exception("ERROR_DB_QUERY_BIND");

        // Executes the query
        if(!$stmt->execute())
            throw new Exception("ERROR_DB_QUERY_EXEC");

        // Closes the statement
        $stmt->close();
    }

    function getQuery($query,$params){
        global $DB_CON;

        // Ensures a connection to the database
        if(!connectToDatabase())
            throw new Exception("ERROR_DB_CONNECT");

        // Prepares the query
        if(!($stmt = $DB_CON->prepare($query)))
            throw new Exception("ERROR_DB_QUERY");

        // Binds the parameters if there are any
        if(count($params) > 0 && !$stmt->bind_param(str_repeat("s",count($params)), ...$params))
            throw new Exception("ERROR_DB_QUERY_BIND");

        // Executes the query
        if(!$stmt->execute())
            throw new Exception("ERROR_DB_QUERY_EXEC");

        // Gets the results from the select
        $res = $stmt->get_result();

        // Checks if the result is valid
        if(!$res)
            throw new Exception("ERROR_DB_RESULT");

        // Fetches all rows as an array
        $resAsArray=[];

        while($row = $res->fetch_assoc())
            $resAsArray[]=$row;
        
        // Ends the request
        $stmt->close();

        // Returns the fetched data
        return $resAsArray;
    }

    /**
     * Closes the database connection
     */
    function closeDatabaseConnection(){
        global $DB_CON;

        // Checks that the connection is still open
        // and that the connection had no error
        if($DB_CON && !$DB_CON->connect_errno)
            // Closes the connection
            $DB_CON->close();
    }
?>