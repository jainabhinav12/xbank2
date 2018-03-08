<?php

class page_closing extends Page {
	function page_index(){
		ini_set('memory_limit', '2048M');
		set_time_limit(0);
		gc_enable();
		
		if($_GET['do']){
			$this->api->forget('progress_data');
			
			$scheme = null;
			$account = null;
			// ===== uncomment to test for scheme or account below
			$scheme = $this->add('Model_Scheme')->load(535);
			$account = $this->add('Model_Account')->load(191840);
			// ======

			try{
				$this->api->db->dsql()->owner->beginTransaction();
				$this->api->closing_running =true;
				foreach ($b=$this->add('Model_Branch')->addCondition('PerformClosings',true) as $branch_array) {
					$b->performClosing($this->api->today,$scheme,$account);
				}
				$this->api->markProgress('COMMITING IN DB',0,'ALL BRANCHES CLOSED',1);
				// throw new \Exception("Error Processing Request", 1);
				
				// $this->api->db->dsql()->owner->rollBack();

				$p = $this->add('Model_Premium')
				$p->addCondition('account_id',$account->id);
				echo "<pre>";
				print_r($p->getRows());
				echo "</pre>";
				throw new \Exception("Error Processing Request", 1);
				
				$this->api->db->dsql()->owner->commit();
			}catch(Exception $e){
				$this->api->db->dsql()->owner->rollBack();
				throw $e;
			}
			unset($this->api->closing_running);
		}else{
			// $this->api->markProgress('branch',2,'Udaipur branch',5);
			// $this->api->markProgress('schemes',rand(100,1024),'CC 4% Interest',1024);
			// $this->api->markProgress('daily','2014-04-01','Running Daily','2014-05-31');
			$this->add('progressview/View_Progress');
		}
	}
}