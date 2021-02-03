<?php
    namespace {
        // Disables the output of errors
        error_reporting(E_ERROR | E_PARSE);

        // Implements the basic starting method
        function startPageDesigner($settings){
            \pageDesigner\__startPageDesigner($settings);
        }
    }

    namespace pageDesigner{
        // Imports the user manager
        include_once("Usermanager.php");

        /**
         * The settings are defined as an array with specifiy values. Alle values that have a [O] in front of them can be set
         * globally, but will be overriten locally for the different handlers like admin or user if they are defined.
         * 
         * [
         * 
         *  # Takes a function with one argument. This is the exception that can be thrown when loading the page.
         *  # Currently only by redirection from the Usermanager.getUserFromSessionID. So this issues must be handled
         *  "onError" default to __sendUnknownError:        function(Exception) : void
         *  
         *  # This is used to determin what name the session id has. Can mostly remain unchanged
         *  "sessionKey" default to "sessionID":            string
         * 
         *  # Executes when the body of the page gets loaded. Use this to output any html to the page.
         *  # The given Array can hold all kind of different context variables. Please look at the local overrides to see the different possiblilties.
         *  # By default for the body no context is given 
         * [O] "body" default to __sendNotImplemented("Body"):                      function(Array) : void
         * 
         *  # Same like the body, but executes when loading the html head-section.
         *  # Use to include any script or css stuff.
         * [O] "head" default to __sendNotImplemented("Head"):                      function(Array) : void
         * 
         *  # Another context-specific value. Returns the string that shall be send using
         *  # the header function (HTTP). If null, nothing will be send
         * [O] "httpheader" default to null:                                        function(Array) : string
         * 
         *  # Another context-specific value. Returns the title that will be displayed on the
         *  # browser tab. If null, no title tag will be applied.
         * [O] "pageTitle" default to null:                                         function(Array) : string
         * 
         * 
         * # The following values are the handlers. They can locally override all values that have a [O] in front of them.
         * # They only applied on very certained conditions.
         * 
         * 
         * # Executes when the user is logged in and an admin; otherwise forward to "onUser"
         * # Context values:
         *      # The user-profile of the requesting user
         *      * Usermanager.User "user"
         *  "onAdmin" default to null:                                              Handler
         * 
         *
         * # Executes when the user is logged in but not an admin; otherwise forward to "onNone"
         * # Context values:
         *      # The user-profile of the requesting user
         *      * Usermanager.User "user"
         *  "onUser" default to null:                                               Handler
         * 
         * # Executes when the the user is not logged in.
         *  "onNone" default to null:                                               Handler
         * ]
         */

        // Holds all presets of the settings. These will be merged together with the individual passed settings
        $PRESET_SETTINGS = [
            "body" => fn($context) => __sendNotImplemented("Body"),
            "head" => fn($context) => __sendNotImplemented("Head"),
            "onError" => fn($exc)=>__sendUnknownError($exc),
            "sessionKey" => "sessionID"
        ];

        /**
         * Sends the html-page and includes the given callbacks and the title.
         * 
         * @param headSender the callback that will be executed, when the head gets displayed
         * @param bodySender the callback that will be executed, when the body gets displayed
         * @param pageTitle the title that will be displayed as the tab-title, if given
         */
        function __loadPage($settings, $handler, $context){
            // Helper-util to insert data from the settings
            // Checks firstly the handler and if that didn't work out the settings
            $insert = function($key,$optHandler=null) use($settings, $handler, $context) {
                // Holds the optional return value
                $ret = null;

                // Checks if the handler gives the key element
                if(isset($handler[$key]))
                    $ret = $handler[$key]($context);
                // Checks if the page gives the key element
                else if(isset($settings[$key]))
                    $ret = $settings[$key]($context);
                
                // Checks if eigther an advanced execution is available (optHandler is set)
                // and if the execution gave an value ($ret is set)
                if(isset($ret) && isset($optHandler))
                    $optHandler($ret);
            };

            // Inserts the header
            $insert("httpheader",fn($val)=>header($val));
            ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='utf-8'>
                    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <?php
                        // Includes the title
                        $insert("pagetitle",fn($val)=>print("<title> Filmausleih | ".htmlspecialchars($val)."</title>"));

                        // Inserts the head
                        $insert("head");
                    ?>
                </head>
                <body>
                    <?php
                        // Inserts the body
                        $insert("body");
                    ?>
                </body>
                </html>
            <?php
        }

        /**
         * Starts the basic page designer
         * 
         * @param settings these settings are passed as an array. They will be merged, but override the preset settings.
         */
        function __startPageDesigner($settings = []){
            global $PRESET_SETTINGS;

            // Generates the settings from the user values and the preset values
            $set = array_merge($PRESET_SETTINGS,$settings);

            try{
                // Creates the context
                $context = [];

                // Tries to get the page handler for the user (By authentication)
                $handler = __getCurrentPageHandler($set,$context);

                // Loads the basic page
                __loadPage($set, $handler, $context);
            }catch(Exception $e){
                // Executes the on error handler
                $set["onError"]($e);
            }
        }

        /**
         * 
         * @throws Exception forwarded exception from the Usermanager.getUserFromSessionID if anything went wrong
         * 
         * @param settings the settings that got passed to generate the handler
         * @param context modifiable context that will be given to the final execution functions
         * 
         * @return Array the handler for the page. From the settings that got passed (onAdmin, onUser or onNone);
         *                 if non works out, returns empty handler (with null values inside)
         */
        function __getCurrentPageHandler($settings,&$context){
            
            // Gets the passed sessionID
            $sessionID = $_COOKIE[$settings["sessionKey"]];

            // Copies the handlers (different access levels)
            $handlerAdmin = $settings["onAdmin"];
            $handlerUser = $settings["onUser"];
            $handlerNone = $settings["onNone"];

            // Returns the given type if it is an array, otherwise an empty array
            $retValidType = fn($array) => isset($array) && gettype($array) === "array" ? $array : [];

            // Checks if a session is not given
            // or if the user and admin handlers are not defined
            if(!isset($sessionID) || (!isset($handlerUser) && !isset($handlerAdmin)))
                return $retValidType($handlerNone);

            // Tries to get the logged in user. Errors will be forwarded
            $user = getUserFromSessionID($sessionID);

            // Checks if the user's session could not be found
            if($user === null)
                return $retValidType($handlerNone);

            // Checks if the user is an admin and the admin handler is defined
            if(isset($handlerAdmin) && $user->adminstatus){
                // Updates the context and returns the handler
                $context["user"] = $user;
                return $retValidType($handlerAdmin);
            }

            // Checks if the user call function is set
            if(isset($handlerUser)){
                // Updates the context and returns the handler
                $context["user"] = $user;
                return $retValidType($handlerUser);
            }
            
            return $retValidType($handlerNone);
        }

        /**
         * Sends an unknown error page if the error handler has not been overriden
         */
        function __sendUnknownError($exception){
            header("HTTP/1.1 500 Internal Server Error");
            echo "Unknown error occured: ".$exception;
        }

        /**
         * Outputs a default not implemented message
         */
        function __sendNotImplemented($name){
            echo "<h1>The ".htmlspecialchars($name)."-Section is not implemented.</h1>";
        }
    }
?>