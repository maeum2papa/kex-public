<?
namespace controller;
use common;
use common\Encrypt;
use common\SF;
use common\GS;
use firebase\jwt\JWT;
use firebase\jwt\Key;

class Api{

    function __construct(){

        global $db,$lib;
        
        $this->db = $db;
        $this->lib = $lib;
        $this->jwt->key = '';
        $this->post = json_decode(file_get_contents( 'php://input' ),JSON_UNESCAPED_UNICODE);
    
        $this->post = Encrypt::de($this->post['body']);

    }


    function test(){
        // $gs = new GS;
        // $a = $gs->get("/orders/308417140499");
        // print_r($a);
    }

    #토큰 체크
    function chk(){

        $res = array('msg'=>'no');

        $accessToken = $_SERVER["HTTP_AUTHORIZATION"];

        $decoded = JWT::decode($accessToken, new Key($this->jwt->key,'HS256'));
        $decoded = (array)$decoded;

        if(!$decoded['id']){

            if($res['msg']=='no'){
                header("HTTP/1.0 400 Bad Request");
            }

            echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /***
     * /api/login
     */
    function login(){

        $res = array('msg'=>'no');
        
        if(!$this->post['name'] || !$this->post['mobile']){
            
            header("HTTP/1.0 400 Bad Request");
            echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
            exit;

        }else{

            $accessTokenPayload = array(
                'id'=>$this->post['name']."|".$this->post['mobile'],
                'iat'=>time(),
                'exp'=>time()+(60*30)
            );

            $res['msg'] = 'ok';
            $res['access_token'] = JWT::encode($accessTokenPayload, $this->jwt->key,'HS256');
            
            
            echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);

        }
    }


    /***
     * /api/auth
     */
    function auth(){

        $res = array('msg'=>'no');

        $accessToken = $_SERVER["HTTP_AUTHORIZATION"];

        if($accessToken){
            try{
                $decoded = JWT::decode($accessToken, new Key($this->jwt->key,'HS256'));
                $decoded = (array)$decoded;
                $res['msg'] = 'ok';
                $res['id'] = $decoded['id'];
            }catch(Exception $e){

            }
        }

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }


    /**
     * sf 운송장 찾기
     * /api/search
     * 
     * SFNumber:SF 운송장 번호
		mobileLast:전화번호 마지막 4자리
     */
    function search(){

        $res = array('msg'=>'no');

        //토큰 검증
        $this->chk(); 

        ### START

        if($this->post['SFNumber'] && $this->post['mobileLast']){

            //접수된 SF운송장인지 체크
            list($chkResult) = $this->db->query("select sfNumber from w_ship where sfNumber=?","s",$this->post['SFNumber']);
            if($chkResult['sfNumber']){

                $res = array('msg'=>'ok','data'=>array('msg'=>"no3"));

            }else{
                $sf = new SF;
                
                $r = $sf->post("/openapi/api/dispatch",array('sfWaybillNo'=>$this->post['SFNumber']));

                $t = $sf->post("/openapi/api/dispatch",array('sfWaybillNo'=>$this->post['SFNumber']),"IUOP_QUERY_TRACK");

                if($t['data'][0]['trackDetailItems'][0]['opCode']){

                    $res = array('msg'=>'ok','data'=>array('msg'=>"no3"));

                }else{

                    
                    if(($this->post['mobileLast']==substr($r['data']['senderInfo']['phoneNo'],-4)) || ($this->post['mobileLast']==substr($r['data']['senderInfo']['telNo'],-4))){

                        if(count($r['data']['parcelInfoList'])>1){//운송장번호 하나에 소포가 두개일 수 없다.
                            $res = array('msg'=>'ok','data'=>array('msg'=>"no"));
                        }elseif($r['data']['parcelInfoList'][0]['quantity']>1){
                            $res = array('msg'=>'ok','data'=>array('msg'=>"no2"));//수량이 두개 이상이면 접수 불가
                        }else{
                            $data = array(
                                "SFNumber"=>$this->post['SFNumber'],
                                "sender"=>$r['data']['senderInfo']['contact'],
                                "postNumber"=>$r['data']['senderInfo']['postCode'],
                                "address"=>'******, '.$r['data']['senderInfo']['regionThird'].' '.$r['data']['senderInfo']['regionSecond'].' '.$r['data']['senderInfo']['regionFirst']
                            );
                            
                            $res = array('msg'=>'ok','data'=>$data);
                        }
                    }

                }
            }
        }
        
        //SF1334456901862'));//5485
        ### END

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        
        
        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);

    }



