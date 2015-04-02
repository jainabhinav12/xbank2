<?php

class page_schemes_Loan extends Page{
	function page_index(){
		// parent::init();

		$crud=$this->add('xCRUD');
		$scheme_Loan_model =$this->add('Model_Scheme_Loan');
		$scheme_Loan_model->setOrder('id','desc');
		
		$crud->addHook('myupdate',function($crud,$form){
			if($crud->isEditing('edit')) return false;
						
			$Loan_scheme_model = $crud->add('Model_Scheme_Loan');
			try {
				$this->api->db->beginTransaction();
			    $Loan_scheme_model->createNewScheme($form['name'],$form['balance_sheet_id'], ACCOUNT_TYPE_LOAN, ACCOUNT_TYPE_LOAN, $is_loanType=$form['type'], $other_values=$form->getAllFields(),$form,$form->api->now);
			    $this->api->db->commit();
			} catch (Exception $e) {
			   	$this->api->db->rollBack();
			   	throw $e;
			}
			return true;
		});

		if($crud->isEditing("add")){
		    $o=$crud->form->add('Order');
		    // $t=array('Two Wheeler Loan','Auto Loan','Personal Loan','Loan Against Deposit','Home Loan','Mortgage Loan','Agriculture Loan','Education Loan','Gold Loan','Other');
		    // $crud->form->addField('DropDown','loan_type')->setEmptyText('Please select')->setValueList(array_combine($t,$t))->validateNotNull();
		}

		if($crud->isEditing('edit')){
			$scheme_Loan_model->hook('editing');
			// $scheme_Loan_model->getElement('type')->system(true);
		}

		$crud->setModel($scheme_Loan_model,array('ReducingOrFlatRate','type','name','MinLimit','MaxLimit','Interest','PremiumMode','NumberOfPremiums','ActiveStatus','balance_sheet_id','balance_sheet','ProcessingFeesinPercent','ProcessingFees','SchemePoints','SchemeGroup'));

		
		if($crud->grid){
			$crud->grid->addPaginator(50);
			$crud->grid->addQuickSearch(array('name'));
			$crud->grid->addFormatter('type','grid/inline');
			$crud->grid->addFormatter('ReducingOrFlatRate','grid/inline');
		}

		if($crud->isEditing('add')){
			$o->now();
		}

		
	}
}