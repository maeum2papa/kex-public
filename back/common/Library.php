<?
namespace common;
class Library{


    function __construct(){

        global $tpl,$db,$dev;

        $this->tpl = $tpl;
        $this->db = $db;
        $this->segment = explode('/',$_SERVER['PHP_SELF']);

    }


    #클래스 구하기
    function getClass($controller){

        $controller_file = str_replace('\\','/',$controller).".php";
        
        if(file_exists($controller_file)){
            $class = new $controller;
        }else{
            exit('not found class');
        }

        
        return $class;
    }




    #이메일체크
    function emailCheck($temp_email) {
    	return preg_match("/^[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)*@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)+$/", $temp_email);
    }

    #xml to array
    function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

        return $out;
    }




    #datetime to date
    function datetimeToDate($datetime){

        $date = substr($datetime,0,10);

        return $date;
    }



    #무작위문자(랜덤문자)
    function getRandomString($len = 10, $type = '') {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '0123456789';
        $special = '~!@#$%^&*()-_=+\\|[{]}:\,<.>/?';
        $key = '';
        $token = '';
        if ($type == '') {
            $key = $lowercase.$uppercase.$numeric;
        } else {
            if (strpos($type,'09') > -1) $key .= $numeric;
            if (strpos($type,'az') > -1) $key .= $lowercase;
            if (strpos($type,'AZ') > -1) $key .= $uppercase;
            if (strpos($type,'$') > -1) $key .= $special;
        }
        for ($i = 0; $i < $len; $i++) {
            $token .= $key[mt_rand(0, strlen($key) - 1)];
        }
        return $token;
    }




    #date ago (max:week)
    function dateAgo($data){

        $ori_data = $data;
        $data = strtotime($data);
        $today = time();
        $hour = 60*60;
        $day = $hour*24;

        $diff = abs($today-$data);



        if($diff<$day && $diff>$hour){
            $res = floor($diff/$hour).' 시간 전';
        }elseif($diff<$hour){
            $res = floor($diff/60).' 분 전';
        }else{
            $res = $ori_data;
        }


        return $res;

    }




    # https로 전환
    function chk_https(){
        if($_SERVER['HTTP_X_FORWARDED_PROTO']=='http'){
            header("location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            exit;
        }
    }


    # http로 전환
    function chk_http(){
        if($_SERVER['HTTP_X_FORWARDED_PROTO']!='http'){
            header("location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            exit;
        }
    }


    # 날짜 사이 기간 구하기
    function datePeriod($start_day,$end_day){

        $begin = new \DateTime($start_day);
        $end = new \DateTime($end_day);
        $end = $end->modify( '+1 day' );

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval ,$end);

        foreach($daterange as $date){
            $result['day'][] = $date->format("Y-m-d");
            $result['week'][] = date('w',strtotime($date->format("Y-m-d")));
            $result['month'][] = substr($date->format("Y-m-d"),0,7);
        }

        if($result['month']) $result['month'] = array_unique($result['month']);

        return $result;
    }

    # 암호화된 쿠키 생성
    function setCookie($key,$text,$time){
        $key = base64_encode($key);
        $text = base64_encode($text);
        $key = str_replace("==","",$key);
        $text = str_replace("==","",$text);
        setcookie($key, $text, time() + $time, "/");
    }

    # 암호화된 쿠키 가져오기
    function getCookie($key){
        $key = base64_encode($key);
        $key = str_replace("==","",$key);
        $data = $_COOKIE[$key]."==";
        return base64_decode($data);
    }

}