    /**
     * /api/cart
     * 
     * params
     * cart : 담긴 정보
     * 
     */
    function cart(){

        $res = array('msg'=>'no');

        //토큰 검증
        $this->chk(); 

        ### START

        if($this->post['cart']){

            $sf = new SF;
            
            $accessToken = $_SERVER["HTTP_AUTHORIZATION"];

            $decoded = JWT::decode($accessToken, new Key($this->jwt->key,'HS256'));
            $decoded = (array)$decoded;
            list($name,$mobile) = explode('|',$decoded['id']);

            //1. 디비에서 sfnumber가 일치하는 데이터들을 가져온다.
            //2. 가져온 데이터의 물품가액이 USD 일텐데 이를 USD에 고시환율을 곱한 값을 리턴한다.
            //4. 단 고시환율이 적용된 물품가액이 50만원을 넘는 경우에는 50만원으로 고정한다.
            //5. 실제물품가액과 50만원으로 제한된 물품가액을 저장하고 GS에 데이터 보낼때는 50만원으로 제한된 물품가액을 보낸다.

        
            list($gosiTmp) = $this->db->query("select data from w_exchange where startDate<=now() and endDate>=now() order by code desc limit 1");
            $gosi = $gosiTmp['data'];

            if($gosi>0){
                foreach($this->post['cart'] as $k=>$v){
                    unset($tmp);

                    $r = $sf->post("/openapi/api/dispatch",array('sfWaybillNo'=>$v['SFNumber']));
                    if($r['code']==0){

                        $tmp['SFNumber'] = $v['SFNumber'];
                        $tmp['product'] = '의류';
                        $tmp['ea'] = $r['data']['parcelInfoList'][0]['quantity'];
                        $tmp['price'] = ceil(($r['data']['parcelInfoList'][0]['amount'] * $tmp['ea']) * $gosi);

                        if($tmp['price']>500000){
                            $tmp['ori_price'] = $tmp['price'];
                            $tmp['price'] = 500000;
                        }

                        
                    }

                    $list[] = $tmp;
                }

                $res = array('msg'=>'ok','list'=>$list);
            }
        }

        ### END

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }




