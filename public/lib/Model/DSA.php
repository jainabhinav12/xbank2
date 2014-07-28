<?php


class Model_DSA extends Model_Table {
	public $table ='dsa';

	function init(){
		parent::init();
		
		$this->hasOne('Member','member_id')->display(array('form'=>'autocomplete/Basic'));
		$this->addField('name');


		$this->hasMany('DocumentSubmitted','dsa_id');
		$this->add('filestore/Field_Image','doc_image_id')->type('image')->mandatory(true);
				
		$this->addHook('beforeDelete',$this);
		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeDelete(){
		throw new Exception("Hook ... ????", 1);
		
	}
}