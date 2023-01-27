<?php
    $back = $_SERVER['HTTP_REFERER'];

    setcookie("username", "", time() - 3600, "/");
    setcookie("loginToken", "", time() - 3600, "/");
    session_start();
    session_unset();
    session_destroy();
    if($back == "https://projectnodenium.com/Profiles/Account"){
        $back = "https://projectnodenium.com/Profiles/Login";
    }
    ob_start();
    while (ob_get_status()) 
    {
        ob_end_clean();
    }
    header("Refresh:0; url=$back");
?>