    /**
     * /api/register
     * 
     * params
     * cart : 담긴 정보
     */
    function register(){

        $res = array('msg'=>'no');

        //토큰 검증
        $this->chk(); 

        ### START

        if($this->post['cart']){

            $sf = new SF;
            $gs = new GS;

            $accessToken = $_SERVER["HTTP_AUTHORIZATION"];

            $decoded = JWT::decode($accessToken, new Key($this->jwt->key,'HS256'));
            $decoded = (array)$decoded;
            list($name,$mobile) = explode('|',$decoded['id']);

            list($gosiTmp) = $this->db->query("select data from w_exchange where startDate<=now() and endDate>=now() order by code desc limit 1");
            $gosi = $gosiTmp['data'];

            if($gosi>0){

                foreach($this->post['cart'] as $k=>$v){
                    unset($tmp,$gsResult,$query);

                    $r = $sf->post("/openapi/api/dispatch",array('sfWaybillNo'=>$v['SFNumber']));
                    if($r['code']==0){

                        $insertData['status'] = '예약 접수 완료'; //화물상태
                        $insertData['kpiStatus'] = ''; //KPI상태
                        $insertData['sfNumber'] = $v['SFNumber'];
                        //$insertData['resNumber'] = ''; //승인번호
                        //$insertData['deliveryNumber'] = ''; //택배사 운송장번호
                        $insertData['orderNumber'] = $r['data']['customerOrderNoTwo']; //고객오더번호2
                        $insertData['sender'] = $name; //발송자명
                        $insertData['mobile'] = $mobile; //전화번호
                        $insertData['iuopSender'] = $r['data']['senderInfo']['contact']; //발송자명 IUOP 오더 상
                        $insertData['item'] = '의류'; //물품종류
                        //$insertData['itemPrice'] = 0; //물품가액
                        $insertData['itemPrice2'] = ceil($r['data']['declaredValue'] * $gosi); //진짜 물품가액
                        //$insertData['reservationDate'] = ''; //예약일시
                        //$insertData['registerDate'] = ''; //접수시간
                        //$insertData['collectionDate'] = ''; //집하시간
                        //$insertData['deliveryDate'] = ''; //배송시간


                        if($insertData['itemPrice2']>500000){
                            $insertData['itemPrice'] = 500000;
                        }else{
                            $insertData['itemPrice'] = $insertData['itemPrice2'];
                        }


                        //GS에 예약
                        $gsResult = $gs->reservation($insertData['sfNumber'],$insertData['itemPrice'],1);
                        $insertData['resNumber'] = $gsResult['orderNo']; //승인번호
                        $insertData['reservationDate'] = date('Y-m-d H:i:s'); //접수시간


                        //인터넷 상태가 불안정한 경우 대비
                        if($insertData['resNumber'] && $insertData['orderNumber'] && $insertData['iuopSender']){

                            $query = "insert into w_ship set status=?, 
                                        kpiStatus=?, 
                                        sfNumber=?, 
                                        orderNumber=?, 
                                        sender=?, 
                                        mobile=?, 
                                        iuopSender=?, 
                                        item=?,
                                        itemPrice2=?,
                                        itemPrice=?,
                                        resNumber=?,
                                        reservationDate=?
                                        ";

                            if($this->db->query($query,"ssssssssiiss",$insertData)){
                                $tmp['SFNumber'] = $insertData['sfNumber'];
                                $tmp['reservationNumber'] = $insertData['resNumber'];
                            }
                        }
                        
                    }

                    $list[] = $tmp;
                }


                /*
                $test = array(
                    array(
                        'SFNumber'=>'SF111111111',
                        'reservationNumber'=>'R11111111111'
                    )
                );

                $list = $test;
                */

                $res = array('msg'=>'ok','list'=>$list);
            }
        }

        ### END

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }



    /**
     * /api/inquire
     */
    function inquire(){

        $res = array('msg'=>'no');

        //토큰 검증
        $this->chk(); 

        ### START

        $accessToken = $_SERVER["HTTP_AUTHORIZATION"];

        $decoded = JWT::decode($accessToken, new Key($this->jwt->key,'HS256'));
        $decoded = (array)$decoded;
        list($name,$mobile) = explode('|',$decoded['id']);

        /*
        1. 발송자명과 전화번호로 3개월 이내에 데이터를 가져온다.
        */

        $query = "select * from w_ship where sender=? and mobile=? order by reservationDate desc";
        $result  = $this->db->query($query,"ss",array($name,$mobile));
        
        if(count($result)>=1){
            foreach($result as $k=>$v){
                $list[] = array(
                    'no'=>($k+1),
                    'SFNumber'=>$v['sfNumber'],
                    'reservationNumber'=>$v['resNumber'],
                    'date'=>date('Y.m.d',strtotime($v['reservationDate'])),
                    'status'=>$v['status']
                );
            }
        }                                
        
        /*
        $test = array(
            array(
                'no'=>1,
                'SFNumber'=>'SF111111111',
                'reservationNumber'=>'1111111111111',
                'date'=>'2023.5.10',
                'status'=>'접수'
            ),
            array(
                'no'=>2,
                'SFNumber'=>'SF111111111',
                'reservationNumber'=>'1111111111111',
                'date'=>'2023.5.10',
                'status'=>'접수'
            ),
            array(
                'no'=>3,
                'SFNumber'=>'SF111111111',
                'reservationNumber'=>'1111111111111',
                'date'=>'2023.5.10',
                'status'=>'접수'
            )
        );
        
        $list = $test;
        */
        if($list){
            $res = array('msg'=>'ok','list'=>$list);
        }


        ### END

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }



