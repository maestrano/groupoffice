<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.customfields.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Customfields_Model_FieldSelectOption model
 *
 * @package GO.modules.customfields.model
 * @property int $sort_order
 * @property string $text
 * @property int $field_id
 * @property int $id
 */

class GO_Customfields_Model_FieldSelectOption extends GO_Base_Db_ActiveRecord{
		
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Customfields_Model_FieldSelectOption 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'cf_select_options';
	}	
	
	public function relations() {
		return array(
				'fields' => array('type' => self::BELONGS_TO, 'model' => 'GO_Customfields_Model_Field', 'field' => 'field_id')		);
	}	

}