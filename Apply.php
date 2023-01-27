<?php
    session_start();
    $file = "Visitors.json";
    $ip = $_SERVER['REMOTE_ADDR'];

    $json = file_get_contents($file) or die("Error: Cannot open file");
    $json = json_decode($json, true);

    $json['totalApplys']++;
    if(isset($json['ips'][$ip]) && isset($_SESSION['sessionStartTime'])) {
        $json['ips'][$ip]['applied'] = array("start" => $_SESSION['sessionStartTime'], "apply" => date("Y-m-d") . " " . date("h:i:sa"));
    }

    $json = json_encode($json, JSON_PRETTY_PRINT);
    file_put_contents($file, $json) or die("Error: Cannot write to file");
?>

<script>
    window.location.href = "https://discord.gg/RKySx92GRC";
</script>