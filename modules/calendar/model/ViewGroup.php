<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */
 
/**
 * The GO_Calendar_Model_CalendarView model
 *
 * @package GO.modules.Calendar
 * @version $Id: CalendarView.php 7607 2011-11-23 15:14:37Z mdhart $
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart mdhart@intermesh.nl
 *
 * @property int $group_id
 * @property int $view_id
 */

class GO_Calendar_Model_ViewGroup extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_CalendarTasklist
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}


	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	// public function aclField(){
	//	 return 'acl_id';	
	// }

	public function primaryKey() {
		
		return array('group_id','view_id');
	}
	
	/**
	 * Returns the table name
	 */
	 public function tableName() {
		 return 'cal_views_groups';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array(
             'view' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_View', 'field' => 'view_id'),
             'group' => array('type' => self::BELONGS_TO, 'model' => 'GO_Base_Model_Group', 'field' => 'group_id'),
         );
	 }
}