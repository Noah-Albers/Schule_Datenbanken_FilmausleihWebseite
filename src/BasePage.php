<?php

    // Imports the user manager
    include_once("resources/Usermanager.php");

    // Disables the output of errors
    error_reporting(E_ERROR | E_PARSE);

    /**
     * @param (onAdmin, onUser, onNotLoggedIn) the execution chain. Checks from top to bottom if the callback handler is given and matches.
     * 
     */
    $PRESET_SETTINGS = [
        "onAdmin" => null,                          // Callback when an admin logges in
        "onUser" => null,                           // Callback when a user logges in
        "onNotLoggedIn" => "__send403Response",     // Callback when someone that is not logged in at all requests the page
        
        "onError" => "__sendUnknownError",           // Callback when something went wrong on the server side (Eg. database error)

        "sessionKey" => "sessionID"                 // The name of the session-id in the get request
    ];

    
    function startBasePageHandler($settings){
        global $PRESET_SETTINGS;

        // Generates the settings from the user values and the preset values
        $set = array_merge($PRESET_SETTINGS,$settings);

        // Gets the passed sessionID
        $sessionID = $_COOKIE[$set["sessionKey"]];

        // Gets the admin call and user call functions
        $onAdmin = $set["onAdmin"];
        $onUser = $set["onUser"];

        // Checks if a session is not given
        // or if the user and admin callbacks are not defined
        if(!isset($sessionID) || (!isset($onUser) && !isset($onAdmin)) ){
            // Executes the not logged in handler
            call_user_func($set["onNotLoggedIn"]);
            return;
        }

        try{
            // Tries to get the logged in user 
            $user = getUserFromSessionID($sessionID);

            // Checks if the user's session could not be found
            if($user === null){
                // Executes the not logged in handler
                call_user_func($set["onNotLoggedIn"]);
                return;
            }

            // Checks if the user is an admin and the admin handler is defined
            if(isset($onAdmin) && $user->adminstatus){
                call_user_func($onAdmin,$user);
                return;
            }

            // Checks if the user call function is set
            if(isset($onUser)){
                call_user_func($onUser,$user);
                return;
            }
            
            // Executes the not logged in function
            call_user_func($set["onNotLoggedIn"]);

        }catch(Exception $e){
            // Executes the on error handler
            call_user_func($set["onError"],$e);
        }
    }

    /**
     * Sends 403 as a default code for not implementing the method as it suggests that the page is forbidden
     */
    function __send403Response(){
        header("HTTP/1.1 403 Forbidden");
        echo "403 you are not allowed to access this page.";
    }

    /**
     * Sends an unknown error page if the error handler has not been overriden
     */
    function __sendUnknownError($exception){
        header("HTTP/1.1 500 Internal Server Error")
        echo "Unknown error occured: ".$exception;
    }

?>