    /**
     * GS에서 화물 상태 변경시 호출
     * https://도메인/api/gs
     * 
     * Authorization:8D15F81E81B5E475EBDD8FD458591
     * Content-Type:application/json
     */
    function gs(){
        $this->post = json_decode(file_get_contents( 'php://input' ),JSON_UNESCAPED_UNICODE);
        
        $this->lib->log("SFKEX<-GS","/api/gs",json_encode($this->post,JSON_UNESCAPED_UNICODE));

        $res = array('msg'=>'no');
        $gsStatus = array(
            'RESERVED'=>'예약 접수 완료',
            'PRINTED'=>'운송장 출력 완료',
            'CONFIRMED'=>'예약 확정(집하요청) 완료',//예약 확정(집하요청) 완료
            'RECEIPTED'=>'택배 접수 완료',
            'GATHERED'=>'배송매니저 수거 완료 (집하완료)',//배송매니저 수거 완료 (집하완료)
            'ARRIVED'=>'도착점포 입고 완료 (점포 도착)',//도착점포 입고 완료 (점포 도착)
            'DELIVERED'=>'배송완료 (고객수취 완료)',//배송완료 (고객수취 완료)
            'CANCELLED'=>'예약 취소',
            'RETURNED'=>'반송'
        );

        $gsAndSf = array(
            'RECEIPTED'=>array("54","","pick up"),
            'GATHERED'=>array("649","","in transit"),
            'DELIVERED'=>array("649","","in transit"),
            'CANCELLED'=>array("70","7","Change of delivery / pick up time"),
        );


        $key = $_SERVER["HTTP_AUTHORIZATION"];

        if($key==''){
            
            $this->post = json_decode(file_get_contents( 'php://input' ),JSON_UNESCAPED_UNICODE);   
            
            if(count($this->post['statuses'])>0){
                
                foreach($this->post['statuses'] as $k=>$v){
                    unset($resTmp);
                    //$v['orderNo'] //승인번호
                    //$v['status'] //상태코드
                    //$v['time'] //처리시간
                    //$v['invoiceNo'] //운송장번호

                    $v['time'] = str_replace("T"," ",$v['time']);

                    $query = "select id,sfNumber,resNumber from w_ship where resNumber=? order by id desc limit 1";
                    list($ship) = $this->db->query($query,"s",array($v['orderNo']));
                    
                    if($ship['id']){
                        
                        switch($v['status']){
                            case 'RESERVED'://예약 접수 완료
                            case 'CONFIRMED'://CONFIRMED
                            case 'ARRIVED'://점포도착
                            case 'RETURNED'://반송
                                
                                // $query = "update w_ship set status=? where id=?";
                                // $this->db->query($query,"si",array($gsStatus[$v['status']],$ship['id']));
                                $queryArray[] = "update w_ship set status='".$gsStatus[$v['status']]."' where id='".$ship['id']."'";
                                
                                if($v['status']=='RECEIPTED'){
                                    $sendSfData[] = array(
                                        "opCode" =>$gsAndSf[$v['status']][0],
                                        "reasonCode" =>$gsAndSf[$v['status']][1],
                                        "sfWaybillNo" => $ship['sfNumber'],
                                        "zoneCode"=>"ICN01D",
                                        "barOprCode"=>"97134154",
                                        "localTm"=>$v['time'],
                                        "gmt"=>"GMT+09:00"
                                    );
                                }

                            break;

                            case 'PRINTED'://운송장 출력 완료

                                $queryArray[] = "update w_ship set status='".$gsStatus[$v['status']]."', deliveryNumber='".$v['invoiceNo']."' where id='".$ship['id']."'";


                            break;

                            case 'RECEIPTED'://택배 접수 완료

                                $queryArray[] = "update w_ship set status='".$gsStatus[$v['status']]."',registerDate='".$v['time']."', deliveryNumber='".$v['invoiceNo']."' where id='".$ship['id']."'";
                                
                                $sendSfData[] = array(
                                    "opCode" =>$gsAndSf[$v['status']][0],
                                    "reasonCode" =>$gsAndSf[$v['status']][1],
                                    "sfWaybillNo" => $ship['sfNumber'],
                                    "zoneCode"=>"ICN01D",
                                    "barOprCode"=>"97134154",
                                    "localTm"=>$v['time'],
                                    "gmt"=>"GMT+09:00"
                                );

                            break;

                            case 'GATHERED': //집하완료

                                // $query = "update w_ship set status=?,collectionDate=? where id=?";
                                // $this->db->query($query,"ssi",array($gsStatus[$v['status']],$v['time'],$ship['id']));
                                $queryArray[] = "update w_ship set status='".$gsStatus[$v['status']]."',collectionDate='".$v['time']."' where id='".$ship['id']."'";
                                $queryArray[] = "update w_ship set kpiStatus='' where id='".$ship['id']."'";
                                
                                $sendSfData[] = array(
                                    "opCode" =>$gsAndSf[$v['status']][0],
                                    "reasonCode" =>$gsAndSf[$v['status']][1],
                                    "sfWaybillNo" => $ship['sfNumber'],
                                    "zoneCode"=>"ICN01D",
                                    "barOprCode"=>"97134154",
                                    "localTm"=>$v['time'],
                                    "gmt"=>"GMT+09:00"
                                );

                            break;

                            case 'DELIVERED': //배송완료 (고객수취 완료)

                                // $query = "update w_ship set status=?,deliveryDate=? where id=?";
                                // $this->db->query($query,"ssi",array($gsStatus[$v['status']],$v['time'],$ship['id']));
                                $queryArray[] = "update w_ship set status='".$gsStatus[$v['status']]."',deliveryDate='".$v['time']."' where id='".$ship['id']."'";
                                $queryArray[] = "update w_ship set kpiStatus='' where id='".$ship['id']."'";
                                
                                $sendSfData[] = array(
                                    "opCode" =>$gsAndSf[$v['status']][0],
                                    "reasonCode" =>$gsAndSf[$v['status']][1],
                                    "sfWaybillNo" => $ship['sfNumber'],
                                    "zoneCode"=>"ICN01D",
                                    "barOprCode"=>"97134154",
                                    "localTm"=>$v['time'],
                                    "gmt"=>"GMT+09:00"
                                );

                            break;

                            case 'CANCELLED': //예약 취소

                                // $query = "update w_ship set status=? where id=?";
                                // $this->db->query($query,"si",array($gsStatus[$v['status']],$ship['id']));
                                $queryArray[] = "update w_ship set status='".$gsStatus[$v['status']]."' where id='".$ship['id']."'";
                                
                                $sendSfData[] = array(
                                    "opCode" =>$gsAndSf[$v['status']][0],
                                    "reasonCode" =>$gsAndSf[$v['status']][1],
                                    "sfWaybillNo" => $ship['sfNumber'],
                                    "zoneCode"=>"ICN01D",
                                    "barOprCode"=>"97134154",
                                    "localTm"=>$v['time'],
                                    "gmt"=>"GMT+09:00"
                                );

                            break;
                        }       

                    }


                    //GS에 응답 해주기위해 데이터 수집
                    $resTmp['orderNo'] = $ship['resNumber'];
                    $resTmp['status'] = $v['status'];
                    $resTmp['resultCode'] = '00000';
                    $resTmp['resultMessage'] = '성공';

                    $res['results'][] = $resTmp;
                    
                }
                
                $res['msg']= "ok";
                
                if(count($sendSfData)>0){

                    // test
                    // $jsonData = array(
                    //     "sysCode"=>"GS-CVSnet-KR",
                    //     "sign"=>"802fbf34fbebbda02bd8442e0b550273baf5486b96dc7b3427d2ee01635e857c",
                    //     "data"=>$sendSfData
                    // );

                    $jsonData = array(
                        "sysCode"=>"GS-CVSnet-KR",
                        "sign"=>"6041ccc22761db00368c9be4e4ad8251345f71c6d96325011788a3334e97b46f",
                        "data"=>$sendSfData
                    );
                    
                    //로컬로 자료 보내서 엑셀 만들어서 sf에 전달
                    $header = array(
                        'Content-Type: application/json; charset=utf-8'
                    );
            
                    $ch = curl_init(); //curl 초기화
                    //curl_setopt($ch, CURLOPT_URL, "https://ibu-gts.sit.sf-express.com:45580/gts-tc/api/track/receive"); //테스트
                    curl_setopt($ch, CURLOPT_URL, "https://ibu-gts.sf-express.com/gts-tc/api/track/receive"); //테스트
                    curl_setopt($ch, CURLOPT_HEADER, true);//헤더 정보를 보내도록 함(*필수)
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //header 지정하기
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonData,JSON_UNESCAPED_UNICODE));       //POST data
                    curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
                    
                    curl_exec($ch);
                    curl_close($ch);
                    
                }

                if(count($queryArray)>0){
                    foreach($queryArray as $v2){
                        $this->db->query($v2);
                    }
                }

            }
            
