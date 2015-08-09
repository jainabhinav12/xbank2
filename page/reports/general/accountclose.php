<?php

class page_reports_general_accountclose extends Page {
	public $title="Account Close Repots";
	function page_index(){
		// parent::init();

		$from_date = '1970-01-02';
		$to_date = $this->api->today;
		
		$filter = $this->api->stickyGET('filter');

		if($_GET['from_date']){
			$from_date = $this->api->stickyGET('from_date');
		}

		if($_GET['to_date']){
			$to_date = $this->api->stickyGET('to_date');
		}


		$form=$this->add('Form');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');

		$account_type=$form->addField('DropDown','account_type');
		$array_value = $array_key = explode(',', ACCOUNT_TYPES);
		$account_type->setValueList(array_combine($array_key, $array_value))->setEmptyText('Select Account type');
		
		$form->addSubmit('GET List');

		$grid=$this->add('Grid_Report_AccountClose',array('from_date'=>$from_date,'to_date'=>$to_date));

		$grid->add('H3',null,'grid_buttons')->set('Account Close Report From Date '. date('d-M-Y',strtotime($from_date)).'To Date '.date('d-M-Y',strtotime($to_date)) ); 
		
		// $q1 = '(SELECT * FROM accounts WHERE accounts.CurrentBalanceCr = accounts.CurrentBalanceDr AND accounts.CurrentBalanceCr > 0)';
		// $q = $this->api->db->dsql()->expr($q1);

		// $q=$this->api->db->dsql()->table('accounts');
		// $q->where('CurrentBalanceCr',$q->getField('CurrentBalanceDr'));
		// $q->where('CurrentBalanceCr','>',0);
		// echo $q->field('count(*)')->getOne();
		// $account_model1 = $this->api->db->dsql($this->api->db->dsql()->expr($q))->execute();
		
		$account_model = $this->add('Model_Account',array('alias'=>'xx'));
		$account_model->addCondition('branch_id',$this->api->current_branch->id);
		$account_model->addCondition('CurrentBalanceCr','>',0);
		$account_model->addCondition('CurrentBalanceCr',$account_model->getField('CurrentBalanceDr'));
		
		$member_join = $account_model->leftJoin('members','member_id');
		$member_join->addField('FatherName')->caption('Father/Husband Name');
		$member_join->addField('PermanentAddress');
		$member_join->addField('PhoneNos');

		if($_GET['filter']){
			if($_GET['account_type']){
				$selected_account_type = $this->api->stickyGET('account_type');
				$account_model->addCondition('SchemeType',$selected_account_type);
			}
			// if($_GET['as_on_date'])
				// $account_model->addCondition('created_at','<',$this->api->nextDate($_GET['as_on_date']));
		}
		$account_model->setOrder('id','desc');

		$grid->setModel($account_model,array('member','AccountNumber','SchemeType','FatherName','PermanentAddress','PhoneNos','ActiveStatus','updated_at'));
		
		if($form->isSubmitted()){
			$grid->js()->reload(array('to_date'=>$form['to_date']?:0,'from_date'=>$form['from_date']?:0,'account_type'=>$form['account_type'],'filter'=>1))->execute();
		}	

	}

}
