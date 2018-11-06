<?php

class page_reports_deposit_comparativebusiness extends Page {
	public $title ="Comparative Business Reports";
	
	function init(){
		parent::init();

		$tabs = $this->add('Tabs');
		$agent_tab = $tabs->addTab('Agent Base');
		$mo_tab = $tabs->addTab('MO Base');

		$this->manageAgentBase($agent_tab);
		$this->manageAgentBase($mo_tab,$with_mo=true);
	}

	function manageAgentBase($tab, $with_mo=false){
		$model = 'Agent';
		if($with_mo){
			$model = 'Mo';
		}

		$form = $tab->add('Form');
		$form->addField('DatePicker','from_date')->validateNotNull();
		$form->addField('DatePicker','to_date')->validateNotNull();

		$agent_mo_field=$form->addField('autocomplete/Basic',$model);
		$agent_mo_field->setModel($model);

		$form->addSubmit('Go');

		$agent_mo_model = $this->add('Model_Agent');
		if($with_mo){
			$agent_mo_model->getElement('branch_id')->destroy();
			$agent_mo_model->addExpression('branch_id')->set($agent_mo_model->refSQL('mo_id')->fieldQuery('branch_id'));
		}
		$agent_mo_model->add('Controller_Acl');
		
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$selected_agent_mo = $this->app->stickyGET($model);

		$account_types = ['Recurring','FixedAndMis','DDS2'];
		if($with_mo) 
			$grid_columns = ['mo','agent_member_name'];
		else
			$grid_columns = ['agent_member_name'];

		if($from_date){
			$date_range = $this->get_date_ranges($from_date,$to_date);
			// var_dump($date_range);
			foreach ($account_types as $type) {
				foreach ($date_range as $dr) {
					$agent_mo_model->addExpression($this->app->normalizeName($dr['start'].' = '. $dr['end']. ' new_'.$type))->set(function($m,$q)use($type,$dr){
						$accounts = $this->add('Model_Account_'.$type);
						$accounts->addCondition('agent_id',$q->getField('id'));
						$accounts->addCondition('created_at','>=',$dr['start']);
						$accounts->addCondition('created_at','<',$this->app->nextDate($dr['end']));
						return $accounts->count();
					})->caption($dr['start'].' <br/> '. $dr['end']. '<br/> new_'.$type);

					$agent_mo_model->addExpression($this->app->normalizeName($dr['start'].' = '. $dr['end']. ' amount_'.$type))->set(function($m,$q)use($type,$dr){
						$accounts = $this->add('Model_Account_'.$type);
						$accounts->addCondition('agent_id',$q->getField('id'));
						$accounts->addCondition('created_at','>=',$dr['start']);
						$accounts->addCondition('created_at','<',$this->app->nextDate($dr['end']));
						return $accounts->sum('Amount');
					})->caption($dr['start'].' <br/> '. $dr['end']. '<br/> amount_'.$type);

					$grid_columns[] = $this->app->normalizeName($dr['start'].' = '. $dr['end']. ' new_'.$type);
					$grid_columns[] = $this->app->normalizeName($dr['start'].' = '. $dr['end']. ' amount_'.$type);

					// add RD Specific count and amount from transactions
					if($type=='Recurring'){
						$agent_mo_model->addExpression($this->app->normalizeName($dr['start'].' = '. $dr['end']. ' rd_collection_accounts'))->set(function($m,$q)use($type,$dr){
							$tra_row = $this->add('Model_TransactionRow');
							$acc_j = $tra_row->join('accounts','account_id');
							$acc_j->addField('agent_id');
							$scheme_j = $acc_j->join('schemes','scheme_id');
							$scheme_j->addField('SchemeType');

							$tra_row->addCondition('created_at','>=',$dr['start']);
							$tra_row->addCondition('created_at','<',$this->app->nextDate($dr['end']));
							$tra_row->addCondition('transaction_type',TRA_RECURRING_ACCOUNT_AMOUNT_DEPOSIT);
							$tra_row->addCondition('agent_id',$q->getField('id'));
							$tra_row->addCondition('SchemeType','Recurring');

							return $tra_row->_dsql()->del('fields')->field('COUNT(DISTINCT(account_id))');
						})->caption($dr['start'].' <br/> '. $dr['end']. '<br/> rd_collection_accounts');

						$agent_mo_model->addExpression($this->app->normalizeName($dr['start'].' = '. $dr['end']. ' rd_collection_amount'))->set(function($m,$q)use($type,$dr){
							$tra_row = $this->add('Model_TransactionRow');
							$acc_j = $tra_row->join('accounts','account_id');
							$acc_j->addField('agent_id');
							$scheme_j = $acc_j->join('schemes','scheme_id');
							$scheme_j->addField('SchemeType');

							$tra_row->addCondition('created_at','>=',$dr['start']);
							$tra_row->addCondition('created_at','<',$this->app->nextDate($dr['end']));
							$tra_row->addCondition('transaction_type',TRA_RECURRING_ACCOUNT_AMOUNT_DEPOSIT);
							$tra_row->addCondition('agent_id',$q->getField('id'));
							$tra_row->addCondition('SchemeType','Recurring');

							return $tra_row->sum('amountCr');
						})->caption($dr['start'].' <br/> '. $dr['end']. '<br/> rd_collection_amount');

						$grid_columns[] = $this->app->normalizeName($dr['start'].' = '. $dr['end']. ' rd_collection_accounts');
						$grid_columns[] = $this->app->normalizeName($dr['start'].' = '. $dr['end']. ' rd_collection_amount');
					}

				}
			}
		
			if($selected_agent_mo){

			}

		}

		$or_cond = [];
		foreach ($grid_columns as $gc) {
			if($gc=='agent_member_name') continue;
			$or_cond[] = [$gc ,' > ',0];
		}
		if($or_cond)
			$agent_mo_model->addCondition($or_cond);

		$grid = $tab->add('Grid');
		$grid->setModel($agent_mo_model,$grid_columns);

		$grid->addPaginator(100);

		if($form->isSubmitted()){
			$grid->js()->reload(['from_date'=>$form['from_date'],'to_date'=>$form['to_date'],$model=>$form[$model]])->execute();
		}
	}

