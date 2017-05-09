<?php

class page_reports_deposit_duestogive extends Page {
	public $title="Dues To Give";
	
	function init(){
		parent::init();

		$till_date = $this->api->today;

		if($_GET['to_date']){
			$till_date=$_GET['to_date'];
		}
		$account_type_array=array('%'=>'All','DDS'=>'DDS','FD'=>'Fixed Account','MIS'=>'MIS','Recurring'=>'Recurring');
		$form=$this->add('Form');
		$form->addField('dropdown','account_type')->setValueList($account_type_array);
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');

		$document=$this->add('Model_Document');
		$document->depositDocuments();

		foreach ($document as $junk) {
			$form->addField('CheckBox','doc_'.$document->id, $document['name']);
		}


		$form->addSubmit('GET List');

		$grid=$this->add('Grid_AccountsBase');
		$grid->add('H3',null,'grid_buttons')->set('Dues To Give Report From ' . date('01-m-Y',strtotime($_GET['from_date'])). ' to ' . date('t-m-Y',strtotime($_GET['to_date'])) );

		$account=$this->add('Model_Account');
		$member_join=$account->join('members','member_id');
		$member_join->addField('member_name','name');
		$member_join->addField('FatherName');
		$member_join->addField('PhoneNos');
		$member_join->addField('PermanentAddress');
		
		$agent_join=$account->leftJoin('agents','agent_id');
		$agen_member_join=$agent_join->leftJoin('members','member_id');
		$agen_member_join->addField('agent_name','name');
		$agen_member_join->addField('agent_phoneno','PhoneNos');

		$account->addExpression('maturity_date')->set(function($m,$q){
			return "(IF (".$q->getField('account_type')."='FD' OR ".$q->getField('account_type')."='MIS',(
					DATE_ADD(DATE(".$q->getField('created_at')."), INTERVAL +(".$m->scheme_join->table_alias.".MaturityPeriod + 0) DAY)
				),(
				DATE_ADD(DATE_ADD(DATE(".$q->getField('created_at')."), INTERVAL +(".$m->scheme_join->table_alias.".MaturityPeriod) MONTH), INTERVAL + 0 DAY)
				)
				)
				)";
		});

		$grid_column_array=array('AccountNumber','scheme','member_name','FatherName','PermanentAddress','PhoneNos','maturity_date','Amount','MaturityAmount','agent_name','agent_phoneno','ActiveStatus','account_type');


		if($_GET['filter']){
			$this->api->stickyGET('filter');
			$this->api->stickyGET('account_type');
			$this->api->stickyGET('from_date');
			$this->api->stickyGET('to_date');
			if($_GET['account_type']){
				if($_GET['account_type']=='%')
					$account->addCondition('account_type',array_keys($account_type_array));
				else
					$account->addCondition('account_type','like',$_GET['account_type']);
			}

			if($_GET['from_date'])
				$account->_dsql()->having('maturity_date','>=',$_GET['from_date']);
			if($_GET['to_date'])
				$account->_dsql()->having('maturity_date','<=',$_GET['to_date']);

			foreach ($document as $junk) {
				$doc_id = $document->id;
				if($this->api->stickyGET('doc_'.$document->id)){
					$this->api->stickyGET('doc_'.$document->id);
					$account->addExpression($this->api->normalizeName($document['name']))->set(function($m,$q)use($doc_id ){
						return $m->refSQL('DocumentSubmitted')->addCondition('documents_id',$doc_id )->fieldQuery('Description');
					});
					$grid_column_array[] = $this->api->normalizeName($document['name']);
				}
			}
			
		}else
			$account->addCondition('id',-1);

		$account->addCondition('DefaultAC',false);
		$account->add('Controller_Acl');
		$account->addExpression('MaturityAmount','(CurrentBalanceCr + CurrentInterest - CurrentBalanceDr)');
		$grid->setModel($account,$grid_column_array);
		$grid->addPaginator(500);
		$grid->addSno();
		$grid->addFormatter('PermanentAddress','wrap');
		$grid->addTotals(['Amount','MaturityAmount']);
		// $js=array(
		// 	$this->js()->_selector('.mymenu')->parent()->parent()->toggle(),
		// 	$this->js()->_selector('#header')->toggle(),
		// 	$this->js()->_selector('#footer')->toggle(),
		// 	$this->js()->_selector('ul.ui-tabs-nav')->toggle(),
		// 	$this->js()->_selector('.atk-form')->toggle(),
		// 	);

		// $grid->js('click',$js);


		if($form->isSubmitted()){
			
			$send = array('account_type'=>$form['account_type'],'from_date'=>$form['from_date']?:0,'to_date'=>$form['to_date']?:0,'report_type'=>$form['report_type'],'filter'=>1);
			foreach ($document as $junk) {
				if($form['doc_'.$document->id])
					$send['doc_'.$document->id] = $form['doc_'.$document->id];
			}
			$grid->js()->reload($send)->execute();

			// $grid->js()->reload(array('account_type'=>$form['account_type'],'from_date'=>$form['from_date']?:0,'to_date'=>$form['to_date']?:0,'report_type'=>$form['report_type'],'filter'=>1))->execute();
		}

	}
}