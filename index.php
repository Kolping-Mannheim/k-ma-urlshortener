<?php
    $cfg = null; 

    // try to load config
    $cfg_filename = __DIR__.'/config.json';
    if (file_exists($cfg_filename)){
        $rawcfg = file_get_contents($cfg_filename);
        if ($rawcfg){
            $cfg = json_decode($rawcfg, true); 
        }
    }

    if ($cfg){
        $found = false; 
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

                exit(); 
            }
        }

        if (!$found){
            echo 'Sorry, der Link "'.$_REQUEST["k-ma-path"].'" wurde leider nicht gefunden. <a href="'.$cfg["defaultURL"].'">Kehre hier zu unserer Homepage zurück.</a>';
        }
    }
?>