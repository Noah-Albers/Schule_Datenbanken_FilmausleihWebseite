<?php
    include_once("PageDesigner.php");

    startPageDesigner([
        "pageTitle" =>"t",

        "onUser" => [
            "body" => fn($u)=>onUser($u)
        ],

        "body" => fn($cont) => onNotLoggedIn($cont)
    ]);

    function onUser($context){
        echo "You are a user: ".$context["user"]->firstname;
    }
    function onAdmin($u){
        echo "You are an admin: ".$u->firstname;
    }
    function onNotLoggedIn($cont){
        echo "You are not logged in";
    }
?>