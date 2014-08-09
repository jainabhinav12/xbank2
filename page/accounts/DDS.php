<?php

class page_accounts_DDS extends Page {
	function init(){
		parent::init();

		$crud=$this->add('xCRUD');
		$account_dds_model = $this->add('Model_Account_DDS');
		$account_dds_model->setOrder('created_at','Desc');
		$account_dds_model->add('Controller_Acl');

		$crud->addHook('myupdate',function($crud,$form){
			if($crud->isEditing('edit')) return false;
			
			$dds_account_model = $crud->add('Model_Account_DDS');			
			try {
				$crud->api->db->beginTransaction();
			    $dds_account_model->createNewAccount($form['member_id'],$form['scheme_id'],$crud->api->current_branch, $form['AccountNumber'],$form->getAllFields(),$form);
			    $crud->api->db->commit();
			} catch (Exception $e) {
			   	$crud->api->db->rollBack();
			   	throw $e;
			}
			return true;
		});

		if($crud->isEditing("add")){
		    $o=$crud->form->add('Order');
			$k = 2;
			for($k=2;$k<=4;$k++) {
			    $f=$crud->form->addField('autocomplete/Basic','member_ID'.$k);
			   	$f->setModel('Member');
			   	$o->move($f->other_field,'before','Nominee');
			}

		}

		if($crud->isEditing('edit')){
			$account_dds_model->hook('editing');
		}

		$crud->setModel($account_dds_model,array('AccountNumber','member_id','scheme_id','Amount','agent_id','ActiveStatus','ModeOfOperation','Nominee','NomineeAge','RelationWithNominee','mo_id','team_id'),array('AccountNumber','member','scheme','Amount','agent','ActiveStatus','ModeOfOperation','Nominee','NomineeAge','RelationWithNominee','mo','team'));
		
		if($crud->grid)
			$crud->grid->addPaginator(10);

		if($crud->isEditing('add')){
			$crud->form->addField('line','initial_opening_amount');
			
			$o->move('initial_opening_amount','before','Amount')
				// ->move('collector_saving_account','after','collector_id')
				->now();
		}

	}
}