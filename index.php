<?php
    $headers = []; 
    $cfg = null; 

    $headers["X-cfg-request"] = json_encode($_REQUEST);

    // try to load config
    $cfg_filename = __DIR__.'/config.json';
    if (file_exists($cfg_filename)){
        $headers["X-cfg-lastchange"] = date("Y-m-d H:i:s", filemtime($cfg_filename));
        $rawcfg = file_get_contents($cfg_filename);
        if ($rawcfg){
            $cfg = json_decode($rawcfg, true); 
        }
    }

    if ($cfg){
        $headers["X-cfg-urlcount"] = isset($cfg["urls"])?sizeof($cfg["urls"]):0;
        if ($_REQUEST["k-ma-path"]){
            // path provided, search for it
            $found = false; 

            if (isset($cfg["urls"])){
                foreach ($cfg["urls"] as $urlcfg){
                    if (strtolower($urlcfg["path"]) == strtolower($_REQUEST["k-ma-path"])){
                        $found = true; 
                        if (isset($urlcfg["permanent"]) && $urlcfg["permanent"]){
                            // permanent redirect
                            http_response_code(301);
                        } else {
                            // temporarly redirect
                            http_response_code(302);
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
            http_response_code(302);
            header("Location: ".$cfg["defaultURL"]);
        }
    } else {
        $headers["X-error"] = "no-config";
    }

    foreach ($headers as $header => $headerval){
        header($header.": ".$headerval);
    }
?>