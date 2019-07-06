<?php 



/* CRONTABS 
* Manage cron jobs , create, remove jobs
* @dir /application/libraries/Crontabs.php
* */
class Crontabs

{
	
	
	/* CONVERT STRING JOBS TO ARRAY JOBS
	*
	* @param string $job
	* @return array:  converted string
	*/
	private static function string_to_array($jobs = '')
	{
		$array = explode ("\r\n", trim($jobs));
		foreach ($array as $key => $item)
		{
			if($item == '')
			{
				unset($array[$key]);
			}
		}
		return $array;
	}
	
	//---------------------------------------------------
	
	
		/* CONVERT ARRAY JOBS TO STRING JOBS
	*
	* convert array to string in order to write crontab file
	* @param array $jobs
	* @return string : converted array
	*/
	private static function array_to_string($jobs = array())
	{
		$string = implode("\r\n", $jobs);
		return $string;
	}
	
	
	
	
	
	//---------------------------------------------------
	
	
	/* GET LIST CRON JOBS
	*
	* @return array : list cron job
	*/
	public static function get_jobs()
	{
		$output = shell_exec('crontab -l');
		return self::string_to_array($output);
	}
	
	
	
	#---------------------------------------------------
	
	
	/* SAVE CRON JOBS
	*
	* @param array $jobs
	* @param string $userLinux : logged on user
	* @return mixed 
	*/
	private static function save_jobs($jobs = array())
	{															
		$output = shell_exec('echo "'.self::array_to_string($jobs).'" | crontab -');
		return $output;
	}
	#---------------------------------------------------	
	
	/* ADD CRON JOBS
	*
	* Add job to crontab
	* @param string $jobs
	* @param string $userLinux : logged on user
	* @return mixed 
	*/
	public static function add_jobs($job = '')
	{
		if(self::does_job_exist($job))
		{
			return false;
		}
		else
		{
			$jobs = self::get_jobs();
			$jobs[] = $job;
			return self::save_jobs($jobs);
		}
	}
	
	//---------------------------------------------------	
	
	/* Check CRON JOBS Exist
	*
	* @param string $jobs
	* @return bool 
	*/
	public static function does_job_exist($job ='')
	{
		$jobs = self::get_jobs();
		if(in_array($job,$jobs))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
		//---------------------------------------------------	
	
	/* Delete Jobs Exist
	*
	* @param string $jobs
	* @return bool 
	*/
	public static function remove_job($job = '')
	{
		if(self::does_job_exist($job))
		{
			$jobs= self::get_jobs();
			unset($jobs[array_search($job,$jobs)]);
			return self::save_jobs($jobs);
		}
		else
		{
			return false;
		}
	}
}


?>