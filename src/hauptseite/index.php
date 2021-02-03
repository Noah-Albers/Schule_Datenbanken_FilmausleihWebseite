<?php
    // Imports the page designer
    include_once("../resources/PageDesigner.php");
    include_once("../resources/config.php");

    // Starts the page designer
    startPageDesigner([
        "pagetitle" =>fn($c)=>"Hauptseite",

        "head"=>fn($c)=>onGlobalHead(),

        "body"=>fn($c)=>onNoneBody(),

        "onUser" => [
            "body" => fn($c)=>onUserBody($c)
        ],

        "onAdmin" => [
            "body" => fn($c)=>onAdminBody($c)
        ]
    ]);

    // Adds all head-files
    function onGlobalHead(){
        ?>
            <link rel="stylesheet" href="style.css">
        <?php
    }
    
    // Adds the default redirections
    function addDefaultFunctions(){
        ?>
        <!--Logout redirection-->
        <a href="../backend/redirect/logout.php">Ausloggen</a>
        <!--Filmlist redirection-->
        <a href="../filmliste/">Filmlist</a>
        <!--Filmlist redirection-->
        <a href="../filmkorb/">Filmkorb</a>
        <?php
    }


    // When the user is not logged in
    function onNoneBody(){
        // Displays a welcome message
        ?> <h1>Willkommen</h1> <?php
        // Displays all basic functions
        addDefaultFunctions();
    }

    // When a user is logged in
    function onUserBody($context){
        // Gets the user
        $u = &$context["user"];

        // Displays a welcome message
        ?> <h1>Willkommen <?php echo htmlspecialchars($u->firstname)?></h1> <?php

        // Adds the default functions
        addDefaultFunctions();

        // Adds the redirection to the borrowed films
        ?> <a href="../ausleihen/">Ausgeliehen Filme</a> <?php
    }

    // When an admin is logged in
    function onAdminBody($context){
        // Displays all user functions
        onUserBody($context);

        // Adds the redirection to film managment
        ?> <a href="../verwaltung/">Filmverwaltung</a> <?php
    }
?>