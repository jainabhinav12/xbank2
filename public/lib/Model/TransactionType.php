<?php
class Model_TransactionType extends Model_Table {
	var $table= "transaction_types";
	function init(){
		parent::init();

		$this->addField('Transaction');
		$this->addField('FromAC');
		$this->addField('ToAC');
		$this->addField('Default_Narration');
		//$this->add('dynamic_model/Controller_AutoCreator');
	}
}