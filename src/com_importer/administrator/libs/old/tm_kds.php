<?php
/**
 * @version     1.0.0
 * @package     com_teammanager
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Pieter <joomla@jort.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.log.log');

class KDSException extends Exception 
{
	public function __construct($message, $code, Exception $previous = null) {
		JLog::add(JText::_("Error $code : $message"), JLog::WARNING, 'com_teammanager');
        parent::__construct($message, $code, $previous);
    }

}

class tm_kds 
{
	private static $instance = false;
	private $stub, $stubpath, $sessionid;
	private $apipath, $apikey;
	private $requests, $hash;
	
	public static function getInstance($stub = false) {
		if (self::$instance === false)
			self::$instance = new kds($stub);
		return self::$instance;
	}

	public function getTeam($teamid) {
	
	}

	private function __construct($stub)
	{
		$this->stub		= $stub;
		$this->apikey	= 'test';
		$this->apipath	= 'as80';
		$this->stubpath	= 'forza-almere';
	
		$this->requests['stub']['init'] = 'http://api.knvbdataservice.nl/stub/initialisatie';
		$this->requests['api']['init']	= 'http://api.knvbdataservice.nl/api/initialisatie';
		$this->requests['stub']['teams'] = 'http://api.knvbdataservice.nl/stub/teams';
		$this->requests['api']['teams']	= 'http://api.knvbdataservice.nl/api/teams';
		$this->requests['stub']['uitslag'] = 'http://api.knvbdataservice.nl/stub/teams/';
		$this->requests['api']['uitslag'] = 'http://api.knvbdataservice.nl/api/teams/';
		$this->initialise();
	}
	
	private function initialise() {
		if ($this->stub)
		{
			$request = $this->requests['stub']['init'].'/'.$this->stubpath;
		}
		else
		{
			$request = $this->requests['api']['init'].'/'.$this->apipath;
		}
		
		try 
		{
			$result  = $this->callKDS($request);
			$this->sessionid = $result['List'][0]['PHPSESSID'];
			$path = ($this->stub)?$this->stubpath:$this->apipath;
			$this->hash	= md5($this->apikey.'#'.$path.'#'.$this->sessionid);
		} 
		catch (KDSException $e) 
		{
			$this->sessionid = '';
			$this->hash = '';
		}
		return false;		
	}

	public function getTeams() 
	{
		$request = ($this->stub)?$this->requests['stub']['teams']:$this->requests['api']['teams'];
		$request.= '?PHPSESSID='.$this->sessionid.'&hash='.$this->hash;
		
		try 
		{
			$result = $this->callKDS($request);
			$teams	= $result['List'];
		} 
		catch (KDSException $e) 
		{
			$teams = array();
		}
		return $teams;		
	}

	public function getUitslag( $teamid, $weeknr = 0, $competition = '') 
	{
		$request = ($this->stub)?$this->requests['stub']['uitslag']:$this->requests['api']['uitslag'];
		$request .= $teamid .'/results?';
		if ($weeknr!=0) $request .= 'weeknummer='.$weeknr.'&';
		if ($competition!='') $request .= 'comptype='.$competition.'&';
		$request .= 'PHPSESSID='.$this->sessionid.'&hash='.$this->hash;
	
		try
		{
			$result = $this->callKDS($request);
			$uitslagen	= $result['List'];
		}
		catch (KDSException $e)
		{
			$uitslagen = array();
		}
		return $uitslagen;
	}
	
	private function callKDS($request)
	{
		//	$auth		= base64_encode('RABONETEU\SchaafsmaPA:Mohaa067');
		//	$aContext	= array('http' =>  array( 'header'=> array("Proxy-Authorization: Basic $auth", "Authorization: Basic $auth","HTTP_X_APIKEY: ".$this->apikey,),
		//						   'proxy' => 'tcp://10.243.66.7:8080', 'request_fulluri' => true, ), );
		$context 	= stream_context_create(array('http' => array( 'header'=> array("HTTP_X_APIKEY: ".$this->apikey,),),));
		$response	= file_get_contents($request, false, $context);
		if ($respons===false) throw new KDSException( "Could not get file content for query $request", "1");
		$result	= json_decode($response,true);
		if ($result===NULL) throw new KDSException( "Output is NULL","2");
		if (isset($result['error'])) throw new KDSException( $result['error']['message'],$result['error']['errorcode']);
		if ($result['errorcode']!=1000) throw new KDSException( $result['message'],$result['errorcode']);
		return $result;
	}
}