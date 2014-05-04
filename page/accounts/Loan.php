<?php

class page_accounts_Loan extends Page {
	function page_index(){
		// parent::init();

		$crud=$this->add('xCRUD');
		
		$crud->addHook('myupdate',function($crud,$form){
			$loan_account_model = $crud->add('Model_Account_Loan');
			$loan_account_model->createNewAccount($form['member_id'],$form['scheme_id'],$crud->api->current_branch->id, $form['AccountNumber'],$form->getAllFields(),$form);
			$crud->js()->univ()->errorMessage('Done')->execute();
		});


		/**
		 * Add Documents Fields ...
		 */
		if($crud->isEditing("add")){
		    $o=$crud->form->add('Order');
			$documents=$this->add('Model_Document');
			foreach ($documents as $d) {
			    $f1=$crud->form->addField('checkbox',$this->api->normalizeName($documents['name']));
			   	$o->move($f1,'last');
			    $f2=$crud->form->addField('line',$this->api->normalizeName($documents['name'].' value'));
			   	$o->move($f2,'last');
			   	$f1->js(true)->univ()->alert('hi')->bindConditionalShow(array(
					''=>array(''),
					'*'=>array($this->api->normalizeName($documents['name'].' value'))
					),'div .atk-form-row');
			}
		}

		$crud->setModel('Account_Loan',array('AccountNumber','member_id','scheme_id','loanAmount','agent_id','ActiveStatus','gaurantor','gaurantorAddress','gaurantorPhNo','ModeOfOperation','loan_from_account_id','LoanInsurranceDate','LoanAgainstAccount_id','dealer_id'));
		if($crud->grid){
			$crud->grid->addPaginator(10);
			$crud->grid->addColumn('expander','edit_document');
		}

		if($form=$crud->form){
			$crud->form->addField('checkbox','LoanAgSecurity');

			$crud->form->add('Order')
						->move('LoanAgSecurity','after','LoanInsurranceDate')
						->now();


		}


		if($crud->isEditing('add')){
			$o->now();
		}		

	}

	function page_edit_document(){
		$this->api->stickyGET('accounts_id');

		$documents=$this->add('Model_DocumentSubmitted');
		$documents->addCondition('accounts_id',$_GET['accounts_id']);

		$crud=$this->add('CRUD',array('allow_add'=>true));
		$crud->setModel($documents);
	}
}