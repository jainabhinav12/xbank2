<?php
class Model_Account_CC extends Model_Account{

	public $transaction_deposit_type = TRA_CC_ACCOUNT_AMOUNT_DEPOSIT;	
	public $transaction_withdraw_type = TRA_CC_ACCOUNT_AMOUNT_WITHDRAWL;	
	public $default_transaction_deposit_narration = "CC Account Amount Deposit in {{AccountNumber}}";	
	public $default_transaction_withdraw_narration = "Amount withdrawl from CC Account {{AccountNumber}}";	

	function init(){
		parent::init();

		$this->addCondition('SchemeType','CC');

		$this->getElement('agent_id')->destroy();
		$this->getElement('scheme_id')->getModel()->addCondition('SchemeType','CC');
		$this->getElement('Amount')->caption('CC Limit');

		$this->addHook('editing',$this);

		//$this->add('dynamic_model/Controller_AutoCreator');
	}

	function editing(){
		
	}

	function createNewAccount($member_id,$scheme_id,$branch_id, $AccountNumber,$otherValues=array(),$form=null,$on_date=null){

		$otherValues += array('account_type'=>ACCOUNT_TYPE_CC);

		$new_account_id = parent::createNewAccount($member_id,$scheme_id,$branch_id, $AccountNumber,$otherValues,$form,$on_date);
		if($this['Amount'])
			$this->doProsessingFeesTransactions($on_date);
	}

	function doProsessingFeesTransactions($on_date=null){
		if(!$on_date) $on_date = $this->api->now;

		$processing_fee = $this->ref('scheme_id')->get('ProcessingFees') * $this['Amount'] / 100;
		$transaction = $this->add('Model_Transaction');
		
		$transaction->createNewTransaction(TRA_CC_ACCOUNT_OPEN, null, $on_date, "CC Account Opened",null,array('reference_account_id'=>$this->id));
		$transaction->addDebitAccount($this,$processing_fee);
	
		$credit_account = $this->ref('branch_id')->get('Code') . SP . PROCESSING_FEE_RECEIVED . $this->ref('scheme_id')->get('name');		
		$transaction->addCreditAccount($credit_account,$processing_fee);

		$transaction->execute();

	}

	function deposit($amount,$narration=null,$accounts_to_debit=null,$form=null,$transaction_date=null,$transaction_in_branch=null){
		if(!$transaction_in_branch) $transaction_in_branch = $this->api->current_branch;
		$this['CurrentInterest'] = $this['CurrentInterest'] + $this->getCCInterest($transaction_date);
		$this->save();
		parent::deposit($amount,$narration,$accounts_to_debit,$form,$transaction_date,$transaction_in_branch);
	}

	function withdrawl($amount,$narration=null,$accounts_to_credit=null,$form=null,$on_date=null,$transaction_in_branch=null){
		if(!$transaction_in_branch) $transaction_in_branch = $this->api->current_branch;
		$ccbalance = $this['Amount'] - ($this['CurrentBalanceDr'] - $this['CurrentBalanceCr']);
		if ($ccbalance < $amount)
			throw $this->exception('Cannot withdraw more than '. $ccbalance,'ValidityCheck')->setField('amount');

		$this['CurrentInterest'] = $this['CurrentInterest'] + $this->getCCInterest($on_date);
		$this->save();
		parent::withdrawl($amount,$narration,$accounts_to_credit,$form,$on_date,$transaction_in_branch);
	}

	function getCCInterest($on_date=null,$from_date=null,$on_amount=null, $at_interest_rate=null){
		if(!$on_date) $on_date = $this->api->today;
		if(!$from_date) $from_date = $this['LastCurrentInterestUpdatedAt'];
		if(!$on_amount){
			$openning_balance = $this->getOpeningBalance($this->api->nextDate($from_date));
			$on_amount = ($openning_balance['DR']) - ($openning_balance['CR'])>0?  :0;
		}
		if(!$at_interest_rate) $at_interest_rate = $this->ref('scheme_id')->get('Interest');

		$days = $this->api->my_date_diff($on_date,$from_date);

		$interest = $on_amount * $at_interest_rate * $days['days_total'] / 36500;
		return $interest;
	}

	/**
	 * [applyMonthlyInterest description]
	 * @param  MySql_Date_String  $on_date interest till date ... transaction of provided date included ie last date of any month
	 * @param  boolean $return    if set true, no changes or trnsaction will be saved to database only interest will get calculate and returned 
	 * @return number             returns interest as number if argument return is set true
	 */
	function postInterestEntry($on_date=null, $return=false){
		if(!$on_date) $on_date = $this->api->today;
		if(!$this->loaded()) throw $this->exception('Account must be loaded to apply monthly interest');

		// Interest from last transaction to month end
		$current_interest = $this['CurrentInterest'] + $this->getCCInterest($on_date);
		$this['CurrentInterest']=0; // Make Zero to be ready for next months Interests
		$this['LastCurrentInterestUpdatedAt'] = $on_date;

		if($return) return $current_interest;
		
		$this->save();

		if($current_interest == 0 ) return; //no need to save a new transaction of zero interest

		$transaction = $this->add('Model_Transaction');
		$transaction->createNewTransaction(TRA_INTEREST_POSTING_IN_CC_ACCOUNT, null, $on_date, "Interest posting in CC Account",null,array('reference_account_id'=>$this->id));

		$transaction->addCreditAccount($this->ref('branch_id')->get('Code') . SP . INTEREST_RECEIVED_ON . $this['scheme_name'], $current_interest);
		$transaction->addDebitAccount($this,$current_interest);
		$transaction->execute();
	}
}