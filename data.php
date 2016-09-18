<?php
    $log = array();
    $ipv6 = file_exists("/etc/pihole/.useIPv6");
    $hosts = file_exists("/etc/hosts") ? file("/etc/hosts") : array();
    #include file for syslog collection
    include 'syslog_data.php';

    /*******   Public Members ********/
    function getSummaryData() {
        global $ipv6;
        $log = readInLog();
        $domains_being_blocked = gravityCount() / ($ipv6 ? 2 : 1);

        $dns_queries_today = count(getDnsQueries($log));

        $ads_blocked_today = count(getBlockedQueries($log));

        $ads_percentage_today = $dns_queries_today > 0 ? ($ads_blocked_today / $dns_queries_today * 100) : 0;

        return array(
            'domains_being_blocked' => $domains_being_blocked,
            'dns_queries_today' => $dns_queries_today,
            'ads_blocked_today' => $ads_blocked_today,
            'ads_percentage_today' => $ads_percentage_today,
        );
    }

    function getOverTimeData() {
        $log = readInLog();
        $dns_queries = getDnsQueries($log);
        $ads_blocked = getBlockedQueries($log);

        $domains_over_time = overTime($dns_queries);
        $ads_over_time = overTime($ads_blocked);
        alignTimeArrays($ads_over_time, $domains_over_time);
        return Array(
            'domains_over_time' => $domains_over_time,
            'ads_over_time' => $ads_over_time,
        );
    }

    function getTopItems() {
        $log = readInLog();
        $dns_queries = getDnsQueries($log);
        $ads_blocked = getBlockedQueries($log);

        $topAds = topItems($ads_blocked);
        $topQueries = topItems($dns_queries, $topAds);

        return Array(
            'top_queries' => $topQueries,
            'top_ads' => $topAds,
        );
    }

    function getRecentItems($qty) {
        $log = readInLog();
        $dns_queries = getDnsQueries($log);
        return Array(
            'recent_queries' => getRecent($dns_queries, $qty)
        );
    }

    function getIpvType() {
        $log = readInLog();
        $dns_queries = getDnsQueries($log);
        $queryTypes = array();

        foreach($dns_queries as $query) {
            $info = trim(explode(": ", $query)[1]);
            $queryType = explode(" ", $info)[0];
            if (isset($queryTypes[$queryType])) {
                $queryTypes[$queryType]++;
            }
            else {
                $queryTypes[$queryType] = 1;
            }
        }

        return $queryTypes;
    }

    function getForwardDestinations() {
        $log = readInLog();
        $forwards = getForwards($log);
        $destinations = array();
        foreach ($forwards as $forward) {
            $exploded = explode(" ", trim($forward));
            $dest = $exploded[count($exploded) - 1];
            if (isset($destinations[$dest])) {
                $destinations[$dest]++;
            }
            else {
                $destinations[$dest] = 0;
            }
        }

        return $destinations;

    }

    function getQuerySources() {
        $log = readInLog();
        $dns_queries = getDnsQueries($log);
        $sources = array();
        foreach($dns_queries as $query) {
            $exploded = explode(" ", $query);
            $ip = hasHostName(trim($exploded[count($exploded)-1]));
            if (isset($sources[$ip])) {
                $sources[$ip]++;
            }
            else {
                $sources[$ip] = 1;
            }
        }
        arsort($sources);
        $sources = array_slice($sources, 0, 10);
        return Array(
            'top_sources' => $sources
        );
    }

        function getAllQueries() {
        $allQueries = array("data" => array());
        $log = readInLog();
        $dns_queries = getDnsQueriesAll($log);

        foreach ($dns_queries as $query) {
            $time = date_create(substr($query, 0, 16));
            $exploded = explode(" ", trim($query));
            $tmp = $exploded[count($exploded)-4];

            if (substr($tmp, 0, 5) == "query"){
              $type = substr($exploded[count($exploded)-4], 6, -1);
              $domain = $exploded[count($exploded)-3];
              $client = $exploded[count($exploded)-1];
              $status = "";
            }
            elseif (substr($tmp, 8, 9) == "dH"){
              $type = substr($exploded[count($exploded)-4], 0, -5);
              $domain = $exploded[count($exploded)-2];
              $client = $exploded[count($exploded)-1];
              //$client = $exploded[count($exploded)-1];
	      $status = "OK";
            }	

	    elseif (substr($tmp, 0, 2) == "DH"){
              $type = substr($exploded[count($exploded)-4], 0, -5);
              $domain = $exploded[count($exploded)-2];
              $client = $exploded[count($exploded)-1];
              //$client = ""
	      $status = "OK";
            }	  
 
	    elseif (substr($tmp, 0, 9) == "forwarded" ){
              $status="OK";
            }
            elseif (substr($tmp, strlen($tmp) - 12, 12)  == "gravity.list"  && $exploded[count($exploded)-5] != "read"){
              $status="Pi-holed";
            }

            if ( $status != ""){
              array_push($allQueries['data'], array(
                $time->format('Y-m-d\TH:i:s'),
                $type,
                $domain,
                hasHostName($client),
                $status,
              ));
            }


        }
        return $allQueries;
    }


    /******** Private Members ********/
    function gravityCount() {
        //returns count of domains in blocklist.
        $gravity="/etc/pihole/gravity.list";
        $swallowed = 0;
        $NGC4889 = fopen($gravity, "r");
        while ($stars = fread($NGC4889, 1024000)) {
          $swallowed += substr_count($stars, "\n");
        }
        fclose($NGC4889);

        return $swallowed;

    }

    function readInLog() {
        global $log;
        return count($log) > 1 ? $log :
            file("/var/log/pihole.log");
    }

    function getDnsQueries($log) {
        return array_filter($log, "findQueries");
    }

     function getDnsQueriesAll($log) {
      return array_filter($log, "findQueriesAll");
    }
    function getBlockedQueries($log) {
        return array_filter($log, "findAds");
    }
    function getForwards($log) {
        return array_filter($log, "findForwards");
    }


    function topItems($queries, $exclude = array(), $qty=10) {
        $splitQueries = array();
        foreach ($queries as $query) {
            $exploded = explode(" ", $query);
            $domain = trim($exploded[count($exploded) - 3]);
            if (!isset($exclude[$domain])) {
                if (isset($splitQueries[$domain])) {
                    $splitQueries[$domain]++;
                }
                else {
                    $splitQueries[$domain] = 1;
                }
            }
        }
        arsort($splitQueries);
        return array_slice($splitQueries, 0, $qty);
    }

    function overTime($entries) {
        $byTime = array();
        foreach ($entries as $entry) {
            $time = date_create(substr($entry, 0, 16));
            $hour = $time->format('G');

            if (isset($byTime[$hour])) {
                $byTime[$hour]++;
            }
            else {
                $byTime[$hour] = 1;
            }
        }
        return $byTime;
    }

    function alignTimeArrays(&$times1, &$times2) {
        $max = max(array(max(array_keys($times1)), max(array_keys($times2))));
        $min = min(array(min(array_keys($times1)), min(array_keys($times2))));

        for ($i = $min; $i <= $max; $i++) {
            if (!isset($times2[$i])) {
                $times2[$i] = 0;
            }
            if (!isset($times1[$i])) {
                $times1[$i] = 0;
            }
        }

        ksort($times1);
        ksort($times2);
    }

    function getRecent($queries, $qty){
        $recent = array();
        foreach (array_slice($queries, -$qty) as $query) {
            $queryArray = array();
            $exploded = explode(" ", $query);
            $time = date_create(substr($query, 0, 16));
            $queryArray['time'] = $time->format('h:i:s a');
            $queryArray['domain'] = trim($exploded[count($exploded) - 3]);
            $queryArray['ip'] = trim($exploded[count($exploded)-1]);
            array_push($recent, $queryArray);

        }
        return array_reverse($recent);
    }

    function findQueriesAll($var) {
        return strpos($var, ": query[") || strpos($var, "gravity.list") || strpos($var, " DHCP")  || strpos($var, ": forwarded") !== false;
    }


    function findAds($var) {
      $exploded = explode(" ", $var);
      $tmp = $exploded[count($exploded)-4];
      $tmp2 = $exploded[count($exploded)-5];
      //filter out bad names and host file reloads:
      return (substr($tmp, strlen($tmp) - 12, 12)  == "gravity.list" && $tmp2 != "read") ;
    }

    function findForwards($var) {
        return strpos($var, ": forwarded") !== false;
    }

    function hasHostName($var){
        global $hosts;
        foreach ($hosts as $host){
            $x = preg_split('/\s+/', $host);
            if ( $var == $x[0] ){
                $var = $x[1] . "($var)";
            }
        }
        return $var;
    }
?>
