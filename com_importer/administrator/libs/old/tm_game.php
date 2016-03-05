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

class TM_Game extends TM_Base {
	
	public function __construct() 
	{
		$this->defineTable();
		$this->tt = array(
			"wedstrijdnummer"		=> array("name"=>"wedstrijdnummer","type"=>"i", ),
			"intern wedstrijdnummer"=> array("name"=>"wedstrijdnr_intern","type"=>"i", ),
			"thuis team" 			=> array("name"=>"thuis_team","type"=>"s", ),
			"uit team" 				=> array("name"=>"uit_team","type"=>"s", ),
			"thuisscore" 			=> array("name"=>"thuis_score","type"=>"i", ),
			"uitscore" 				=> array("name"=>"uit_score","type"=>"i", ),
			"wedstrijddatum" 		=> array("name"=>"datum","type"=>"d", "format"=>"%d/%m/%Y" ),
			"aanvangstijd" 			=> array("name"=>"begintijd","type"=>"t", ),
			"wedstrijdstatus"		=> array("name"=>"wedstrijdstatus","type"=>"s", ),
			"scheidsrechter(s)"		=> array("name"=>"scheidsrechters","type"=>"s", ),
			"soort wedstrijd"		=> array("name"=>"soort","type"=>"s", ),
			"thuiswedstrijd?"		=> array("name"=>"thuis_wedstrijd","type"=>"s", ),
			"op terrein tegenstander?" => array("name"=>"terrein_tegenstander","type"=>"s", ),
			"accommodatie naam"		=> array("name"=>"accomodatienaam","type"=>"s", ),
			"straat" 				=> array("name"=>"straat","type"=>"s", ),
			"huisnummer"			=> array("name"=>"huisnummer","type"=>"s", ),
			"toevoeging"			=> array("name"=>"toevoeging","type"=>"s", ),
			"postcode" 				=> array("name"=>"postcode","type"=>"s", ),
			"plaats"				=> array("name"=>"plaats","type"=>"s", ),
			"telefoon"				=> array("name"=>"telefoon","type"=>"s", ),
			"veld"					=> array("name"=>"veld","type"=>"s", ),
			"veldtype"				=> array("name"=>"veldtype","type"=>"s", ),
			"veld/zaal"				=> array("name"=>"veld_zaal","type"=>"s", ),
		);
		parent::__construct();
		$this->doNotTrack();
	}
	
	public function defineTable()
	{
		
		$this->table	= "#__wedstrijdbeheer_wedstrijd";
		$this->pk		= array("wedstrijdnummer");
	}
}

/*
 ClubOfficialId,Sport code,Aanduiding code,Aanduiding,Competitiesoort,Klasse code,
 Klasse,Poule code,Poule,Intern wedstrijdnummer,Wedstrijdnummer,Elftal,Elftalcode,
 Sorteervolgorde,Thuis teamcode,Thuis team,Gesl thuis team,Thuis organisatiecode,
 Uit teamcode,Uit organisatiecode,Uit team,Gesl uit team,Wedstrijd,Thuisscore,
 Uitscore,Wedstrijddatum,Wedstrijddatum (niet geformatteerd),Aanvangstijd,
 Accommodatie code,Accommodatie naam,Straat,Huisnummer,Toevoeging,Postcode,Plaats,Telefoon,
 Veld,Veldtype,Veld/Zaal,Info,Arbitrage opmerkingen,Tuchtstatus,Bijzonderheden,
 Code buiten mededinging,Buiten mededinging,District,ClubOfficialId,Wedstrijdstatus,
 ClubOfficialId,Internetstatus,Sport omschrijving,Wedstrijddetails Code,
 DWF wedstrijd?,Verenigingsofficials,Bondsofficials,Scheidsrechter(s),
 ClubOfficialId,ClubOfficialId,Scheidsrechtercode,Scheidsrechter,
 Overige officialcode,Overige official,Scheidsrechter,Kleedkamer thuis,
 Kleedkamer uit,Kleedkamer official,Vertrektijd,Rijder(s),Soort wedstrijd,
 Thuiswedstrijd?,Kleding uitgereikt,Op terrein tegenstander?,Bondswedstrijd?,Bond/Club,Info

		$this->fields	= array(
			"id" 				=> array( "type"=>"i" ,"required"=>true, ),
			"state"				=> array( "type"=>"i"  ,"required"=>true, ) ,
			"wedstrijdnummer" 	=> array( "type"=>"i"  ,"required"=>true, ) ,
			"wedstrijdnr_intern"=> array( "type"=>"i"  ,"required"=>true, ) ,
			"thuis_team"		=> array( "type"=>"s"  ,"required"=>true, ) ,
			"uit_team"			=> array( "type"=>"s"  ,"required"=>true, ) ,
			"thuis_score"		=> array( "type"=>"i"  ,"required"=>true, ) ,
			"uit_score"			=> array( "type"=>"i"  ,"required"=>true, ) ,
			"datum" 			=> array( "type"=>"d"  ,"required"=>true, ) ,
			"begintijd"			=> array( "type"=>"t"  ,"required"=>true, ) ,
			"wedstrijdstatus"	=> array( "type"=>"s"  ,"required"=>true, ) ,
			"scheidsrechters"	=> array( "type"=>"s"  ,"required"=>true, ) ,
			"soort"				=> array( "type"=>"s" ,"required"=>true, ) ,
			"thuis_wedstrijd"	=> array( "type"=>"s"  ,"required"=>true, ) ,
			"terrein_tegenstander" => array( "type"=>"s"  ,"required"=>true, ) ,
			"accomodatienaam"	=> array( "type"=>"s"  ,"required"=>true, ) ,
			"straat" 			=> array( "type"=>"s"  ,"required"=>true, ) ,
			"huisnummer"		=> array( "type"=>"s" ,"required"=>true, ) ,
			"toevoeging"		=> array( "type"=>"s" ,"required"=>true, ) ,
			"postcode"			=> array( "type"=>"s" ,"required"=>true, ) ,
			"plaats"			=> array( "type"=>"s" ,"required"=>true, ) ,
			"telefoon"			=> array( "type"=>"s" ,"required"=>true, ) ,
			"veld"				=> array( "type"=>"s" ,"required"=>true, ) ,
			"veldtype"			=> array( "type"=>"s" ,"required"=>true, ) ,
			"veld_zaal"			=> array( "type"=>"s" ,"required"=>true, ) ,
			"params"			=> array( "type"=>"s" ,"required"=>true, ) ,
		);
*/