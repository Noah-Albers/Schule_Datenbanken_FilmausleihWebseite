<?php
    include_once("BasePage.php");

    startBasePageHandler([
        "onUser" => "onUser",
        "onAdmin" => "onAdmin",
        "onNotLoggedIn" => "onNotLoggedIn"
    ]);

    function onUser($u){
        echo "You are a user: ".$u->firstname;
    }
    function onAdmin($u){
        echo "You are an admin: ".$u->firstname;
    }
    function onNotLoggedIn(){
        echo "You are not logged in";
    }
?>