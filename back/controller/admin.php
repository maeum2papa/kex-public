<?
namespace controller;
use common;
use common\Encrypt;
use firebase\jwt\JWT;
use firebase\jwt\Key;
class Admin{

    function __construct(){

        global $db,$lib;
        
        $this->db = $db;
        $this->lib = $lib;
        $this->jwt->key = '';
        $this->post = json_decode(file_get_contents( 'php://input' ),JSON_UNESCAPED_UNICODE);
        
        $this->post = Encrypt::de($this->post['body']);

    }

    #토큰 체크
    function chk(){
        
        $res = array('msg'=>'no');


        $accessToken = $_SERVER["HTTP_AUTHORIZATION"];
        //$res = array('msg'=>'no','access_token'=>$accessToken);
        $decoded = JWT::decode($accessToken, new Key($this->jwt->key,'HS256'));
        $decoded = (array)$decoded;
        
        if(!$decoded['email']){
            
            if($res['msg']=='no'){
                header("HTTP/1.0 400 Bad Request");
            }

            echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /***
     * /admin/login
     */
    function login(){

        $res = array('msg'=>'no');

        ### START
        
       
        if(!$this->post['email'] || !$this->post['password']){
            
            header("HTTP/1.0 400 Bad Request");
            echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
            exit;

        }else{
            //koheunji@kex-sf.com / p@ss#word1
            if($this->post['email']=='koheunji@kex-sf.com' && $this->post['password']=='p@ss#word1'){

                $accessTokenPayload = array(
                    'email'=>$this->post['email'],
                    'iat'=>time(),
                    'exp'=>time()+(60*15)
                );

                $refreshTokenPayload = array(
                    'email'=>$this->post['email'],
                    'iat'=>time(),
                    'exp'=>time()+(60*60*24*180)
                );
                

                $res['msg'] = 'ok';
                $res['access_token'] = JWT::encode($accessTokenPayload, $this->jwt->key,'HS256');
                $res['refresh_token'] = JWT::encode($refreshTokenPayload, $this->jwt->key,'HS256');
            }

            
            
            echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);

        }

        ### END

    }

    /***
     * /api/auth
     */
    function auth(){

        $res = array('msg'=>'no');

        ### START

        $accessToken = $_SERVER["HTTP_AUTHORIZATION"];

        if($accessToken){
            try{
                $decoded = JWT::decode($accessToken, new Key($this->jwt->key,'HS256'));
                $decoded = (array)$decoded;
                $res['msg'] = 'ok';
                $res['email'] = $decoded['email'];
            }catch(Exception $e){

            }
        }

        ### END

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }

    /***
     * /admin/retoken
     */
    function retoken(){
        
        $res = array('msg'=>'no');

        ### START

        $refreshToken = $this->post['refresh_token'];

        if($refreshToken){
            try{
                $decoded = JWT::decode($refreshToken, new Key($this->jwt->key,'HS256'));
                $decoded = (array)$decoded;
                if($decoded['email']){
                    
                    $accessTokenPayload = array(
                        'email'=>$decoded['email'],
                        'iat'=>time(),
                        'exp'=>time()+(60*15)
                    );

                    $res['msg'] = 'ok';
                    $res['email'] = $decoded['email'];
                    $res['access_token'] = JWT::encode($accessTokenPayload, $this->jwt->key,'HS256');
                }
            }catch(Exception $e){

            }
        }

        ### END
        
        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }

        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);

    }


    /***
     * /admin/list
     * 
     * request param
     * startResDate:예약일자 검색 시작일,
        endResDate:예약일자 검색 종료일,
        startRegDate:접수일자 검색 시작일,
        endRegDate:접수일자 검색 종료일,
        startColDate:집하일자 검색 시작일,
        endColDate:집하일자 검색 종료일,
        startDelDate:배송일자 검색 시작일,
        endDelDate:배송일자 검색 종료일,
        sender:발송자명,
        status:화물상태,
        kpiStatus:KPI상태,
        convSfNumber:SF운송장번호 검색 (Array),
        convResNumber:승인번호 검색 (Array),
        convDeliveryNumber:택백사 운송장 번호 검색 (Array),
     */
    function list(){
        $res = array('msg'=>'no');
        
        //토큰 검증
        $this->chk(); 
        
        ### START

        $this->post['convSfNumber'] = array_filter($this->post['convSfNumber']);
        $this->post['convReNumber'] = array_filter($this->post['convReNumber']);
        $this->post['convDeliveryNumber'] = array_filter($this->post['convDeliveryNumber']);

        // $where[] = "1";

        if($this->post['convSfNumber'][0]){
            $where[] = "sfNumber in('".implode("','",$this->post['convSfNumber'])."')";
        }

        if($this->post['convResNumber'][0]){
            $where[] = "resNumber in('".implode("','",$this->post['convResNumber'])."')";
        }

        if($this->post['convDeliveryNumber'][0]){
            $where[] = "deliveryNumber in('".implode("','",$this->post['convDeliveryNumber'])."')";
        }

        if($this->post['sender']!=''){
            $where[] = "sender='".$this->post['sender']."'";
        }
        
        if($this->post['status']!=''){
            $where[] = "status='".$this->post['status']."'";
        }

        if($this->post['kpiStatus']!=''){
            $where[] = "kpiStatus='".$this->post['kpiStatus']."'";
        }

        if($this->post['startResDate']!='' && $this->post['endResDate']!=''){
            
            $where[] = "reservationDate>='".$this->post['startResDate']." 00:00:00'";
            $where[] = "reservationDate<='".$this->post['endResDate']." 23:59:59'";

        }else if($this->post['startResDate']!='' && !$this->post['endResDate']){
            $where[] = "reservationDate>='".$this->post['startResDate']." 00:00:00'";
        }else if(!$this->post['startResDate'] && $this->post['endResDate']!=''){
            $where[] = "reservationDate<='".$this->post['endResDate']." 23:59:59'";
        }

        if($this->post['startRegDate']!='' && $this->post['endRegDate']!=''){
            
            $where[] = "registerDate>='".$this->post['startRegDate']." 00:00:00'";
            $where[] = "registerDate<='".$this->post['endRegDate']." 23:59:59'";

        }else if($this->post['startRegDate']!='' && !$this->post['endRegDate']){
            $where[] = "registerDate>='".$this->post['startRegDate']." 00:00:00'";
        }else if(!$this->post['startRegDate'] && $this->post['endRegDate']!=''){
            $where[] = "registerDate<='".$this->post['endRegDate']." 23:59:59'";
        }

        if($this->post['startColDate']!='' && $this->post['endColDate']!=''){
            
            $where[] = "collectionDate>='".$this->post['startColDate']." 00:00:00'";
            $where[] = "collectionDate<='".$this->post['endColDate']." 23:59:59'";

        }else if($this->post['startColDate']!='' && !$this->post['endColDate']){
            $where[] = "collectionDate>='".$this->post['startColDate']." 00:00:00'";
        }else if(!$this->post['startColDate'] && $this->post['endColDate']!=''){
            $where[] = "collectionDate<='".$this->post['endColDate']." 23:59:59'";
        }

        if($this->post['startDelDate']!='' && $this->post['endDelDate']!=''){
            
            $where[] = "deliveryDate>='".$this->post['startDelDate']." 00:00:00'";
            $where[] = "deliveryDate<='".$this->post['endDelDate']." 23:59:59'";

        }else if($this->post['startDelDate']!='' && !$this->post['endDelDate']){
            $where[] = "deliveryDate>='".$this->post['startDelDate']." 00:00:00'";
        }else if(!$this->post['startDelDate'] && $this->post['endDelDate']!=''){
            $where[] = "deliveryDate<='".$this->post['endDelDate']." 23:59:59'";
        }

        
        $query = "select * from w_ship where ".implode(' and ',$where)." order by id desc, reservationDate desc";

        $list = $this->db->query($query);

        $res = array('msg'=>'ok',"list"=>$list);

        ### END

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }
        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }


    /**
     * /admin/exchnage/get
     * /admin/exchnage/set
     * 
     * request params
     * 
     * price : 환율 소수점 두자리까지
        startDate : 환율 적용 기간 (예약접수시간을 기준으로 환율을 계산할때 사용됨)
        endDate : 환율 적용 기간 (예약접수시간을 기준으로 환율을 계산할때 사용됨)
     * 
     */
    function exchange(){
        
        //토큰 검증
        $this->chk(); 
        
        if($this->lib->segment[4]=='get'){
            $this->getExchange();
            exit;
        }

        if($this->lib->segment[4]=='set'){
            $this->setExchange();
            exit;
        }

        if($this->lib->segment[4]=='del'){
            $this->delExchange();
            exit;
        }

    }

    #고시 환율 정보 가져오기
    function getExchange(){
        $res = array('msg'=>'no');


        ### START

        $query = "select * from w_exchange order by code desc limit 5";
        $list = $this->db->query($query);

        foreach($list as $k=>$v){
            $list[$k]['price'] = $v['data'];
            $list[$k]['startDate'] = substr($v['startDate'],0,10);
            $list[$k]['endDate'] = substr($v['endDate'],0,10);
        }

        //$test = array('price'=>1200.12,'startDate'=>'2032-03-01','endDate'=>'2032-03-31');

        $res = array('msg'=>'ok','data'=>$list);

        ### END


        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }
        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }


    #고시 환율 정보 저장
    function setExchange(){

        $res = array('msg'=>'no');


        ### START
        if($this->post['price'] && $this->post['startDate'] && $this->post['endDate']){

            // 데이터베이스에 저장
            $query = "insert into w_exchange set data=?,startDate=?,endDate=?";
            $this->db->query($query,"sss",array($this->post['price'],$this->post['startDate'],str_replace("00:00:00","23:59:59",$this->post['endDate'])));

            $res = array('msg'=>'ok');
        }
        ### END


        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }
        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }

    #고시 환율 정보 삭제
    function delExchange(){

        $res = array('msg'=>'no');

        if($this->post['code']){

            $query = "delete from w_exchange where code=?";
            $this->db->query($query,"i",array($this->post['code']));

            $res = array('msg'=>'ok');
        }

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }
        echo json_encode(Encrypt::en($res),JSON_UNESCAPED_UNICODE);
    }


    # SF운송장번호로 CJ택배운송장번호 가져오기
    function scan(){

        $res = array('msg'=>'no');

        ### START
        
        if($_GET['d']){

            $query = "select deliveryNumber,orderNumber,sfNumber from w_ship where deliveryNumber=?";
            list($result) = $this->db->query($query,"s",array($_GET['d']));
            
            if($result['deliveryNumber']){
                $res = array('msg'=>'ok','data'=>array('d'=>$result['sfNumber'],'o'=>$result['orderNumber']));
            }
        }

        ### END

        if($res['msg']=='no'){
            header("HTTP/1.0 400 Bad Request");
        }
        echo json_encode($res,JSON_UNESCAPED_UNICODE);
    }

}
