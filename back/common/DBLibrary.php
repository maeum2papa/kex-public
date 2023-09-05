<?php
namespace common;

class DBLibrary extends \mysqli
{
    function __construct()
    {
        // $dbinfo['host'] = 'localhost';
        // $dbinfo['dbid'] = 'lab3';
        // $dbinfo['dbpw'] = 'Vkfksquf10#';
        // $dbinfo['dbnm'] = 'lab3';
		include $_SERVER['DOCUMENT_ROOT']."/common/db.php";

        parent::__construct($dbinfo['host'],$dbinfo['dbid'],base64_decode($dbinfo['dbpw']),$dbinfo['dbnm']);

        if ($this->connect_error) {
            echo $this->connect_error;
            exit;
        }

        $this->tables['member'] = '';
        $this->tables['estimate'] = '';
    }


    /**
     *  :: 데이터베이스 연결 수정
     */
    function setData($dbinfo)
    {
        parent::__construct($dbinfo['host'],$dbinfo['dbid'],$dbinfo['dbpw'],$dbinfo['dbnm']);
    }


    /**
     *  :: 최근 insert_id 를 가져오기
     */
    function last()
    {
        return @$this->insert_id;
    }


    /**
     *  :: 쿼리실행
     */
     function query($query,$type=null,$val=null){
        
         if($query && !$type && !$val){

             $stmt = @parent::prepare($query);
             if(!$stmt) return false;
             if(!$stmt->execute()) return false;

             if(preg_match("/^select/i",$query)){
                 return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
             }else{
                 return true;
             }

         }else{
            
             if(!is_array($val)) $val = array($val);
             
             $stmt = @parent::prepare($query);
             if(!$stmt) return false;
             $params = array_merge(array($type),$val);
             $tmp = array();
             
             foreach($params as $k => $v) $tmp[$k] = &$params[$k];

             call_user_func_array(array($stmt,'bind_param'),$tmp);

             if(!$stmt->execute()) return false;


             if(preg_match("/^select/i",$query)){
                 return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
             }else{
                 return true;
             }

         }


     }


    /**
     *  :: 쿼리 결과물 가져오기 또는 쿼리 실행
     */
    function fetch($res){
 		if(is_string($res)) $res = $this->query($res);
 		if($res) return @$res->fetch_array();
 	}


    /**
     *  :: 데이터 수 가져오기
     */
    function rows($query,$type,$val){
         if(preg_match("/^select/",$query)){
             $res = $this->query($query,$type,$val);
             $result = count($res);
         }

 		return $result;
 	}


    /**
     *  :: 데이터 검증(보안)
     */
    function escape($data){
 		$data = $this->stripslashes_deep($data);
 		$data = $this->mysql_real_escape_string_deep($data);

 		return $data;
 	}


    /**
     *  :: 데이터 검증 (보안)
     */
    function stripslashes_deep($var){
 		$var = is_array($var)?array_map(array($this,'stripslashes_deep'), $var) :stripslashes($var);
 		return $var;
 	}


    /**
     *  :: 데이터 검증 (보안)
     */
    function mysql_real_escape_string_deep($var){
 		$var = is_array($var)?array_map(array($this,'mysql_real_escape_string_deep'), $var):$this->real_escape_string($var);
 		return $var;
 	}


}
 ?>