            /*
            {
                "statuses" : [
                    {
                        "orderNo": "991234567890",
                        "status": "PRINTED",
                        "time": "2021-05-14T10:38:22",
                        "invoiceNo": "163000309745"
                    },
                    {
                        "orderNo": "991234567890",
                        "status": "RECEIPTED",
                        "time": "2021-05-14T10:39:13",
                        "storeName": "접수점포명",
                        "invoiceNo": "163000309745"
                    },
                    {
                        "orderNo": "990987654321",
                        "status": "DELIVERED",
                        "time": "2021-05-16T05:34:13"
                    }
                ]
            }
            */
            /*
            {
                "results" : [
                    {
                        "orderNo": "991234567890",
                        "status": "PRINTED",
                        "resultCode": "00000",
                        "resultMessage": "성공"
                    },
                    {
                        "orderNo": "991234567890",
                        "status": "RECEIPTED",
                        "resultCode": "ERR-001",
                        "resultMessage": "내부 오류 발생"
                    },
                    {
                        "orderNo": "990987654321",
                        "status": "DELIVERED",
                        "resultCode": "99999",
                        "resultMessage": "삭제된 데이터입니다."
                    }
                ]
            }
            */
            
        }else{
            $res = array('msg'=>'Key Error');
        }

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        $this->lib->log("SFKEX->GS","/api/gs",json_encode($res,JSON_UNESCAPED_UNICODE));

        echo json_encode($res,JSON_UNESCAPED_UNICODE);

    }

    /**
     * sf로 엑셀 파일 sftp로 전송
     */
    function sfxls(){

        // $filename = "sf_sftp.xls";

        // header("Content-type: application/vnd.ms-excel");
        // header("Content-Disposition: attachment; filename=".$filename);
        // header("Content-Description: PHP4 Generated Data");

        $this->post = json_decode(file_get_contents( 'php://input' ),JSON_UNESCAPED_UNICODE);

        

        if(count($this->post)>0){

            ob_start();


            echo "
            <html>
            <head>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8'> 
            <style type='text/css'>
            
            td{mso-number-format:'\@'}
            </style>
            </head>
            
            <body>";
            
            echo "
                <table>
                    <tr>
                        <th>opCode</th>
                        <th>opName</th>
                        <th>reasonCode</th>
                        <th>reasonRemark</th>
                        <th>agentOpCode</th>
                        <th>agentReasonCode</th>
                        <th>sfWaybillNo</th>
                        <th>agentWaybillNo</th>
                        <th>zoneCode</th>
                        <th>zoneName</th>
                        <th>barOprCode</th>
                        <th>opAttachInfo</th>
                        <th>contnrCode</th>
                        <th>sortingInfo</th>
                        <th>flightNo</th>
                        <th>vessel</th>
                        <th>voyage</th>
                        <th>courierCode</th>
                        <th>batchCode</th>
                        <th>phone</th>
                        <th>subbillPieceQty</th>
                        <th>weightQty</th>
                        <th>feeAmt</th>
                        <th>truckPlateOde</th>
                        <th>srcContnrCode</th>
                        <th>scheduleCode</th>
                        <th>accountantCode</th>
                        <th>trackCountry</th>
                        <th>trackProvince</th>
                        <th>trackCity</th>
                        <th>trackAddr</th>
                        <th>trackPostCode</th>
                        <th>localTm</th>
                        <th>gmt</th>
                        <th>Phonezone</th>
                        <th>agentTrackRemark</th>	
                        <th>otherInfo</th>
                        <th>volume</th>
                        <th>length</th>
                        <th>width</th>
                        <th>height</th>
                        <th>extendAttach1</th>
                        <th>extendAttach2</th>
                        <th>extendAttach3</th>
                    </tr>
            ";

            foreach($this->post as $v){
                
            echo "<tr>
                <td>".$v['opCode']."</td>
                <td></td>
                <td>".$v['reasonCode']."</td>
                <td></td>
                <td></td>
                <td></td>
                <td>".$v['sfWaybillNo']."</td>
                <td></td>
                <td>".$v['zoneCode']."</td>
                <td></td>
                <td>".$v['barOprCode']."</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>".$v['localTm']."</td>
                <td>".$v['gmt']."</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
            }


            echo "</table></body></html>";

            $contents = ob_get_contents();
            ob_end_clean();


            $sf = new SF;

            //$fp = fopen('./data/data.xls', 'w');
            // fwrite($fp, $contents);
            // fclose($fp);

            $sf->upload("/files/".time().".xls",$contents);
            
        }

    }


    
    /**
     * batch
     * 예약일자가 3개월을 초과한 데이터 영구 백업으로 전환
     * 오전 3시
     */
    function cleanup(){

        $query = "select * from w_ship where reservationDate<'".date("Y-m-d H:i:s",strtotime("-3 months"))."'";
        $list = $this->db->query($query);

        if(count($list)>0){

            foreach($list as $v){
                $query = "insert into w_ship_permanent(
                    id,
                    status,
                    kpiStatus,
                    sfNumber,
                    resNumber,
                    deliveryNumber,
                    orderNumber,
                    sender,
                    mobile,
                    iuopSender,
                    item,
                    itemPrice,
                    itemPrice2,
                    reservationDate,
                    registerDate,
                    collectionDate,
                    deliveryDate
                    ) select id,
                    status,
                    kpiStatus,
                    sfNumber,
                    resNumber,
                    deliveryNumber,
                    orderNumber,
                    sender,
                    mobile,
                    iuopSender,
                    item,
                    itemPrice,
                    itemPrice2,
                    reservationDate,
                    registerDate,
                    collectionDate,
                    deliveryDate from w_ship where id='".$v['id']."'";

                $this->db->query($query);

                $query = "delete from w_ship where id='".$v['id']."'";
                $this->db->query($query);
            }

        }


    }

    /**
     * batch
     * 오전 12시, 오후 12시에 지연 체크
     */
    function delayupdate(){

        //공공데이터 포털 키 
        $key1 = "";


        //한달전, 지금, 한달후 날짜 구하기
        $rangeMonth[0] = array("solYear"=>date("Y",strtotime('-1month')),"solMonth"=>date("m",strtotime('-1month')),"solDay"=>date("d",strtotime('-1month')));
        $rangeMonth[1] = array("solYear"=>date("Y"),"solMonth"=>date("m"),"solDay"=>date("d"));
        $rangeMonth[2] = array("solYear"=>date("Y",strtotime('+1month')),"solMonth"=>date("m",strtotime('+1month')),"solDay"=>date("d",strtotime('+1month')));
        

        //주말 휴일 구하기
        $start_date = strtotime($rangeMonth[0]['solYear'].'-'.$rangeMonth[0]['solMonth'].'-'.$rangeMonth[0]['solDay']);
        $end_date = strtotime($rangeMonth[2]['solYear'].'-'.$rangeMonth[2]['solMonth'].'-'.$rangeMonth[2]['solDay']);
        
        while ($start_date <= $end_date) {
            $day_of_week = date('N', $start_date);
            
            if ($day_of_week == 6 || $day_of_week == 7 ) {
                $holidays[] = date('Ymd',$start_date);
            }
            
            $start_date = strtotime('+1 day', $start_date);
        }


        
        //공휴일 구하기
        foreach($rangeMonth as $k=>$v){
            $url = "http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getRestDeInfo?solYear=".$v['solYear']."&solMonth=".$v['solMonth']."&ServiceKey=".$key1;
            
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            curl_close($ch); 


            $object = simplexml_load_string($response);
            $holiday = json_decode(json_encode($object),1);
            
            if(!$holiday['body']['items']['item'][0]){
                if($holiday['body']['items']['item']['locdate']) $holidays[] = $holiday['body']['items']['item']['locdate'];
            }else{
                foreach($holiday['body']['items']['item'] as $v2){
                    if($v2['locdate']) $holidays[] = $v2['locdate'];
                }
            }

        }
        

        //시간순으로 정렬 수정
        sort($holidays);



        ##접수 후 집하가 24시간 동안 집하 안된 데이터 가져오기
        $query = "select * from w_ship where registerDate<'".date("Y-m-d H:i:s",strtotime("-24 hours"))."' and collectionDate is null and kpiStatus!='집하지연'";
        $tmpList = $this->db->query($query);
        
        if(count($tmpList)>0){
            
            $tmpEndDate = time();

            foreach($tmpList as $k=>$v){
                unset($tmpRange,$cnt);

                $tmpStartDate = strtotime($v['registerDate']);
                
                //주말, 공휴일을 제외하고
                while($tmpStartDate<=$tmpEndDate){
                    
                    $tmpStartDateConvert = date("Ymd",$tmpStartDate);
                    
                    if(in_array($tmpStartDateConvert,$holidays)){
                   
                    }else{
                        $tmpRange[] = $tmpStartDateConvert;
                    }

                    $tmpStartDate = strtotime('+1 day', $tmpStartDate);
                }

                $cnt = count($tmpRange);
                
                //1일을 초과하면
                if($cnt>1){
                    $query = "update w_ship set kpiStatus='집하지연' where id='".$v['id']."'";
                    $this->db->query($query);
                }
                
            }
        }
        

        ##지합 후 집하가ㅏ 48시간 동안 배송이 안된 데이터 가져오기
        $query = "select * from w_ship where collectionDate<'".date("Y-m-d H:i:s",strtotime("-48 hours"))."' and deliveryDate is null and kpiStatus!='배송지연'";
        $tmpList = $this->db->query($query);

        if(count($tmpList)>0){
            
            $tmpEndDate = time();

            foreach($tmpList as $k=>$v){
                unset($tmpRange,$cnt);

                $tmpStartDate = strtotime($v['collectionDate']);
                
                //주말, 공휴일을 제외하고
                while($tmpStartDate<=$tmpEndDate){
                    
                    $tmpStartDateConvert = date("Ymd",$tmpStartDate);

                    if(in_array($tmpStartDateConvert,$holidays)){
                   
                    }else{
                        $tmpRange[] = $tmpStartDateConvert;
                    }

                    $tmpStartDate = strtotime('+1 day', $tmpStartDate);
                }

                $cnt = count($tmpRange);

                //2일을 초과하면
                if($cnt>2){
                    $query = "update w_ship set kpiStatus='배송지연' where id='".$v['id']."'";
                    $this->db->query($query);
                }
                
            }

        }

    }
}