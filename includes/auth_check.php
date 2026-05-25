<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/*
|------------------------------------------------------------
| CHECK LOGIN
|------------------------------------------------------------
*/

if(!isset($_SESSION['user_id'])){
    echo "User not logged in";
    exit();
}

/*
|------------------------------------------------------------
| ROLE CHECK
|------------------------------------------------------------
*/

function checkRole($allowed_roles){

    if(
        !isset($_SESSION['role']) || 
        !in_array($_SESSION['role'], $allowed_roles)
    ){

        echo "
        <h2 style='color:red;text-align:center;margin-top:50px;'>
        ACCESS DENIED
        </h2>

        <p style='text-align:center;'>
        You do not have permission to access this page.
        </p>
        ";

        exit();
    }
}
?>