<?php
/**
 * @package Zedek Framework
 * @subpackage Controller class
 */
namespace __zf__;
class CController extends ZController{

    public $updateLimit = 250;

    function __construct(){
        parent::__construct();
        if($_SERVER["REQUEST_SCHEME"] != "https"){
            //$this->redirect();
        }
    }

    function tin(){
        header('Access-Control-Allow-Origin: *');
        header("Content-type: text/json");
        if($_SERVER["REQUEST_METHOD"] != "POST"){
            $output["error_no"] = "706"; //bad request
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

    function payment(){
        header('Access-Control-Allow-Origin: *');
        header("Content-type: text/json");
        if($_SERVER["REQUEST_METHOD"] != "POST"){
            $output["error_no"] = "706"; //bad request
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
        }

        $client = new Client;
        $bir = new BIRService;

        switch($this->uri->id){
            case "history":
                $this->paymentHistory($client, $bir);
                break;
            case "demo":
                $this->demoHistory($client, $bir);
                break;
            default:
                $output["error_no"] = "705"; //bad and illegal
                print json_encode($output, JSON_PRETTY_PRINT);
        }
    }

    function get(){
        if($_SERVER["REQUEST_METHOD"] != "POST"){
            $output["error_no"] = "706"; //bad request
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
        header('Access-Control-Allow-Origin: *');
        header("Content-type: text/json");
        $bir = new BIRService;
        $client = new Client;

        $api_id = isset($_POST["id"]) ? $_POST["id"] : false;
        $api_key = isset($_POST["key"]) ? $_POST["key"] : false;
        $tin = isset($_POST["tin"]) ? $_POST["tin"] : false;

        if($client->status($api_id, $api_key)){
            switch($this->uri->id){
                case "banks":
                    $output = $bir->getBanks($api_id);
                    break;
                case "payment_methods":
                    $output = $bir->getPaymentMethods($api_id);
                    break;
                case "products":
                    $output = $bir->getProducts($api_id);
                    break;
                default:
                    $output = array('error_no'=>901);
            }
        } else {
            $output = array('error_no'=>902);
        }


        print json_encode($output, JSON_PRETTY_PRINT);
    }

    private function verifyTIN($client, $bir){
        $api_id = isset($_POST["id"]) ? $_POST["id"] : false;
        $api_key = isset($_POST["key"]) ? $_POST["key"] : false;
        $tin = isset($_POST["tin"]) ? $_POST["tin"] : false;

        if($client->status($api_id, $api_key)){
            if($bir->exists($tin, "tax_id")){
                $output["tin"] = "exists";
                $output["error_no"] = "700"; //no error
            } else {
                $output["tin"] = "not exists";
                $output["error_no"] = "701"; //tin is invalid
            }

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

    private function paymentHistory($client, $bir){
        $api_id = isset($_POST["id"]) ? $_POST["id"] : false;
        $api_key = isset($_POST["key"]) ? $_POST["key"] : false;
        $revenue_code = isset($_POST["revenue_code"]) ? $_POST["revenue_code"] : false;
        $from = isset($_POST["from"]) ? $_POST["from"] : false;
        $to = isset($_POST["to"]) ? $_POST["to"] : false;
        $filters = isset($_POST["filters"]) ? $_POST["filters"] : array();

        if($client->status($api_id, $api_key)){
            $output["history"] = $bir->getHistory($api_id, $from, $to, $filters);

            if($output != false){
                $output["error_no"] = "800"; //no error
                $activity['action']= "Retrieved payment history";
            } else {
                $output["error_no"] = "801"; //empty result set
                $activity['action']= "Failed to retrieve Retrieve payment history";
            }


        } elseif($client->status($api_id, $api_key) == false){
            $output["error_no"] = "802"; //client does not exit
            $activity['action']= "Attempt to retrieve payment history for {$revenue_code} by a non client";
        } elseif($client->validIP($api_id, $_SERVER["REMOTE_ADDR"]) == false){
            $output["error_no"] = "803"; //ip is not registered
            $activity['action']= "Attempt to retrieve payment history for {$revenue_code} from unregistered IP";
        } else {
            $output["error_no"] = "804"; //everything is wrong
            $activity['action']= "Unknown error on payment history retrieval for {$revenue_code}";
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

    private function demoHistory(){}

    /**
     * takes from tin updates and payment updated and updated services server
     */
    function plug(){
        $this->tinUpdates();
        $this->paymentUpdates();
    }

    private function tinUpdates(){
        $dbo = $this->orm->dbo;
        $last_tax_id = $this->orm->table("last_ids")->row(1)->last_tax_id;
        $c = curl_init("http://plateau.taxo-igr.com/bir_services/socket.php");
        curl_setopt_array(
            $c,
            array(
                CURLOPT_POST=>true,
                CURLOPT_POSTFIELDS=>array(
                    'type'=>"tin",
                    'last_id'=>$last_tax_id,
                    'limit'=>$this->updateLimit,
                ),
                CURLOPT_RETURNTRANSFER => true
            )
        );

        $json = curl_exec($c);
        $array = json_decode($json);

        try{
            $dbo->beginTransaction();
            foreach ($array as $i=>$rec) {
                $q = "	INSERT INTO tax_registration (
							idtax_registration, 
							gender, 
							title, 
							name, 
							home_address, 
							state_of_origin, 
							home_town, 
							local_government, 
							occupation, 
							company_name, 
							office_address, 
							ministry, 
							market, 
							park, 
							phone_number, 
							email_address, 
							photo_url, 
							tax_id, 
							registered_by, 
							biometric_status, 
							registration_type, 
							group_id, 
							taxpayer_password, 
							tax_exempt, 
							active, 
							disability, 
							rf1, 
							rf2, 
							rf3, 
							rf4, 
							rf5, 
							lf1, 
							lf2, 
							lf3, 
							lf4, 
							lf5, 
							registered_on, 
							drivers_license_number, 
							national_id_card_number, 
							international_passport_number, 
							company_rcc, 
							workplace_category, 
							office_lg, 
							parent_id, 
							workplace_type, 
							marital_status, 
							nationality, 
							vend_pin, 
							residential_address_status, 
							dob, 
							surname, 
							first_name, 
							middle_name, 
							utin, 
							last_pw_reset_by, 
							temp_reg, 
							company_size, 
							business_commencement_date, 
							proprietor_tax_id, 
							business_ownership_type, 
							has_subsidiary, 
							subsidiary, 
							subsidiary_of 
						) VALUES (
							'{$rec->idtax_registration}', 
							'{$rec->gender}', 
							'{$rec->title}', 
							'{$rec->name}', 
							'{$rec->home_address}', 
							'{$rec->state_of_origin}', 
							'{$rec->home_town}', 
							'{$rec->local_government}', 
							'{$rec->occupation}', 
							'{$rec->company_name}', 
							'{$rec->office_address}', 
							'{$rec->ministry}', 
							'{$rec->market}', 
							'{$rec->park}', 
							'{$rec->phone_number}', 
							'{$rec->email_address}', 
							'{$rec->photo_url}', 
							'{$rec->tax_id}', 
							'{$rec->registered_by}', 
							'{$rec->biometric_status}', 
							'{$rec->registration_type}', 
							'{$rec->group_id}', 
							'{$rec->taxpayer_password}', 
							'{$rec->tax_exempt}', 
							'{$rec->active}', 
							'{$rec->disability}', 
							'{$rec->rf1}', 
							'{$rec->rf2}', 
							'{$rec->rf3}', 
							'{$rec->rf4}', 
							'{$rec->rf5}', 
							'{$rec->lf1}', 
							'{$rec->lf2}', 
							'{$rec->lf3}', 
							'{$rec->lf4}', 
							'{$rec->lf5}', 
							'{$rec->registered_on}', 
							'{$rec->drivers_license_number}', 
							'{$rec->national_id_card_number}', 
							'{$rec->international_passport_number}', 
							'{$rec->company_rcc}', 
							'{$rec->workplace_category}', 
							'{$rec->office_lg}', 
							'{$rec->parent_id}', 
							'{$rec->workplace_type}', 
							'{$rec->marital_status}', 
							'{$rec->nationality}', 
							'{$rec->vend_pin}', 
							'{$rec->residential_address_status}', 
							'{$rec->dob}', 
							'{$rec->surname}', 
							'{$rec->first_name}', 
							'{$rec->middle_name}', 
							'{$rec->utin}', 
							'{$rec->last_pw_reset_by}', 
							'{$rec->temp_reg}', 
							'{$rec->company_size}', 
							'{$rec->business_commencement_date}', 
							'{$rec->proprietor_tax_id}', 
							'{$rec->business_ownership_type}', 
							'{$rec->has_subsidiary}', 
							'{$rec->subsidiary}', 
							'{$rec->subsidiary_of}'
						)";
                $this->orm->execute($q);
            }

            $q = "	SELECT `idtax_registration` AS last_tax_id 
					FROM tax_registration 
					ORDER BY `idtax_registration` 
					DESC 
					LIMIT 1
			";
            $last_tax_id = $dbo->query($q)->fetchObject()->last_tax_id;
            $row = $this->orm->table("last_ids")->row(1);
            $row->last_tax_id = $last_tax_id;
            $row->commit();
            $dbo->commit();
        }catch(Exception $e){
            $dbo->rollback();
        }
    }

    private function paymentUpdates(){
        $dbo = $this->orm->dbo;
        $last_payment_id = $this->orm->table("last_ids")->row(1)->last_payment_id;
        $c = curl_init("http://plateau.taxo-igr.com/bir_services/socket.php");
        curl_setopt_array(
            $c,
            array(
                CURLOPT_POST=>true,
                CURLOPT_POSTFIELDS=>array(
                    'type'=>"payment",
                    'last_id'=>$last_payment_id,
                    'limit'=>$this->updateLimit,
                ),
                CURLOPT_RETURNTRANSFER => true
            )
        );

        $json = curl_exec($c);
        $array = json_decode($json);

        try{
            $dbo->beginTransaction();
            foreach ($array as $i=>$rec) {
                $q = "	INSERT INTO payment_report (
							`idpayments`, 
							`tax_id`, 
							`revenue_code`, 
							`amount_paid`, 
							`BankName`, 
							`date`, 
							`payment_method` 
						) VALUES (
							'{$rec->idpayments}',
							'{$rec->tax_id}',
							'{$rec->revenue_code}', 
							'{$rec->amount_paid}', 
							'{$rec->BankName}', 
							'{$rec->date}', 
							'{$rec->payment_method}'
						)";
                $this->orm->execute($q);
            }

            $q = "	SELECT `idpayments` AS last_payment_id 
					FROM payment_report  
					ORDER BY `idpayments` 
					DESC 
					LIMIT 1
			";
            $last_payment_id = $dbo->query($q)->fetchObject()->last_payment_id;
            $row = $this->orm->table("last_ids")->row(1);
            $row->last_payment_id = $last_payment_id;
            $row->commit();
            $dbo->commit();
        }catch(Exception $e){
            $dbo->rollback();
        }
    }

    //function createKey(){print _Form::encrypt("404421005");}
}