	function get_months($date1, $date2) { 
	   $time1  = strtotime($date1); 
	   $time2  = strtotime($date2); 
	   $my     = date('n-Y', $time2); 
	   $mesi = array('01','02','03','04','05','06','07','08','09','10','11','12');

	   //$months = array(date('F', $time1)); 
	   $months = array(); 
	   $f      = ''; 

	   while($time1 < $time2) { 
	      if(date('n-Y', $time1) != $f) { 
	         $f = date('n-Y', $time1); 
	         if(date('n-Y', $time1) != $my && ($time1 < $time2)) {
	         	$str_mese=$mesi[(date('n', $time1)-1)];
	            $months[] = date('Y', $time1)."-".$str_mese; 
	         }
	      } 
	      $time1 = strtotime((date('Y-n-d', $time1).' +15days')); 
	   } 

	   $str_mese=$mesi[(date('n', $time2)-1)];
	   $months[] = date('Y', $time2)."-".$str_mese; 
	   return $months; 
	} 

	function get_date_ranges($date1,$date2){
		$months_list = $this->get_months($date1, $date2);
		$start=true;
		$date_ranges=[];
		foreach ($months_list as $ml) {
			if($start){
				$date_ranges[]=['start'=>$date1,'end'=>date('Y-m-t',strtotime($date1))];
				$start=false;
			}elseif($ml==$months_list[count($months_list)-1]) {
				// It is last
				$date_ranges[]=['start'=>date('Y-m-01',strtotime($date2)),'end'=>$date2];
			}else{
				$date_ranges[]=['start'=>$ml.'-01','end'=>date('Y-m-t',strtotime($ml.'-01'))];
			}
		}

		return $date_ranges;
	}

}