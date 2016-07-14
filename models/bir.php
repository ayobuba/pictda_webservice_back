<?php

namespace __zf__;

class BIRService extends ZModel{
	
	public $table = "tax_registration";

	function getDummyDetails(){
		$output["title"] = "Mr";
		$output["name"] = "NULL";
		$output["surname"] = "Barau";
		$output["first_name"] = "Luka";
		$output["middle_name"] = "Bulus";
		$output["drivers_license_number"] = "123456789012";	
		$output["national_id_card_number"] = "AA12345678";	
		$output["international_passport_number"] = "AA12345678";
		$output["gender"] = "Male";
		$output["marital_status"] = "Married";
		$output["phonenumber"] = "07021234567";	
		$output["email"] = "barau.luka@email.com";
		$output["dob_formated"] = strftime("%d %B %Y", strtotime("1982-07-12"));	
		$output["dob_unformated"] = "1982-07-12";	
		$output["home_address"] = "#3 Ryfield Street";		
		$output["home_town"] = "Tam";
		$output["local_government"] = "Mangu";	
		$output["state_of_origin"] = "Plateau";
		$output["nationality"] = "Nigerian";
		$output["company_name"] = "NULL";	
		$output["office_address"] = "GOVT HOUSE RAYFIELD";	
		$output["ministry"] = "BIR";	
		$output["market"] = "NULL";	
		$output["park"] = "NULL";	
		$output["tax_exempt"] = "0";	
		$output["company_rcc"] = "NULL";	
		$output["office_lg"] = "NULL";	
		$output["office_city"] = "JOS";	
		$output["residential_address_status"] = "Tenant";	
		$output["company_size"] = "3";	
		$output["business_commencement_date"] = "0000-00-00";	
		$output["business_ownership_type"] = "NULL";	
		return $output;		
	}

	function getDetails($tin){
		$details = $this->findOne($tin, "tax_id");
		$output["title"] = $details->title;
		$output["name"] = $details->name;
		$output["surname"] = $details->surname;
		$output["first_name"] = $details->first_name;
		$output["middle_name"] = $details->middle_name;
		$output["drivers_license_number"] = $details->drivers_license_number;	
		$output["national_id_card_number"] = $details->national_id_card_number;	
		$output["international_passport_number"] = $details->international_passport_number;
		$output["gender"] = $details->gender;
		$output["marital_status"] = $details->marital_status;
		$output["phonenumber"] = $details->phonenumber;	
		$output["email"] = $details->email;
		$output["dob_formated"] = strftime("%d %B %Y", strtotime($details->dob));	
		$output["dob_unformated"] = $details->dob;	
		$output["home_address"] = $details->home_address;		
		$output["home_town"] = $details->home_town;
		$output["local_government"] = $details->local_government;	
		$output["state_of_origin"] = $details->state_of_origin;
		$output["nationality"] = $details->nationality;
		$output["company_name"] = $details->company_name;	
		$output["office_address"] = $details->office_address;	
		$output["ministry"] = $details->ministry;	
		$output["market"] = $details->market;	
		$output["park"] = $details->park;	
		$output["tax_exempt"] = $details->tax_exempt;	
		$output["company_rcc"] = $details->company_rcc;	
		$output["office_lg"] = $details->office_lg;	
		$output["office_city"] = $details->office_city;	
		$output["residential_address_status"] = $details->residential_address_status;	
		$output["company_size"] = $details->company_size;	
		$output["business_commencement_date"] = $details->business_commencement_date;	
		$output["business_ownership_type"] = $details->business_ownership_type;	
		return $output;	
	}
	
}