<?php

/**
 * This is the model class for table "promotion_campaigns".
 *
 * The followings are the available columns in table 'promotion_campaigns':
 * @property string $campaignId
 * @property string $campaignName
 * @property string $campaignDesc
 * @property string $campaignValidFrom
 * @property string $campaignValidThru
 * @property integer $campaignEnabled
 * @property integer $campaignOneTimeTickets
 * @property string $campaignType
 * @property string $campaignBookId
 * @property string $campaignCategoryId
 * @property string $campaignOrganisationId
 * @property string $campaignDiscountType
 * @property integer $campaignDiscountValue
 *
 * The followings are the available model relations:
 * @property PromotionCampaignCodes[] $promotionCampaignCodes
 */
class PromotionCampaigns extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'promotion_campaigns';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('campaignId, campaignName, campaignDesc, campaignValidFrom, campaignValidThru, campaignEnabled, campaignOneTimeTickets, campaignType, campaignBookId, campaignCategoryId, campaignOrganisationId, campaignDiscountType, campaignDiscountValue', 'required'),
			array('campaignEnabled, campaignOneTimeTickets, campaignDiscountValue', 'numerical', 'integerOnly'=>true),
			array('campaignId, campaignBookId, campaignCategoryId, campaignOrganisationId', 'length', 'max'=>44),
			array('campaignName', 'length', 'max'=>250),
			array('campaignType', 'length', 'max'=>12),
			array('campaignDiscountType', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('campaignId, campaignName, campaignDesc, campaignValidFrom, campaignValidThru, campaignEnabled, campaignOneTimeTickets, campaignType, campaignBookId, campaignCategoryId, campaignOrganisationId, campaignDiscountType, campaignDiscountValue', 'safe', 'on'=>'search'),
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
			'promotionCampaignCodes' => array(self::HAS_MANY, 'PromotionCampaignCodes', 'campaignId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'campaignId' => 'Campaign',
			'campaignName' => 'Campaign Name',
			'campaignDesc' => 'Campaign Desc',
			'campaignValidFrom' => 'Campaign Valid From',
			'campaignValidThru' => 'Campaign Valid Thru',
			'campaignEnabled' => 'Campaign Enabled',
			'campaignOneTimeTickets' => 'Campaign One Time Tickets',
			'campaignType' => 'Campaign Type',
			'campaignBookId' => 'Campaign Book',
			'campaignCategoryId' => 'Campaign Category',
			'campaignOrganisationId' => 'Campaign Organisation',
			'campaignDiscountType' => 'Campaign Discount Type',
			'campaignDiscountValue' => 'Campaign Discount Value',
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
		$criteria->compare('campaignName',$this->campaignName,true);
		$criteria->compare('campaignDesc',$this->campaignDesc,true);
		$criteria->compare('campaignValidFrom',$this->campaignValidFrom,true);
		$criteria->compare('campaignValidThru',$this->campaignValidThru,true);
		$criteria->compare('campaignEnabled',$this->campaignEnabled);
		$criteria->compare('campaignOneTimeTickets',$this->campaignOneTimeTickets);
		$criteria->compare('campaignType',$this->campaignType,true);
		$criteria->compare('campaignBookId',$this->campaignBookId,true);
		$criteria->compare('campaignCategoryId',$this->campaignCategoryId,true);
		$criteria->compare('campaignOrganisationId',$this->campaignOrganisationId,true);
		$criteria->compare('campaignDiscountType',$this->campaignDiscountType,true);
		$criteria->compare('campaignDiscountValue',$this->campaignDiscountValue);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PromotionCampaigns the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
