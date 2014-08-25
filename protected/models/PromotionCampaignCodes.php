<?php

/**
 * This is the model class for table "promotion_campaign_codes".
 *
 * The followings are the available columns in table 'promotion_campaign_codes':
 * @property string $campaignId
 * @property string $promotionCode
 * @property integer $promotionUsed
 * @property integer $promotionUseCount
 *
 * The followings are the available model relations:
 * @property PromotionCampaigns $campaign
 */
class PromotionCampaignCodes extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'promotion_campaign_codes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('campaignId, promotionCode, promotionUsed, promotionUseCount', 'required'),
			array('promotionUsed, promotionUseCount', 'numerical', 'integerOnly'=>true),
			array('campaignId', 'length', 'max'=>44),
			array('promotionCode', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('campaignId, promotionCode, promotionUsed, promotionUseCount', 'safe', 'on'=>'search'),
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
			'campaign' => array(self::BELONGS_TO, 'PromotionCampaigns', 'campaignId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'campaignId' => 'Campaign',
			'promotionCode' => 'Promotion Code',
			'promotionUsed' => 'Promotion Used',
			'promotionUseCount' => 'Promotion Use Count',
		);
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

		$criteria->compare('campaignId',$this->campaignId,true);
		$criteria->compare('promotionCode',$this->promotionCode,true);
		$criteria->compare('promotionUsed',$this->promotionUsed);
		$criteria->compare('promotionUseCount',$this->promotionUseCount);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PromotionCampaignCodes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function beforeSave(){
		if(parent::beforeSave()){
	   
	         // for example
	        $this->promotionUsed=true;
	        $this->promotionUseCount+=1;

	        return true;
	   }
	   return false;
		
	}
}
