<?php
    // Imports the database access
    include_once("Database.php");

    class User{
        public $id,$sessionID,$firstname,$lastname,$email,$pass_hash,$pass_salt,$birthday,$adminstatus;

        /**
         * Gets the user from an mysql result.
         * @param mysqlResult the returnd mysql data to fetch the user
         * 
         * @throws Exception if the data is not valid or not given
         */
        function __construct($mysqlResult){
            // Gets the values
            $this->id=intval($mysqlResult["ID"]);
            $this->sessionID=$mysqlResult["SessionID"];
            $this->firstname=$mysqlResult["Vorname"];
            $this->lastname=$mysqlResult["Nachname"];
            $this->email=$mysqlResult["Email"];
            $this->pass_hash=$mysqlResult["Pass_hash"];
            $this->pass_salt=$mysqlResult["Pass_salt"];
            $this->birthday=$mysqlResult["Geburtstag"];
            $this->adminstatus=$mysqlResult["AdminStatus"];
        }


        /**
         * Updates any changes of the user to the database
         * @throws Exception if anything happens with the database
         */
        function updateDatabaseUser(){
            // Tries to update the database with the given data
            // Forwards any exceptions
            $res = sendQuery(
                "UPDATE `kunde` SET `SessionID`=?,`Vorname`=?,`Nachname`=?,`Email`=?,`Pass_hash`=?,`Pass_salt`=?,`Geburtstag`=?,`AdminStatus`=? WHERE ID=?",
                [
                    $this->sessionID,
                    $this->firstname,
                    $this->lastname,
                    $this->email,
                    $this->pass_hash,
                    $this->pass_salt,
                    $this->birthday,
                    $this->adminstatus,
                    $this->id
                ]
            );
        }
    }

    /**
     * Tries to grab a user by his session id
     * 
     * @param id the user passed session id to search for
     * @throws exception the exception contains the string-key for the error code on the 
     * 
     * @return null if the session is invalid (Not found or not 20 characters long); otherwise the user instance
     */
    function getUserFromSessionID($id){
        global $DB_CON,$ERROR_CODES;

        // Checks if the session id has an invalid length
        if(strlen($id) != 20)
            return null;

        // Gets the database result
        $dbRes = getQuery(
            "SELECT * FROM `Kunde` k WHERE k.`sessionID`=?;",
            [$id]
        );

        // Checks if a user got found
        if(count($dbRes) <= 0)
            return null;

        // Gets the user
        $user = new User($dbRes[0]);

        return $user;
    }

?>