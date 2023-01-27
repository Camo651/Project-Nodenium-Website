<?php include 'AdminLock.php'; ?>
<br>
<a href="VisitorStats?general=true">General</a>
<br>
<a href="VisitorStats?pages=true">Pages</a>
<br>
<a href="VisitorStats?users=true">Users</a>
<br>
<a href="VisitorStats?visits=true">Visits</a>
<br>
<a href="VisitorStats?applications=true">Applications</a>
<br>
<a href='Visitors.json' >Raw Stats</a>
<br>

<?php
    $path = "Visitors.json";
    $json = json_decode(file_get_contents($path), true) or die("Error: Cannot create object");
    $totalHits = $json['totalHits'];
    $totalUniqueVisits = $json['totalUniqueVisits'];
    $totalLogins = $json['totalLogins'];
    $totalPages = $json['totalPages'];
    $totalReferrers = $json['totalReferrers'];
    $totalApplyBtns = $json['totalApplys'];
    $ips = $json['ips'];
    $users = array();
    $hits = array();
    $hitsWithReferences = array();
    $visitsPerDay = $json['visitsByDate'];
    $visitsPerHour = $json['visitsByHour'];
    $usernames = array();
    $timeToApply = array();
    $totalTimeToApply = 0;
    $timeToApplyByRange = array();
    $applicationsPerDay = array();


    foreach ($ips as $ip => $user){
        array_push($users, $ip.": ".$user['hits']." hits, lastSeen: ".$user['lastSeen']);
        $hits[$user['hits']]++;
        if(isset($user['referrers']))
            $hitsWithReferences[$user['hits']]++;

        if(isset($user['applied'])){
            $timeToApply[$ip] = strtotime($user['applied']['apply']) - strtotime($user['applied']['start']);
            $totalTimeToApply += $timeToApply[$ip];

            $date = date("Y-m-d", strtotime($user['applied']['apply']));
            if(!isset($applicationsPerDay[$date]))
                $applicationsPerDay[$date] = 0;
            $applicationsPerDay[$date]++;

            if($timeToApply[$ip] < 5)
                $timeToApplyByRange["5  Sec"]++;
            else if($timeToApply[$ip] < 30)
                $timeToApplyByRange["30 Sec"]++;
            else if($timeToApply[$ip] < 60)
                $timeToApplyByRange["1  Min"]++;
            else if($timeToApply[$ip] < 120)
                $timeToApplyByRange["2  Min"]++;
            else if($timeToApply[$ip] < 300)
                $timeToApplyByRange["5  Min"]++;
            else if($timeToApply[$ip] < 1800)
                $timeToApplyByRange["30 Min"]++;
            else if($timeToApply[$ip] < 3600)
                $timeToApplyByRange["1 Hour"]++;
            else
                $timeToApplyByRange["1 Hr+ "]++;

        }
    }

    $avgTimeToApply = $totalTimeToApply / count($timeToApply);

    $maxVisits = 0;

    foreach ($visitsPerDay as $date => $count){
        $v = intval($count);
        if($v > $maxVisits)
            $maxVisits = $v;
    }
    foreach ($visitsPerDay as $date => $count){
        $v = intval($count) / $maxVisits * 100;
        $visitsPerDay[$date] = "$date |";
        for ($i = 0; $i < $v; $i++)
            $visitsPerDay[$date] .= "|";
        $visitsPerDay[$date] .= "   $count";
    }

    $maxVisits = 0;

    foreach ($visitsPerHour as $hour => $count){
        $v = intval($count);
        if($v > $maxVisits)
            $maxVisits = $v;
    }
    foreach ($visitsPerHour as $hour => $count){
        $v = intval($count) / $maxVisits * 50;
        $visitsPerHour[$hour] = "$hour:00 |";
        for ($i = 0; $i < $v; $i++)
            $visitsPerHour[$hour] .= "|";
        $visitsPerHour[$hour] .= "   $count";
    }

    ksort($visitsPerDay);
    ksort($visitsPerHour);
    ksort($applicationsPerDay);
    arsort($totalReferrers);
    arsort($totalPages);

    $medianHitsPerVisit = array_search(max($hits), $hits);
    $medianHitsPerVisitWithReferences = array_search(max($hitsWithReferences), $hitsWithReferences);
    $ipWithMostHits = array_search(max($ips), $ips);


    if (isset($_GET['pages'])) {
        echo "<h4>Total Pages:</h4>";
        foreach ($totalPages as $page => $count)
            echo "<p>$page: $count</p>";
        echo "<h4>Total Referrers:</h4>";
        foreach ($totalReferrers as $referrer => $count)
            echo "<p>$referrer: $count</p>";
    }else if (isset($_GET['users'])) {
        echo "<h4>Users:</h4>";
        foreach ($users as $user)
            echo "<p>$user</p>";
    }else if (isset($_GET['visits'])) {
        echo "<h4>Hits Per Day:</h4>";
        foreach ($visitsPerDay as $day => $count)
            echo "<p>$count</p>";

        echo "<h4>Hits Per Hour:</h4>"; 
        foreach ($visitsPerHour as $hour => $count)
            echo "<p>$count</p>";
    }else if (isset($_GET['applications'])) {
        echo "<h4>Total Applications: $totalApplyBtns</h4>";
        echo "<h4>Avg Time To Apply: " . substr(($totalTimeToApply / count($timeToApply)) . "", 0, 4) . "</h4>";
        echo "<h4>Time To Apply By Range:</h4>";
        foreach ($timeToApplyByRange as $range => $count)
            echo "<p>$range: ".getBarGraph($count/10) . "  $count</p>";
        echo "<h4>Applications Per Day:</h4>";
        foreach ($applicationsPerDay as $date => $count)
            echo "<p>$date: ".getBarGraph($count)."  $count</p>";
        echo "<h4>Time To Apply:</h4>";
        foreach ($timeToApply as $ip => $time)
            echo "<p>$ip: $time</p>";
    } else {
        echo "<h1>Visitor Stats</h1>";
        echo "<h4>Total Hits: $totalHits</h4>";
        echo "<h4>Total Unique Visits: $totalUniqueVisits</h4>";
        echo "<h4>Avg Hits Per Visit: " . substr(($totalHits / $totalUniqueVisits) . "", 0, 4) . "</h4>";
        echo "<h4>Avg Hits Per Connection: " . substr(($totalHits / count($ips)) . "", 0, 4) . "</h4>";
        echo "<h4>Median Hits: $medianHitsPerVisit</h4>";
        echo "<h4>Median Hits With Referrer: $medianHitsPerVisitWithReferences</h4>";
        echo "<h4><strike>Total Logins: $totalLogins</strike></h4>";
        echo "<h4>Total Connections: " . count($ips) . "</h4>";
        echo "<h4>Total Applys: $totalApplyBtns</h4>";
        echo "<h4>Avg Time To Apply: " . substr(($avgTimeToApply) . "", 0, 4) . " seconds</h4>";
    }

?>