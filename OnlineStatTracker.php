<?php
    if($_SERVER['REMOTE_ADDR'] != gethostbyname('play.projectnodenium.com')){
        echo "<h1>Access Denied. This page can only be accessed from the server machine</h1>";
        exit();
    }

    $filepath = 'OnlineStats.json';
    $json = json_decode(file_get_contents($filepath), true) or die("Error: Cannot create object from file");

    $serverData = $_POST['dataPush'];
    if($serverData == null){
        echo "<h1>No Data Yet.</h1>";
    }else{
        $serverData = json_decode($serverData, true) or die("Error: Cannot create object from POST data");
        if ($serverData['main'] != null && $serverData['test'] != null) {


            $timeMain = $serverData['main']['time'];
            $timeTest = $serverData['test']['time'];
            $onlineMain = $serverData['main']['online'];
            $onlineTest = $serverData['test']['online'];
            $playersMain = $serverData['main']['players'];
            $playersTest = $serverData['test']['players'];

            $json['main']['last-updated'] = $timeMain;
            $json['main']['online'] = $onlineMain;
            foreach ($playersMain as $player) {
                $json['main']['players'][$player]['last-online'] = $timeMain;
                if($json['main']['players'][$player]['logs'] == null)
                    $json['main']['players'][$player]['logs'] = array();

                $time = intdiv($timeMain, 3600);
                if(!in_array($time, $json['main']['players'][$player]['logs']))
                    $json['main']['players'][$player]['logs'] = array_merge($json['main']['players'][$player]['logs'], array($time));
            }

            $json['test']['last-updated'] = $timeTest;
            $json['test']['online'] = $onlineTest;
            foreach ($playersTest as $player) {
                $json['test']['players'][$player]['last-online'] = $timeTest;
                $json['test']['players'][$player]['isBot'] = $serverData['test']['players'][$player]['isBot'];

                if($json['test']['players'][$player]['logs'] == null)
                    $json['test']['players'][$player]['logs'] = array();

                $time = intdiv($timeTest, 3600);
                if(!in_array($time, $json['test']['players'][$player]['logs']))
                    $json['test']['players'][$player]['logs'] = array_merge($json['test']['players'][$player]['logs'], array($time));
            }


            $json = json_encode($json, JSON_PRETTY_PRINT);
            file_put_contents($filepath, $json);

            echo "<h1>Next Log in " . 1 . " minute</h1>";
            echo "<h1>Main</h1>";
            echo "<p>Time: " . (new DateTime("@$timeMain"))->format('Y-m-d H:i:s') . "</p>";
            echo "<p>Online: " . $onlineMain . "</p>";
            echo "<p>Players: " . implode(", ", $playersMain) . "</p>";
            echo "<h1>Test</h1>";
            echo "<p>Time: " . (new DateTime("@$timeTest"))->format('Y-m-d H:i:s') . "</p>";
            echo "<p>Online: " . $onlineTest . "</p>";
            echo "<p>Players: " . implode(", ", $playersTest) . "</p>";
        }
    }
?>

<script>
    async function LogData(){
        let urlMain = 'https://mcapi.us/server/status?ip=' + 'play.projectnodenium.com' + '&port=' + '25565';
        let urlTest = 'https://mcapi.us/server/status?ip=' + 'play.projectnodenium.com' + '&port=' + '25566';
        let responseMain = await fetch(urlMain);
        let responseTest = await fetch(urlTest);
        dataMain = await responseMain.json();
        dataTest = await responseTest.json();
        
        onlineMembersMain = dataMain.players.sample;
        onlineMembersMain = onlineMembersMain.map(function(member){
            return member.name;
        });
        onlineMembersTest = dataTest.players.sample;
        onlineMembersTest = onlineMembersTest.map(function(member){
            return member.name;
        });
        
        let pushData = {
            "main": {
                "time": dataMain.last_updated,
                "online": dataMain.online,
                "players": onlineMembersMain
            },
            "test": {
                "time": dataTest.last_updated,
                "online": dataTest.online,
                "players": onlineMembersTest
            }
        };

        document.getElementById('dataPush').value = JSON.stringify(pushData);
        document.getElementById('onlineStatTracker').submit();
    }


    let minsDelay = 10;
    setTimeout(LogLoop, minsDelay * 60 * 1000);
    function LogLoop(){
        LogData();
    }
</script>
<a href='OnlineStats.json'>Raw Data</a>
<form id="onlineStatTracker" action="https://projectnodenium.com/OnlineStatTracker" method="post">
    <input id="dataPush" type="hidden" name="dataPush" value="">
</form>