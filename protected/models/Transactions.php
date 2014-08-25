<?php

/**
 * This is the model class for table "transactions".
 *
 * The followings are the available columns in table 'transactions':
 * @property string $this->type_id
 * @property integer $user_id
 * @property string $type
 * @property string $type_id
 * @property integer $result
 * @property string $transaction_start_date
 * @property string $transaction_end_date
 * @property string $ip
 */
class Transactions extends CActiveRecord
{
	public $validate_this_payment;
	public $this_payment_validated;
	private $catalogInfoCache;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'transactions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, ip', 'required'),
			//array('user_id, result', 'numerical', 'integerOnly'=>true),
			array('id, type_id', 'length', 'max'=>44),
			array('type', 'length', 'max'=>5),
			array('ip', 'length', 'max'=>20),
			array('transaction_start_date, transaction_end_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, type, type_id, result, transaction_start_date, transaction_end_date, ip', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'type' => 'Type',
			'type_id' => 'Type',
			'result' => 'Result',
			'transaction_start_date' => 'Transaction Start Date',
			'transaction_end_date' => 'Transaction End Date',
			'ip' => 'Ip',
			`transaction_type` => "Transaction",
			`transaction_value` => "Transaction",
			`transaction_CCNO` => "Transaction",
			`transaction_CCYEAR` => "Transaction",
			`transaction_CCMONTH` => "Transaction",
			`transaction_CCV` => "Transaction",
			`transaction_PROMOCODE` => "Transaction",
			`transaction_PROMOCODEID` => "Transaction"
		);
	}

	public function catalogInfo(&$responseHandler=null){
		if ($this->catalogInfoCache) return $this->catalogInfoCache;
		$catalogUrl=Yii::app()->params['catalog_host']."/api/getCatalog?bookId=".$this->type_id;
		error_log("CATALOG URL:".$catalogUrl);
		$cht = curl_init($catalogUrl);
		curl_setopt($cht, CURLOPT_HEADER, 0);
		curl_setopt($cht, CURLOPT_RETURNTRANSFER, 1);
		$catalog = curl_exec($cht);
		error_log("CATALOG RESPONSE:".$catalog);
		
		


		//var_dump($catalogUrl);

		if (!$catalog) {
			if($responseHandler) $responseHandler->error("ModelTransaction","No Catalog Response",func_get_args(),$catalog);
			return false;
		}

		$catalog=json_decode($catalog);

		if (!$catalog->result) {
			if($responseHandler) $responseHandler->error("ModelTransaction","No Catalog Info",func_get_args(),$catalog);
			return false;
		}
		$this->catalogInfoCache=$catalog;
		return $this->catalogInfoCache;

	}


	public function createModelFromPayment($paymentObject,&$responseHandler){
		if(! in_array($paymentObject->type, array('InAppIOS','InAppAndroid','Web','PromoCode')) ){
			if($responseHandler) $responseHandler->error("ModelTransaction","UnknowPaymentType",$paymentObject->type);
			return false;
		}
		$this->transaction_type = $paymentObject->type;

		$this->validate_this_payment=true;
		$this->this_payment_validated=false;

		$catalog=$this->catalogInfo();


		switch ($paymentObject->type) {
			case 'InAppIOS':
				$this->validate_this_payment=false;
				
				break;
			case 'InAppAndroid':
				$this->validate_this_payment=false;
				
				break;
			case 'Web':
				
				switch ($catalog->result->contentIsForSale) {
					case 'Yes':
						


						// Some Payment GW Processor Here Would be better than just boolean;
				        $bankProcess=false;
				        
				        if (!$bankProcess) {
				        	if($responseHandler) $responseHandler->error("ModelTransaction","bankProcessNotSuccessfull",$paymentObject->type);
				        	$this->this_payment_validated=false;
							return false;
				        }

				        $this->this_payment_validated=true;
				        return true;

						break;
					case 'Free':

						$this->this_payment_validated=true;
				        return true;

						break;


					case 'Promo':
						if($responseHandler) $responseHandler->error("ModelTransaction","This Item Is Available Onyl With a Valid Promotion Code, PromoCode Type is expected!",$paymentObject);
						return false;
						
						break;
				}


				
					
					
				




				break;
			case 'PromoCode':
			//var_dump($catalog);die;
				$CampaignCode = PromotionCampaignCodes::model()
					->with('campaign')
					->findByPk($paymentObject->PROMOCODE,
						"
						(
							( campaignType=:campaignTypeOrg AND 
								campaignOrganisationId=:campaignOrganisationId ) 
						 	OR 
							( campaignType=:campaignTypeBook AND 
								campaignBookId=:campaignBookId )

						) AND 
						(campaignEnabled=1) AND 
						(
							(campaignOneTimeTickets=1 AND promotionUsed=0 ) OR 
							(campaignOneTimeTickets=0)
						)


						", 
						array(
							":campaignTypeOrg"=>"Organisation" ,
							":campaignOrganisationId"=>$catalog->result->organisationId ,
							":campaignTypeBook"=>"Book" ,
							":campaignBookId"=>$this->type_id ,
							)

						 );

				
					
				if (!$CampaignCode) {
					if($responseHandler) $responseHandler->error("ModelTransaction","CampaignCode Not Found for PaymentType",$paymentObject->PROMOCODE);
					return false;

				}
				$this->transaction_PROMOCODE=$CampaignCode->promotionCode;
				$this->transaction_PROMOCODE=$CampaignCode->campaignId;
				
				$CampaignCode->save();




				$this->this_payment_validated=true;
				return true;


				break;
			
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('type_id',$this->type_id,true);
		$criteria->compare('result',$this->result);
		$criteria->compare('transaction_start_date',$this->transaction_start_date,true);
		$criteria->compare('transaction_end_date',$this->transaction_end_date,true);
		$criteria->compare('ip',$this->ip,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Transactions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
