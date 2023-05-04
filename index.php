<?php
    $headers = [];
    $stat = [
        "ts" => date("Y-m-d H:i:s")
    ]; 

    $statsFromServer = [
        "protocol" => "REQUEST_SCHEME",
        "host" => "HTTP_HOST",
        "lang" => "HTTP_ACCEPT_LANGUAGE",
        "ua" => "HTTP_USER_AGENT",
        "ref" => "HTTP_REFERER",
    ];
    foreach ($statsFromServer as $key => $srvkey){
        if (isset($_SERVER[$srvkey])){
            $stat[$key] = $_SERVER[$srvkey]; 
        }
    }
    // $stat["dbg"] = $_SERVER; 

    $cfg = null; 
    $httpcode = 200; 

    $headers["X-cfg-request"] = json_encode($_REQUEST);

    // try to load config
    $cfg_filename = __DIR__.'/config/config.json';
    if (file_exists($cfg_filename)){
        $headers["X-cfg-lastchange"] = date("Y-m-d H:i:s", filemtime($cfg_filename));
        $rawcfg = file_get_contents($cfg_filename);
        if ($rawcfg){
            $cfg = json_decode($rawcfg, true); 
        }
    }

    if ($cfg){
        $headers["X-cfg-urlcount"] = isset($cfg["urls"])?sizeof($cfg["urls"]):0;
        if (isset($_REQUEST["k-ma-path"]) && strlen($_REQUEST["k-ma-path"]) > 0){
            $stat["path"] = $_REQUEST["k-ma-path"]; 
            // path provided, search for it
            $found = false; 

            if (isset($cfg["urls"])){
                foreach ($cfg["urls"] as $urlcfg){
                    if (strtolower($urlcfg["path"]) == strtolower($_REQUEST["k-ma-path"])){
                        $found = true; 
                        if (isset($urlcfg["permanent"]) && $urlcfg["permanent"]){
                            // permanent redirect
                            $httpcode = 301; 
                        } else {
                            // temporarly redirect
                            $httpcode = 302; 
                        }
                        header("Location: ".$urlcfg["url"]);
                    }
                }
            }

            if (!$found){
                echo 'Sorry, der Link "'.$_REQUEST["k-ma-path"].'" wurde leider nicht gefunden. <a href="'.$cfg["defaultURL"].'">Kehre hier zu unserer Homepage zurück.</a>';
            }
        } else {
            // default with 
            $stat["path"] = false; 
            $httpcode = 302; 
            header("Location: ".$cfg["defaultURL"]);
        }
    } else {
        $headers["X-error"] = "no-config";
    }

    $stat["httpcode"] = $httpcode; 
    http_response_code($httpcode); 

    foreach ($headers as $header => $headerval){
        header($header.": ".$headerval);
    }

    try {
        file_put_contents(__DIR__.'/stats/stats.json', json_encode($stat)."\n", FILE_APPEND);
    } catch (Exception $e){

    }
?>