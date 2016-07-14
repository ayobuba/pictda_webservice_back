<?php
/**
* @package Zedek Framework
* @subpackage Controller class
*/
namespace __zf__;
class CController extends ZController{

	function tin(){
		header('Access-Control-Allow-Origin: *');
		header("Content-type: text/json");

		if($_SERVER["REQUEST_METHOD"] != "POST"){
			$output["error_no"] = "701"; //bad request
			print json_encode($output, JSON_PRETTY_PRINT);
			Log::activity(
				array(
					'user_type'=>'', 
					'user_id'=>@$_REQUEST["id"], 
					'action'=>"Failed login using {$_SERVER['REQUEST_METHOD']}", 
					'ip'=>$_SERVER['REMOTE_ADDR'], 
					'created_on'=>strftime("%Y-%m-%d %H:%M:%S", time())
					)
				);
			return false;			
			exit;
		}

		$client = new Client;
		$bir = new BIRService;

		switch($this->uri->id){
			case "demo":
				$this->demoTINVerification($client, $bir);
				break;
			case "verification":
				$this->VerifyTIN($client, $bir);
				break;
			default:
				$output["error_no"] = "705"; //bad and illegal
				print json_encode($output, JSON_PRETTY_PRINT);
		}
	}

	function verifyTIN($client, $bir){
		$api_id = isset($_POST["id"]) ? $_POST["id"] : false; 
		$api_key = isset($_POST["key"]) ? $_POST["key"] : false; 
		$tin = isset($_POST["tin"]) ? $_POST["tin"] : false; 

		if($client->status($api_id, $api_key) && $client->validIP($api_id, $_SERVER["REMOTE_ADDR"])){
			$output["error_no"] = "700"; //no error
			$output["tin"] = $bir->exists($tin, "tax_id") ? "exists" : "not exists";
			$output["details"] = $bir->exists($tin, "tax_id") ? $bir->getDetails($tin) : array();
			$activity['action']= $bir->exists($tin, "tax_id") ? "Retrieved TIN verification for {$tin}" : "{$tin} failed verification";
		} elseif($client->status($api_id, $api_key) == false){
			$output["error_no"] = "702"; //client does not exit
			$activity['action']= "Attempt to verify {$tin} by a non client";
		} elseif($client->validIP($api_id, $_SERVER["REMOTE_ADDR"]) == false){
			$output["error_no"] = "703"; //ip is not registered
			$activity['action']= "Attempt to verify {$tin} from unregistered IP";
		} else {
			$output["error_no"] = "704"; //everything is wrong
			$activity['action']= "Unknown error on tin verification of {$tin}";
		}
		
		$activity['user_type']= 'client';
		$activity['user_id']= $_POST["id"]; 
		$activity['ip']= $_SERVER['REMOTE_ADDR'];
		$activity['created_on']  = strftime("%Y-%m-%d %H:%M:%S", time());
		
		Log::activity($activity);
		print json_encode($output, JSON_PRETTY_PRINT);
	}

	function demoTINVerification($client, $bir){
		$api_id = isset($_POST["id"]) ? $_POST["id"] : false; 
		$api_key = isset($_POST["key"]) ? $_POST["key"] : false; 
		$tin = isset($_POST["tin"]) ? $_POST["tin"] : false; 

		$validIP = true;

		if($client->status($api_id, $api_key) && $validIP){
			$output["error_no"] = "700"; //no error
			$output["tin"] = $tin == '9876543210' ? "exists" : "not exists";
			$output["details"] = $tin == '9876543210' ? $bir->getDummyDetails($tin) : array();
		} elseif($client->status($api_id, $api_key) == false){
			$output["error_no"] = "702"; //client does not exit
		} elseif($validIP == false){
			$output["error_no"] = "703"; //ip is not registered
		} else {
			$output["error_no"] = "704"; //everything is wrong
		}
		
		$activity['action']= "Demo TIN verification";
		$activity['user_type']= 'client';
		$activity['user_id']= $_POST["id"]; 
		$activity['ip']= $_SERVER['REMOTE_ADDR'];
		$activity['created_on'] = strftime("%Y-%m-%d %H:%M:%S", time());

		Log::activity($activity);
		
		print json_encode($output, JSON_PRETTY_PRINT);
	}

}