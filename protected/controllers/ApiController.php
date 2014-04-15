<?php

class ApiController extends Controller
{
	private $method='aes128';
   	private $pass='qaxcftyjmolsp';
    private $iv='8759226422345672';
    	
	public $response=null; 
	public $errors=null; 

	public function response($response_avoition=null){

		$response['result']=$response_avoition ? $response_avoition : $this->response;
		if ($this->errors) $response['errors']=$this->errors;

		$response_string=json_encode($response);


		header('Content-type: plain/text');
		header("Content-length: " . strlen($response_string) ); // tells file size
		echo $response_string;
	}
 
	public function error($domain='Api',$explanation='Error', $arguments=null,$debug_vars=null ){
		$error=new error($domain,$explanation, $arguments,$debug_vars);
		$this->errors[]=$error; 
		return $error;
	}

	public function actionService(){
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$kerberized=new KerberizedServer($auth,$http_service_ticket);
		$myarray=$kerberized->ticketValidation();

		error_log("ticket validation:".serialize($myarray));	
		$kerberized->authenticate();			
	}

	private function authenticate()
	{
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$type=Yii::app()->request->getPost('type','android');
		// error_log("auth:".$auth);
		// error_log("http_service_ticket:".$http_service_ticket);
		$kerberized=new KerberizedServer($auth,$http_service_ticket,KerbelaEncryptionFactory::create($type));
		

		 $myarray=$kerberized->ticketValidation();
		// error_log("user_id:".$kerberized->getUserId());
		//$kerberized->authenticate();
		if ($kerberized->getUserId()) {
			return $kerberized->getUserId();
		}
		else
			return 0;
	} 

	public function actionAuthenticate()
	{
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$type=Yii::app()->request->getPost('type','android');
		// error_log("auth:".$auth);
		// error_log("http_service_ticket:".$http_service_ticket);
		$kerberized=new KerberizedServer($auth,$http_service_ticket,KerbelaEncryptionFactory::create($type));
		

		 $myarray=$kerberized->ticketValidation();
		// error_log("user_id:".$kerberized->getUserId());
		$kerberized->authenticate();
	}

	public function actionDocumentation()
	{
		$this->render('documentation');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function getCatalog($id)
	{
		$catalogUrl=Yii::app()->params['catalog_host']."/api/getCatalog?bookId=".$id;
		error_log("CATALOG URL:".$catalogUrl);
		$cht = curl_init($catalogUrl);
		curl_setopt($cht, CURLOPT_HEADER, 0);
		curl_setopt($cht, CURLOPT_RETURNTRANSFER, 1);
		$catalog = curl_exec($cht);
		error_log("CATALOG RESPONSE:".$catalog);
		if ($catalog) {
			return $catalog;
		}
		return false;
	}

	public function callAddUserBook($user_id,$type_id)
	{
		$url = Yii::app()->params['koala_host']."/api/addUserBook";
	        	
		$user_p=openssl_encrypt($user_id, $this->method, $this->pass,true,$this->iv);
		$type_p=openssl_encrypt($type_id, $this->method, $this->pass,true,$this->iv);
		$params = array(
						'user'=>$user_p,
						'type'=>$type_p
						);
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec( $ch );
		error_log("Response from catalog while adding book to use:".$response);
		error_log("USER ID and TYPE:".$user_id."-".$type_id);
		return $response;
	}

	public function addTransaction($user_id,$type,$type_id)
	{
		$transction= new Transactions;
		$transction->id=uniqid("", true);
		$transction->user_id=$user_id;
		$transction->type=$type;
		$transction->type_id=$type_id;
        $transction->ip=CHttpRequest::getUserHostAddress();
        $transction->transaction_start_date=date('Y-n-d g:i:s',time());
        $transction->result=1;
        
        if ($transction->save()) {
        	return $transction->id;
        }
    	$this->error("AC-ATransaction","Operation failed",func_get_args());
    	return 0;
        
	}

	public function actionTransaction()
	{
		$email=$this->authenticate();
		if (!$email) {
			return null;
		}
		$response=array();

		if (!CHttpRequest::getIsPostRequest()) {
        	$this->error("AC-ATransaction","Wrong Request",func_get_args(),CHttpRequest::getIsPostRequest());
        	$this->response($response);
			return null;
		}
		


		// $type=openssl_decrypt(CHttpRequest::getPost('type_name',0), $this->method, $this->pass,true,$this->iv);
		// $type_id=openssl_decrypt(CHttpRequest::getPost('type_id',0), $this->method, $this->pass,true,$this->iv);

		$type=CHttpRequest::getPost('type_name',0);
		$type_id=CHttpRequest::getPost('type_id',0);
		error_log("TYPE is ".$type);
		if ($type=='book') {
			$responseCatalog=$this->getCatalog($type_id);

		}
		error_log("RESPONSE from catalog:".$responseCatalog);

		$responseTransaction=$this->addTransaction($email,$type,$type_id);
		if (!$responseTransaction) {
			$this->response($response);
			return null;
		}
		
		$catalog=json_decode($responseCatalog,true);
		if ($catalog["result"]['contentIsForSale']=='Yes') {
	        //banka işlemleri
	        $bankProcess=$this->bankProcess();
	        
	        if (!$bankProcess) {
	        	$this->response($response);
				return null;
	        }
		}
        
        
       $updateTransaction=$this->endTransaction($responseTransaction);

       	$book='';
    	if ($type=='book') {
        	$book=$this->callAddUserBook($email,$type_id);
    	}

    	if ($book & $responseTransaction) {
    		$res=true;
    	}else
    	{
    		$res=false;
    	}
        
    	$this->response($res);
	}

	public function bankProcess()
	{
		return true;
	}

	public function endTransaction($id)
	{
		$model=Transactions::model()->findByPk($id);
		$model->result=0;
		$model->transaction_end_date=date('Y-n-d g:i:s',time());
		if (!$model->save()) {
			$this->error("AC-UTransaction","Operation failed",func_get_args());
			return false;
		}
		return $model;
	}

	public function actionDeneme()
	{
		$url = Yii::app()->params['panda_host']."/api/transaction";

		$a="1";
		$b="book";
		$c="uI7ukJDeKDtVNwJAGoKEyE76SszKVjGS1rn2w2HH1rsO";

		$a_p=openssl_encrypt($a, $this->method, $this->pass,true,$this->iv);
		$b_p=openssl_encrypt($b, $this->method, $this->pass,true,$this->iv);
		$c_p=openssl_encrypt($c, $this->method, $this->pass,true,$this->iv);

		$params = array(
						'user_id'=>$a_p,
						'type'=>$b_p,
						'type_id'=>$c_p
						);
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec( $ch );
		$this->response($response);
	}
	//  the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
