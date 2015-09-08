<?php

class page_employee extends Page{
	public $title ='Employee Management';
	function page_index(){
		// parent::init();
		$this->api->jui->addStaticStyleSheet('bank-layout','.css');
		$tab=$this->add('Tabs');
		$tab->addTabURL('./manageSalary','Salary Structure');
		$tab->addTabURL('./addEmployee','Add Employees');
		$tab->addTabURL('./salaryRecord','Salary Managment');

	}

	function page_addEmployee(){
		$emp=$this->add('Model_Employee');
		// $emp->addExpression('emp_code')->set(function($m,$q){
		// 	return $m->id;//->fieldQuery('id');
		// });

		$crud=$this->add('CRUD',array('grid_class'=>'Grid_Employee'));
		$crud->setModel($emp);
		// $crud->addRef('EmployeeSalary');
	}


	function page_manageSalary(){
		$this->api->stickyGET('branch');
		$this->api->stickyGET('month');
		$this->api->stickyGET('year');
		$this->api->stickyGET('working_day');
		
		$date=$this->api->today;
		$y=date('Y',strtotime($date));
		for ($i=$y; $i >=1970 ; $i--) { 
			$years[$i]=$i;
		}

		$month=array( '01'=>"Jan",'02'=>"Feb",'03'=>"March",'04'=>"April",
					'05'=>"May",'06'=>"Jun",'07'=>"July",'08'=>"Aug",'09'=>"Sep",
					'10'=>"Oct",'11'=>"Nov",'12'=>"Dec");

		$branch=$this->add('Model_Branch');	

		$form=$this->add('Form',null,null,array('form/empty'));
		// $record_form->addStyle(array('border'=>'2px solid black'));
		$col1=$form->add('Columns');
		// $col1->addColumn(3)->addField('Dropdown','branch')->setEmptyText('Please Select Branch')->validateNotNull(true)->setModel($branch);
		$month_col=$col1->addColumn(3);
		$f_month=$month_col->addField('Dropdown','month')->setValueList($month)->setEmptyText('Please Select Month')->validateNotNull(true);
		$year_col=$col1->addColumn(3);
		$f_year=$year_col->addField('Dropdown','year')->setValueList($years)->setEmptyText('Please Select Year')->validateNotNull(true);
		$wd_col=$col1->addColumn(2);
		$wd=$wd_col->addField('line','working_day','Total Day In Month');
		$twf_col=$col1->addColumn(2);
		$twf=$twf_col->addField('line','monthly_off');
		$mid_col=$col1->addColumn(2);
		$mid=$mid_col->addField('line','monthly_in_day');	
		$form->addSubmit('Get Result');
		$form->add('View')->setHtml('&nbsp;<br/><br/>')->addClass('');

		// $f_month->js( 'change')->univ()->totalDayInMonth($wd,$f_month,$f_year);

		//Second Form

		$record_form=$this->add('Form',null,null,array('form/empty'));

		if($form->isSubmitted()){
			$record_form->js()->reload(array(
										'month'=>$form['month'],
										'year'=>$form['year'],
										'working_day'=>$form['working_day'],
										'monthly_off'=>$form['monthly_off'],
										'monthly_in_day'=>$form['monthly_in_day'],
										'filter'=>1
										)
			)->execute();
		}
		
		$record_form->add('View')->setHtml('&nbsp;<br/><br/>')->addClass('');
		$lable_c=$record_form->add('Columns');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('Name & B. Salary');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('T. Days');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('Paid Days');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('Total Leave');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('Salary');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('PF Salary');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('DED.');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('PF AMT');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('Other ALW.');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('ALW. Paid');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('Net Payable');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('Narrtion');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('CL');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('CCL');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('LWP');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('ABSENT');
		$lable_c->addColumn(1)->addClass('bank-col-2')->add('H5')->set('Monthly Off');
		$lable_c->addColumn(1)->addClass('bank-col-1')->add('H5')->set('T.Month in Day');

		// $emp_model = $this->add('Model_EmployeeSalary');
		$emp_model=$this->add('Model_Employee')->addCondition('is_active',true);

		// $emp_salary_j->addField('month');
		// $emp_salary_j->addField('year');
		// $emp_salary_j->addField('paid_days');
		// $emp_salary_j->addField('total_days');
		// $emp_salary_j->addField('leave');
		// $emp_salary_j->addField('salary');
		// $emp_salary_j->addField('ded');
		// $emp_salary_j->addField('pf_amount');
		// $emp_salary_j->addField('allow_paid');
		// $emp_salary_j->addField('narration');
		// $emp_salary_j->addField('CL');
		// $emp_salary_j->addField('CCL');
		// $emp_salary_j->addField('LWP');
		// $emp_salary_j->addField('ABSENT');


		foreach ($emp_model as  $junk) {

			// Check for existing entry
			$emp_salary =  $this->add('Model_EmployeeSalary');
			$emp_salary->addCondition('employee_id',$emp_model->id);
			$emp_salary->addCondition('month',$_GET['month']);
			$emp_salary->addCondition('year',$_GET['year']);
			$emp_salary->tryLoadAny();

			$col= $record_form->add('Columns')->addClass('atk-box');
			$cl=$col->addColumn(1)->addClass('bank-col-1 atk-col-1');
			$cl->add('View')->setHtml($emp_model['name']."&nbsp<br/>[ ".$emp_model['basic_salary'].' ]');
			$cl->addField('hidden','name_'.$emp_model['id'])->set($emp_model['name']);
			// $cl->addField('hidden','branch_'.$emp_model['id'])->set($emp_model['branch_id']);
			$basic_salary = $cl->addField('hidden','basic_salary_'.$emp_model['id'])->set($emp_model['basic_salary']);
			
			$t_c=$col->addColumn(1)->addClass('bank-col-2');
			$td = $t_c->addField('hidden','total_days_'.$emp_model['id'])->set($emp_salary['total_days']?:$_GET['working_day']);
			$t_c->add('View')->setHtml($emp_salary['total_days']?:$_GET['working_day'].'&nbsp;')->addClass('value-text bank-col-2');
			
			$pd_col = $col->addColumn(1)->addClass('bank-col-2');
			$pd=$pd_col->addField('line','paid_days_'.$emp_model['id'])->set($emp_salary['paid_days']);
			$col->addColumn(1)->addClass('bank-col-2')->addField('line','leave_'.$emp_model['id'])->set($emp_salary['leave']);
			
			$total_days = 30;
			if($emp_salary['total_days'])
				$total_days = $emp_salary['total_days'];

			$new_salary_amount=($emp_model['basic_salary']/$total_days * $emp_salary['paid_days']); 
			
			$s_c=$salary=$col->addColumn(1)->addClass('bank-col-1');
			$salary_f = $s_c->addField('hidden','salary_'.$emp_model['id'])->set($new_salary_amount);
			$s_c->add('View')->setHtml($emp_salary['salary'].'&nbsp')->addClass('value-text');
			
			$p_c=$col->addColumn(1)->addClass('bank-col-1');
			$pf=$p_c->addField('hidden','pf_salary_'.$emp_model['id'])->set($salary_f);
			$p_c->add('View')->setHtml($emp_salary['salary'].'&nbsp')->addClass('value-text');

			$ded_col=$col->addColumn(1)->addClass('bank-col-1');
			$ded=$ded_col->addField('line','ded_'.$emp_model['id'])->set($emp_salary['ded']);

			$new_pf_amount =  round(($emp_salary['salary'] / 100) * 12);
			
			$pf_col=$col->addColumn(1)->addClass('bank-col-1');
			$pf_amount=$pf_col->addField('line','pf_amount_'.$emp_model['id'])->set($new_pf_amount);
			
			$o_c=$col->addColumn(1)->addClass('bank-col-1');
			$other_allw=$o_c->addField('hidden','other_allowance_'.$emp_model['id'])->set($emp_model['other_allowance']);
			$o_c->add('View')->setHtml($emp_model['other_allowance'].'&nbsp');
			

			$ap_col=$col->addColumn(1)->addClass('bank-col-1');
			$ap=$ap_col->addField('line','allow_paid_'.$emp_model['id'])->set($emp_salary['allow_paid']);
			
			$new_nt_amount= ($emp_salary['salary'] + $emp_salary['allow_paid'] - $emp_salary['ded']-$emp_salary['pf_amount']);			

			$n_c=$col->addColumn(1)->addClass('bank-col-1');
			$nt=$n_c->addField('hidden','net_payable_'.$emp_model['id'])->set($new_nt_amount);
			$n_c->add('View')->setHtml($record_form['net_payable_'.$emp_model['id']].'&nbsp')->addClass('value-text');
			$record_form->add('View')->setHtml('&nbsp;<br/>');
			
			$col->addColumn(1)->addClass('bank-col-1 bank-col-3')->addField('line','narration_'.$emp_model['id'])->set($emp_salary['narration']);
			$cl_col=$col->addColumn(1)->addClass('bank-col-2');
			$cl_col->addField('line','cl_'.$emp_model['id'])->set($emp_salary['CL']);
			$cl_col->add('View')->set($emp_model['cl_allowed'])->addClass('atk-box');
			$col->addColumn(1)->addClass('bank-col-2')->addField('line','ccl_'.$emp_model['id'])->set($emp_salary['CCL']);
			$col->addColumn(1)->addClass('bank-col-2')->addField('line','lwp_'.$emp_model['id'])->set($emp_salary['LWP']);
			$col->addColumn(1)->addClass('bank-col-2')->addField('line','absent_'.$emp_model['id'])->set($emp_salary['ABSENT']);
			$wf_col=$col->addColumn(1)->addClass('bank-col-1');
			$wf=$wf_col->addField('line','monthly_off_'.$emp_model['id'])->set($emp_salary['monthly_off']?:$_GET['monthly_off']);
			$tmd_col=$col->addColumn(1)->addClass('bank-col-2');
			$tmd=$tmd_col->addField('line','total_month_day_'.$emp_model['id'])->set($emp_salary['total_month_day']?:$_GET['monthly_in_day']);

			$ded->js( 'change')->univ()->netpayable($nt,$salary_f,$ap,$ded,$pf_amount);
			$ap->js( 'change')->univ()->netpayable($nt,$salary_f,$ap,$pf_amount,$ded);
			$pf_amount->js( 'change')->univ()->netpayable($nt,$salary_f,$ap,$pf_amount,$ded);
			$pd->js( 'change')->univ()->salary($salary_f,$basic_salary,$td,$pd);
			$pd->js( 'change')->univ()->allowPaid($ap,$pd,$td,$other_allw);
			$wd->js( 'change')->univ()->workingDays($td,$wd);
			$pd->js( 'change')->univ()->pfSalary($pf,$salary_f,$emp_model['pf_deduct']=='YES'?1:0);
			$pd->js( 'change')->univ()->pfAmount($pf_amount,$salary_f,$emp_model['pf_deduct']=='YES'?1:0);
			$twf->js( 'change')->univ()->weeklyOff($wf,$twf);
			$mid->js( 'change')->univ()->dayInMonth($tmd,$mid);
			$f_year->js( 'change')->univ()->totalDayInMonth($wd,$f_month,$f_year,$td);
		}

		$record_form->addSubmit('Go');

		if($record_form->isSubmitted()){
			foreach ($emp_model as  $junk) {
				$salary = $this->add('Model_EmployeeSalary');
				$salary->addCondition('employee_id', $emp_model->id);
				// $salary->addCondition('branch_id', $emp_model['branch_id']);
				$salary->addCondition('month', $_GET['month']);
				$salary->addCondition('year', $_GET['year']);
				
				$salary->tryLoadAny();

				$salary['total_days']=$record_form['total_days_'.$emp_model['id']];
				$salary['branch_id']=$emp_model['branch_id'];
				$salary['paid_days']=$record_form['paid_days_'.$emp_model['id']];
				$salary['leave']=$record_form['leave_'.$emp_model['id']];
				$salary['salary']=$record_form['salary_'.$emp_model['id']];
				$salary['pf_salary']=$record_form['pf_salary_'.$emp_model['id']];
				$salary['ded']=$record_form['ded_'.$emp_model['id']];
				$salary['pf_amount']=$record_form['pf_amount_'.$emp_model['id']];
				$salary['other_allowance']=$record_form['other_allowance_'.$emp_model['id']];
				$salary['allow_paid']=$record_form['allow_paid_'.$emp_model['id']];
				$salary['net_payable']=$record_form['net_payable_'.$emp_model['id']];
				$salary['narration']=$record_form['narration_'.$emp_model['id']];
				$salary['CL']=$record_form['cl_'.$emp_model['id']];
				$salary['CCL']=$record_form['ccl_'.$emp_model['id']];
				$salary['LWP']=$record_form['lwp_'.$emp_model['id']];
				$salary['ABSENT']=$record_form['absent_'.$emp_model['id']];
				$salary['monthly_off']=$record_form['monthly_off_'.$emp_model['id']];
				$salary['total_month_day']=$record_form['total_month_day_'.$emp_model['id']];
				$salary->save();
			}
			$record_form->js()->reload(array(
										'branch_id'=>$record_form['branch'],
										'month'=>$record_form['month'],
										'year'=>$record_form['year'],
										'working_day'=>$record_form['working_day'],
										'filter'=>1
										)
			)->execute();

		}
		// }
	}

