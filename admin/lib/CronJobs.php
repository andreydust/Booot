<?php

class CronJobs {
	
	protected $cronJobs = array(
		'Yearly'	=> 0,
		'Monthly'	=> 0,
		'Weekly'	=> 0,
		'Daily'		=> 0,
		'Hourly'	=> 0,
		'Minutely'	=> 0
	);
	
	protected $cronPeriods = array(
		'Yearly'	=> 217728000,
		'Monthly'	=> 18144000,
		'Weekly'	=> 604800,
		'Daily'		=> 86400,
		'Hourly'	=> 3600,
		'Minutely'	=> 60
	);
	
	public function CronJobs() {
		$nowTime = time();
		$cronJobs = unserialize(getVar('cronJobs'));
		if(isset($cronJobs) && !empty($cronJobs)) {
			$this->cronJobs = $cronJobs;
		}
		
		foreach ($this->cronJobs as $period => $lastRun) {
			if($lastRun + $this->cronPeriods[$period] <= $nowTime) {
				$this->cronJobs[$period] = $nowTime;
				setVar('cronJobs', serialize($this->cronJobs));
				$this->$period();
				break;
			}
		}
	}
	
	private function Minutely() {
		
	}
	
	private function Hourly() {
		//Апдейт курсов валют
		$ccbr = new CurrencyCBR();
		$ccbr->Update();
	}
	
	private function Daily() {
		
	}
	
	private function Weekly() {
		
	}
	
	private function Monthly() {
		
	}
	
	private function Yearly() {
		
	}
	
	
}

?>