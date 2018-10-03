<?php

function refreshMeta($metaFilePath,$dataDir){
    $json=[
        "avatar"=>"https://sinri.cc/frontend/static/img/AlipayUkanokan258.png",
        "site_title"=>"Welcome thou vistor!",
        "site_declaration"=>"Powered by and all right reserved to, Copyright 2018 Sinri Edogawa",
        "menu"=>[],
    ];
    if(file_exists($metaFilePath)){
        $content=file_get_contents($metaFilePath);
        $parsed=json_decode($content,true);

        foreach(['avatar','site_title','site_declaration'] as $field){
            if(isset($parsed[$field])){
                $json[$field]=$parsed[$field];
            }
        }
    }

    if ($handle = opendir($dataDir)) {
        echo "Data Dir: $handle\n";
        echo "First Level Items:\n";

        /* This is the correct way to loop over the directory. */
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                echo " |".PHP_EOL;
                echo " +--+ $entry".PHP_EOL;
                if(is_dir($dataDir.'/'.$entry)){
                    $dirItem=[
                        "type"=>"folder",
                        "title"=>$entry,
                        "pages"=>[],
                    ];
                    if($subHandle=opendir($dataDir.'/'.$entry)){
                        while(false!==($file=readdir($subHandle))){
                            if(preg_match('/\.md$/',$file)){
                                echo " |  |".PHP_EOL;
                                echo " |  +-- ".$file.PHP_EOL;
                                $dirItem['pages'][]=[
                                    "type"=>"markdown",
                                    "title"=>queryTitleOfMarkdownFile($dataDir.'/'.$entry,$file),
                                    "link"=>$entry.'/'.$file,
                                ];
                            }
                        }
                    }
                    if(!empty($dirItem['pages'])){
                        // index.md first
                        usort($dirItem['pages'],function($a,$b){
                            //echo "CHECK ".$a['link']."->".(strpos($a['link'],'/index.md')?'Y':'N').PHP_EOL;
                            //echo "CHECK ".$b['link']."->".(strpos($b['link'],'/index.md')?'Y':'N').PHP_EOL;
                            if($a['type']=='markdown' && strpos($a['link'],'/index.md')!==false){
                                return -1;
                            }
                            if($b['type']=='markdown' && strpos($b['link'],'/index.md')!==false){
                                return 1;
                            }
                            if($a['title']>$b['title'])return 1;
                            else if($a['title']<$b['title'])return -1;
                            else return 0;
                        });

                        $json['menu'][]=$dirItem;
                    }
                }else{
                    if(preg_match('/\.md$/',$entry)){
                        $json['menu'][]=[
                            "type"=>"markdown",
                            "title"=>queryTitleOfMarkdownFile($dataDir,$entry),
                            "link"=>$entry,
                        ];
                    }
                }
            }
        }

        echo " |".PHP_EOL;
        echo " FIN".PHP_EOL;

        closedir($handle);

        // welcome.md first
        usort($json['menu'],function($a,$b){
            if($a['type']=='markdown' && $a['link']=='welcome.md'){
                return -1;
            }
            if($b['type']=='markdown' && $b['link']=='welcome.md'){
                return 1;
            }
            if($a['title']<$b['title'])return -1;
            elseif($a['title']>$b['title'])return 1;
            else return 0;
        });

        $ready = json_encode($json,JSON_PRETTY_PRINT);

        $bytes=file_put_contents($metaFilePath,$ready);
        echo ($bytes?"Done ":"FAILED")."! $bytes BYTES WRITTEN!".PHP_EOL;
    }
}

function queryTitleOfMarkdownFile($dir,$file){
    $content=file_get_contents($dir.'/'.$file);
    if(preg_match('/^#\s+(.+)/',$content,$matches)){
        return $matches[1];
    }else{
        return substr($file,0,strlen($file)-3);
    }
}