	function page_salaryRecord(){
	
		$month=array( '01'=>"Jan",'02'=>"Feb",'03'=>"March",'04'=>"April",
					'05'=>"May",'06'=>"Jun",'07'=>"July",'08'=>"Aug",'09'=>"Sep",
					'10'=>"Oct",'11'=>"Nov",'12'=>"Dec");
		
		$date=$this->api->today;
		$y=date('Y',strtotime($date));	
		for ($i=$y; $i >=1970 ; $i--) { 
			$years[$i]=$i;
		}
		$form=$this->add('Form',null,null,array('form/horizontal'));
		$branch_field=$form->addField('Dropdown','branch')->setEmptyText('Please Select Branch');
		$branch_field->setModel('Branch');
		$form->addField('Dropdown','month')->setValueList($month)->validateNotNull(true)->setEmptyText('Please Select Month');
		$form->addField('Dropdown','year')->setValueList($years)->validateNotNull(true)->setEmptyText('Please Select Year');
		$form->addField('autocomplete/Basic','employee')->setModel('Model_Employee');
		$form->addSubmit('Get Record');

		$v = $this->add('View');
		// $grid=$this->add('Grid_Employee');

		$this->api->stickyGET('branch');
		$this->api->stickyGET('month');
		$this->api->stickyGET('year');
		
		if($this->api->stickyGET('filters')){
			$salary_model=$this->add('Model_EmployeeSalary');
			$crud = $v->add('CRUD',array('grid_class'=>'Grid_EmployeeRecord','allow_del'=>false,'allow_add'=>false,'allow_edit'=>false));
			
			if($_GET['branch']){
				$salary_model->addCondition('branch_id',$_GET['branch']);
			}
			if($_GET['month']){				
				$salary_model->addCondition('month',$_GET['month']);
			}
			if($_GET['year']){
				$salary_model->addCondition('year',$_GET['year']);
			}
			if($_GET['employee']){
				$salary_model->addCondition('employee_id',$_GET['employee']);
			}
			
			$crud->setModel($salary_model);
		}

		
		if($form->isSubmitted()){
			$v->js()->reload(array(
								'branch'=>$form['branch']?:0,
								'month'=>$form['month']?:0,
								'year'=>$form['year']?:0,
								'employee'=>$form['employee']?:0,
								'filters'=>1)
							)
			->execute();
		}	
	}

	function render(){
		$this->app->pathfinder->base_location->addRelativeLocation(
		    'epan-components/'.__NAMESPACE__, array(
		        'php'=>'lib',
		        'template'=>'templates',
		        'css'=>array('templates/css','templates/js'),
		        'img'=>array('templates/css','templates/js'),
		        'js'=>'templates/js',
		    )
		);

		$this->js()->_load('emp-salary');
		// $this->api->jquery->addStylesheet('xShop-js');
		// 	$this->api->template->appendHTML('js_include','<script src="epan-components/xShop/templates/js/xShop-js.js"></script>'."\n");
		parent::render();	
}
}