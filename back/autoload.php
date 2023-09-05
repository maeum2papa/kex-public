<?

if(!file_exists('common/db.php')){
    exit('not found db config');
}


function autoload($param){

    $param = str_replace('\\','/',$param).".php";
    
    if(file_exists($param)){
        require_once $param;
    }else{
        exit('not found autoload param');
        exit;
    }


}

spl_autoload_register('autoload');
