<?php include 'AdminLock.php'; ?>
<a href="OnlineTracker">Online Tracker</a>
<p>(time is in UTC)</p>
<?php


    //load the full file
    $filepath = 'OnlineStats.json';
    $json = json_decode(file_get_contents($filepath), true) or die("Error: Cannot create object");

    //grab all the igns of the members
    $validIGNs = array();
    $memberJsonPath = 'Profiles/ProfileDatas/';
    $memberJsons = scandir($memberJsonPath);
    foreach ($memberJsons as $memberJson)
        if ($memberJson != '.' && $memberJson != '..') 
            $validIGNs = array_merge($validIGNs, array(json_decode(file_get_contents($memberJsonPath . $memberJson), true)['inGameName']));

    //load the selected server
    $server = $_GET['server'];
    $loadBots = $_GET['bots'];
    $serverJson = $json[$server];
    $serverlastUpdated = date("Y-m-d H:i:s", $serverJson['last-updated']);
    $serverOnline = $serverJson['online'];
    $serverPlayers = $serverJson['players'];

    //init the data holders
    $_playerNames = array();
    $_playerLogs = array();

    $_activeHoursByPlayer = array();
    $_activeDatesByPlayer = array();
    $_activeDaysOfWeekByPlayer = array();
    
    $_avgActiveHours = array(0 => 0,1 => 0,2 => 0,3 => 0,4 => 0,5 => 0,6 => 0,7 => 0,8 => 0,9 => 0,10 => 0,11 => 0,12 => 0,13 => 0,14 => 0,15 => 0,16 => 0,17 => 0,18 => 0,19 => 0,20 => 0,21 => 0,22 => 0,23 => 0);
    $_maxActiveHours = 0;
    $_avgActiveDaysOfWeek = array('Monday' => 0,'Tuesday' => 0,'Wednesday' => 0,'Thursday' => 0,'Friday' => 0,'Saturday' => 0,'Sunday' => 0);
    $_maxActiveDaysOfWeek = 0;
    $_avgActiveDates = array();
    $_maxActiveDates = 0;

    foreach ($serverPlayers as $player) {
        //get player data
        $name = array_search($player, $serverPlayers);
        $realMember = in_array($name, $validIGNs)? "true" : "false";
        if($loadBots == $realMember)
            continue;
        if (count($player['logs']) == 0)
            continue;
        $_playerNames = array_merge($_playerNames, array($name));
        $_playerLogs = array_merge($_playerLogs, array($name => $logs));
        $activeHours = array(0 => 0,1 => 0,2 => 0,3 => 0,4 => 0,5 => 0,6 => 0,7 => 0,8 => 0,9 => 0,10 => 0,11 => 0,12 => 0,13 => 0,14 => 0,15 => 0,16 => 0,17 => 0,18 => 0,19 => 0,20 => 0,21 => 0,22 => 0,23 => 0);
        $activeDaysOfWeek = array('Monday' => 0,'Tuesday' => 0,'Wednesday' => 0,'Thursday' => 0,'Friday' => 0,'Saturday' => 0,'Sunday' => 0);
        $activeDates = array();
        $currDate = "";

        $lastDT = 0;
        foreach ($player['logs'] as $logEntry) {
            //get date
            $hour = $logEntry % 24;
            $epoch = $logEntry * 3600;
            $day = (new DateTime("@$epoch"))->format('Y-m-d');
            $dayOfWeek = (new DateTime("@$epoch"))->format('l');
            if($day.$hour == $lastDT)
                continue;
            $lastDT = $day.$hour;

            //increment hour data
            if (!isset($activeDates[$day]))
                $activeDates = array_merge($activeDates, array($day => 0));
            $activeDates[$day]++;
            $activeHours[$hour]++;
            $_avgActiveHours[$hour]++;

            //check if new day
            if ($currDate == $day) 
                continue;

            //increment day data
            if (!isset($_avgActiveDates[$day]))
                $_avgActiveDates = array_merge($_avgActiveDates, array($day => 0));
            $_avgActiveDates[$day]++;
            $activeDaysOfWeek[$dayOfWeek]++;
            $_avgActiveDaysOfWeek[$dayOfWeek]++;

            //set current date
            $currDate = $day;
        }

        //add player data to the main data holders
        $_activeHoursByPlayer = array_merge($_activeHoursByPlayer, array($name => $activeHours));
        $_activeDaysOfWeekByPlayer = array_merge($_activeDaysOfWeekByPlayer, array($name => $activeDaysOfWeek));
        $_activeDatesByPlayer = array_merge($_activeDatesByPlayer, array($name => $activeDates));
    }

    //calculate the max values
    $_multiplier = 50;
    foreach (array_keys($_avgActiveHours) as $hour)
        if ($_avgActiveHours[$hour] > $_maxActiveHours)
            $_maxActiveHours = $_avgActiveHours[$hour];
    foreach (array_keys($_avgActiveDaysOfWeek) as $day)
        if ($_avgActiveDaysOfWeek[$day] > $_maxActiveDaysOfWeek)
            $_maxActiveDaysOfWeek = $_avgActiveDaysOfWeek[$day];
    foreach (array_keys($_avgActiveDates) as $date)
        if ($_avgActiveDates[$date] > $_maxActiveDates)
            $_maxActiveDates = $_avgActiveDates[$date];

    ksort($_avgActiveDates);

    if(isset($_GET['server'])){
        if ($_GET['player'] == 'server') {
            echo "<h1>" . ucfirst($server) . " Server Stats</h1>";
            echo "<h2>Last Updated: " . $serverlastUpdated . "</h2>";
            echo "<h2>Server is: " . ($serverOnline == 1 ? "Online" : "Offline") . "</h2>";
            echo "<h2>Active Hours</h2>";
            foreach (array_keys($_avgActiveHours) as $hour)
                echo getBarGraph(round($_avgActiveHours[$hour] / $_maxActiveHours * $_multiplier)) . "   " . $hour . ":00 - " . $_avgActiveHours[$hour] . "<br>";
            echo "<br>";
            echo "<h2>Active Days of Week</h2>"; 
            foreach (array_keys($_avgActiveDaysOfWeek) as $day)
                echo getBarGraph(round($_avgActiveDaysOfWeek[$day] / $_maxActiveDaysOfWeek * $_multiplier)) . "   " . $day . ": " . $_avgActiveDaysOfWeek[$day] . "<br>";
            echo "<br>";
            echo "<h2>Active Dates</h2>"; 
            foreach (array_keys($_avgActiveDates) as $date)
                echo getBarGraph(round($_avgActiveDates[$date] / $_maxActiveDates * $_multiplier)) . "   " . $date . ": " . $_avgActiveDates[$date] . "<br>";
            echo "<br>";
            echo "<h2>Members</h2>";
            echo "<table border='1'>"; 
            foreach ($_playerNames as $player)
                echo "<tr><td>" . $player . "</td><td><a href='OnlineTracker?server=$server&bots=$loadBots&player=$player'>Data</a></td><td>" . ((time() - $serverJson['players'][$player]['last-online']) < (60*15) ? "Online" : " ") . "</td></tr>";
            echo "</table>";
        } else {
            $thisPlayer = $_GET['player'];
            $playerLastOnline = $serverJson['players'][$thisPlayer]['last-online'];
            $playerIsOnline = (time() - $playerLastOnline) < (60*15) ? "true" : "false";
            echo "<h1>" . $thisPlayer ." " . ucfirst($server) . " Server Stats</h1>";
            echo "<h2>Last Online: " . (new DateTime("@$playerLastOnline"))->format('D, d-M-Y H:i:s') . "</h2>";
            echo "<h2>Online: " . ($playerIsOnline == "true" ? "Yes" : "No") . "</h2>";
            echo "<h2>Active Hours</h2>";
            foreach (array_keys($_activeHoursByPlayer[$thisPlayer]) as $hour)
                echo getBarGraph($_activeHoursByPlayer[$thisPlayer][$hour] * 3) . "   " . $hour . ":00 - " . $_activeHoursByPlayer[$thisPlayer][$hour] . "<br>";
            echo "<br>";
            echo "<h2>Active Days of Week</h2>"; 
            foreach (array_keys($_activeDaysOfWeekByPlayer[$thisPlayer]) as $day)
                echo getBarGraph($_activeDaysOfWeekByPlayer[$thisPlayer][$day] * 3) . "   " . $day . ": " . $_activeDaysOfWeekByPlayer[$thisPlayer][$day] . "<br>";
            echo "<br>";
            echo "<h2>Active Dates</h2>"; 
            foreach (array_keys($_activeDatesByPlayer[$thisPlayer]) as $date)
                echo getBarGraph($_activeDatesByPlayer[$thisPlayer][$date] * 3) . "   " . $date . ": " . $_activeDatesByPlayer[$thisPlayer][$date] . "<br>";
        }
    } else {
        echo "<h1>Pick a Server</h1>";
        echo "<a href='OnlineTracker?server=main&bots=false&player=server'>Main Players</a>";
        echo "<br>";
        echo "<a href='OnlineTracker?server=main&bots=true&player=server'>Main Bots</a>";
        echo "<br>";
        echo "<a href='OnlineTracker?server=test&bots=false&player=server'>Test Players</a>";
        echo "<br>";
        echo "<a href='OnlineTracker?server=test&bots=true&player=server'>Test Bots</a>";
        echo "<br>";
        echo "<br>";
        echo "<h4>View the logs</h4>";
        echo "<a href='OnlineStats.json'>Raw Data</a>";
        echo "<br><br>";
    }
?>