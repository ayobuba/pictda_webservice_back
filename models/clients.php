<?php

namespace __zf__;

class Client extends ZModel{
	public $table = "clients";

	/**
	* checks if client exists as valid client
	* @param string $cid client id
	* @param string $key client key
	* @return boolean
	*/
	function status($cid, $key){
		if($this->pairExists($cid, "api_id", $key, "api_key")){
			return true;	
		} else {
			return false;
		}	
	}

	function validIP($cid, $ip){
		$id = $this->findOne($cid, "api_id")->id;
		return $this->orm->table("clients_ips")->m2mExists($id, "client_id", $ip, "ip");
	}

	function encrypt($api_id){
		return _Form::encrypt($api_id, "long", "pictarevenue");
	}
}