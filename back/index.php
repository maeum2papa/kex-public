<?
session_start();
ini_set('display_errors',0);
// header("Content-Type: text/html; charset=UTF-8");
header('Content-Type: text/json');
header('Access-Control-Allow-Origin: *');

include 'autoload.php';


$db = new common\DBLibrary();
$lib = new common\Library();

if($lib->segment[2]!=''){

    $namespace = "controller\\".$lib->segment[2];
    $class = $lib->getClass($namespace);
}

if($lib->segment[3]!=''){
    $method = $lib->segment[3];
    if(method_exists($class,$method)){
        $class->$method();
    }else{
        exit('not found file');
    }
}