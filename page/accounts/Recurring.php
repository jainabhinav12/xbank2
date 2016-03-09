<?php

class page_accounts_Recurring extends Page {
	function init(){
		parent::init();
		$this->add('Controller_Acl');
		$crud=$this->add('xCRUD',array('grid_class'=>'Grid_Account','add_form_beautifier'=>false));
		$account_recurring_model = $this->add('Model_Account_Recurring');
		$account_recurring_model->add('Controller_Acl');
		$account_recurring_model->setOrder('id','desc');
		$self=$this;				
		$crud->addHook('myupdate',function($crud,$form)use($self){
			if($crud->isEditing('edit')) return false;

			$sm_model=$self->add('Model_Account_SM');
				$sm_model->addCondition('member_id',$form['member_id']);
				$sm_model->tryLoadAny();
				if(!$sm_model->loaded()){
					$form->displayError('member',"Member Does not have SM Account");
				}

			if(!$form['sig_image_id'])
				$form->displayError('sig_image_id','Signature File is must');
			
			if($form['NomineeAge'] And  $form['NomineeAge']<18 And $form['MinorNomineeParentName']==""){
				$form->displayError('MinorNomineeParentName','mandatory field');
			}

			$new_account = $crud->add('Model_Account_Recurring');
			try {
				$crud->api->db->beginTransaction();
				// if(!$form['collector_id'] && $form['agent_id']) $form['collector_id'] = $form['agent_id'];
			    $new_account->createNewAccount($form['member_id'],$form['scheme_id'],$crud->api->current_branch, $form['AccountNumber'],$form->getAllFields(),$form);
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
			$crud->form->addField('line','initial_opening_amount');

			// $c_a_f=$crud->form->addField('autocomplete/Basic','collector_saving_account');
			// $c_a_f->setModel('Account_SavingAndCurrent');
			$account_recurring_model->getElement('member_id')->getModel()->addCondition('is_active',true);
		}

		if($crud->isEditing('edit')){
			$account_recurring_model->hook('editing');
		}

		$crud->setModel($account_recurring_model,array('AccountNumber','member_id','scheme_id','Amount','agent_id','collector_id','ActiveStatus','ModeOfOperation','Nominee','NomineeAge','MinorNomineeParentName','RelationWithNominee','mo_id','team_id','sig_image_id'),array('AccountNumber','created_at','member','scheme','Amount','agent','collector','ActiveStatus','collector','ModeOfOperation','Nominee','NomineeAge','RelationWithNominee','mo','team'));
		$crud->add('Controller_DocumentsManager',array('doc_type'=>'RDandDDSAccount'));
		
		if(!$crud->isEditing()){
			$crud->grid->addQuickSearch(array('AccountNumber','member','scheme','agent'));
			
			$nominee_age_field = $crud->form->getElement('NomineeAge');			
			$nominee_age_field->js(true)->univ()->bindConditionalShow(array(
						''=>array(),
						'1'=>array('MinorNomineeParentName'),
						'2'=>array('MinorNomineeParentName'),
						'3'=>array('MinorNomineeParentName'),
						'4'=>array('MinorNomineeParentName'),
						'5'=>array('MinorNomineeParentName'),
						'6'=>array('MinorNomineeParentName'),
						'7'=>array('MinorNomineeParentName'),
						'8'=>array('MinorNomineeParentName'),
						'9'=>array('MinorNomineeParentName'),
						'10'=>array('MinorNomineeParentName'),
						'11'=>array('MinorNomineeParentName'),
						'12'=>array('MinorNomineeParentName'),
						'13'=>array('MinorNomineeParentName'),
						'14'=>array('MinorNomineeParentName'),
						'15'=>array('MinorNomineeParentName'),
						'16'=>array('MinorNomineeParentName'),
						'17'=>array('MinorNomineeParentName'),
						),'div .atk-form-row');

		}

		if($crud->isEditing('add')){
			$crud->form->getElement('scheme_id')->getModel()->addCondition('ActiveStatus',true);
			
			$crud->form->add('Order')
						// ->move($c_a_f->other_field,'after','Amount')
						->move('initial_opening_amount','before','Amount')
						->now();
			$o->now();
		}
		$crud->add('Controller_Acl');
	}
}