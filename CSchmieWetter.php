<?php
/*
 * @author:schmiddi
 * @email:schmiddim@gmx.at
 * @date:18.05.10
 * @desc: Hey ne Klasse, fuer mein Widget:) gibt nur Sachen aus, kein Handling für die Widgetseite. 
 * 
 * 
 */

/*
 * in C waere AdtForeCast ein struct geworden. Meine IDE hat keinen Generator für Getter und Setter 
 * - nicht meine Schuld
 * 
 * 
 */
class AdtForeCast{
	public $day, $icon, $city, $temp, $condition, $low, $high ;
	
	public function getWeatherToday(){
		return "
	     <h4>{$this->city}</h4>		
        {$this->condition}<br /> 
        {$this->temp}&#176;C<br />	
        <img src=\"{$this->icon}\" alt=\"{$this->condition}\" />\n\n";
	}//getWeatherToday;
}

class SchmieWetter{

	private $plz, $nDays;		//Plz and how for how many forecasts
	private $cityName=NULL;
	private $today, $forecasts; //Weather today and forecasts
	private $tableLayout=0;		//Tablelayout || div?
	private $highLow=0;		//Minimum Maximum anzeigen?
	public function __construct($plz, $nDays, $customCityName=""){	
		$this->plz= $plz;
		$this->nDays= $nDays;
		if (!$customCityName == "")
			$this->cityName=$customCityName;									
	}//construct

	public function overrideCityName($name){
		$this->cityName = $name;
	}//function
 
	public function makeTableLayout(){
        	$this->tableLayout= 1;
	}//function 
	public function showHighLow(){
		$this->highLow=1;
	}
	
	public function __toString(){
		$retval = $this->today->getWeatherToday();
		$retval.="<h4>Vorschau</h4>\n";
		
		if ($this->tableLayout == 1) {
    		$retval.="<table><tr>";
    		foreach ($this->forecasts as $fc){	//show  days
    			$retval.="<td>{$fc->day}</td>\n";
    		}
    		$retval.="</tr><tr>\n";
    		foreach ($this->forecasts as $fc){ //show icons
    			$retval.="<td><img src=\"{$fc->icon}\" alt=\"{$fc->condition}\" /></td>\n";
    		}
		//min & max
		if ($this->highLow == 1) {
    			$retval.="</tr><tr>\n";
    			foreach ($this->forecasts as $fc){ 
				$retval.="<td>{$fc->low}-{$fc->high}&#176;</td>\n";
			}
			
		}//min & max
    		$retval.="</tr></table>\n";
		} else {
		    $retval.="<div id='forecast' style='overflow:hidden'>\n";  
    		foreach ($this->forecasts as $fc){	//show  days
                $retval.="<div style='float:left'>";
    			$retval.="<h5>{$fc->day}</h5>\n";
    			$retval.="<img src=\"{$fc->icon}\" alt=\"{$fc->condition}\" />\n";
			if ($this->highLow == 1) {
				$retval.="<h5> {$fc->high}-{$fc->low}&#176;C</h5> \n";
			}
        		$retval.="</div>\n";
    		}
      		$retval.="</div>\n";

        }
		
		return $retval;
	}//toString
	
	public function CurlExists(){	
		if (function_exists('curl_init'))
			return true;
		return false;	
	}//test
	
	private function fetchData($plz){
		//Google Request
		$ch = curl_init("http://www.google.de/ig/api?weather=$plz+Germany");
		curl_getinfo($ch);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}//fetchData
	
	public function getWeather(){
		$output =$this->fetchData($this->plz);
		$xml_string  = simplexml_load_string(utf8_encode($output));
		$this->today = new AdtForeCast();
		
		//weather for today
		if (is_null($this->cityName)){		//user defined name for city?
				$this->today->city ="{$xml_string->weather->forecast_information->city['data']}";
		} else {
			$this->today->city = $this->cityName;
		}
		$icon="http://www.google.com/{$xml_string->weather->current_conditions->icon['data']}";				
		$this->today->icon =$icon;
		$this->today->temp="{$xml_string->weather->current_conditions->temp_c['data']}";
		$this->today->condition ="{$xml_string->weather->current_conditions->condition['data']}"; 
//		print_r($xml_string);
		
		//forecastas
		$this->forecasts = array();
		if ($this->nDays >4)
				$this->nDays = 4;
		$ctr=0;
		foreach($xml_string->weather->forecast_conditions as $element){
			if ($ctr>0){							
				if  ($ctr>=$this->nDays)
					break;
				$fc = new AdtForeCast();
				$fc->day ="{$element->day_of_week['data']}";
				$fc->condition ="{$element->condition['data']}";
				$icon="http://www.google.com/"."{$element->icon['data']}";
				$fc->icon = $icon;
				$fc->low="{$element->low['data']}";
				$fc->high="{$element->high['data']}";
				array_push($this->forecasts, $fc);
			}
				$ctr++;
	}//each

	} //parseData
	
}//class


?>
