<?php

class page_dealer_dashboard extends Page{
	function init(){
		parent::init();

		$this->title = " ";
		$this->app->dealer_menu->addMenuItem('dealer_dashboard',$this->api->auth->model['name']);

		// $this->add('View')->set('Dealer Panel');
		// $tab=$this->add('Tabs');
		// $tab->addTabURL('agent_schemes','Schemes');
		// $tab->addTabURL('agent_emiduelist','EMI Due List');
		// $tab->addTabURL('agent_ddsduelist','DDS Due List');
		// $tab->addTabURL('agent_duestoreceived','Dues to Receive');
		// $tab->addTabURL('agent_duestogive','Dues to Give');
		// $tab->addTabURL('agent_detail','Details Report');
		// $tab->addTabURL('agent_activeinactiveaccount','Agent Accounts');
		// $tab->addTabURL('agent_periodical','Periodical Account');
		// $tab->addTabURL('agent_tds','TDS Report');
		// $tab->addTabURL('agent_crpb','CRPB Report');
		// $tab->addTabURL('agent_accountdetailed','Account Details');
	}	
}