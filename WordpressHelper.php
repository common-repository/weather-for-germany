<?php
/*
 * @desc: ein paar Hilfsfunktionen zum Erzeugen von Eingabefeldern und so für Wordpress
 * @autor:schmiddi
 * @email:schmiddim@gmx.at 
 * @version:0.1
 * 
 */

class SideBarWidgetHelper{
	private $namespace="";
	private $options = array();
	private $wpOptions=array();		//options from the WP DB Table
	const TEXT = 'genTextField';
	const CHECKBOX = 'genCheckBox';
	//set Default values
	
	//
	public function addOption($name,  $type, $description, $default){
		$option = array();
		$option['name'] = $name;
		$option['type'] = $type;
		$option['description'] = $description;
		$option['default'] = $default;
		array_push($this->options, $option);
		
		//falls keine Daten vorhanden...
		$this->wpOptions = get_option($this->namespace);
		if (empty($this->wpOptions[$name]) ||!key_exists($name, $this->wpOptions)){
			$this->wpOptions[$name] = $default;
		 	update_option($this->namespace, $this->wpOptions );
		}//fi
		
	}//addOption
	
	public function save(){			
		if (array_key_exists("{$this->namespace}-submit", $_POST)){
			$options = array();
			foreach ($this->options as $option){
				$name = $option['name'];
				if(array_key_exists("{$this->namespace}-$name", $_POST))
					$options[$name] = strip_tags(stripslashes($_POST["{$this->namespace}-$name"]));
			}//each	
			update_option("schmie_wetter", $options);
		}//fi
		
		
	}
	
	public function __toString(){
		$retval = "";
		foreach ($this->options as $option){		 	
		 	if(method_exists($this, $option['type'])){
		 		$retval.=$this->{$option['type']}($option['description'], $option['name']);
		 
		 	}//fi
		}//each
		$retval.=$this->genSubmitField();
		return $retval;
	}//toString
	
	public function __construct($namespace){
		$this->namespace = $namespace;
	}
	private function genTextField($description, $option){
		$namespace = $this->namespace;
		$options = get_option($namespace);
		$optionValue = htmlspecialchars($options[$option], ENT_QUOTES);		
		return "
			<p style=\"text-align:right;\"><label for=\"$namespace-$option\">$description</label>
			<input style=\"width: 150px;\" id=\"$namespace-$option\" 
			name=\"$namespace-$option\" type=\"text\" value=\"$optionValue\" /> 
	";
	}//genTextField
	 
	private function genCheckBox ($description, $option) {
		$namespace = $this->namespace;		
		$checked="";
		$options = get_option($namespace);		
		if ($options[$option] == $option)
			$checked= 'checked="checked"';
		return "<p style=\"text-align:right;\"><label for=\"$namespace-$option\">$description</label>
				<input type=\"checkbox\" name=\"$namespace-$option\" id=\"$namespace-$option\"
				value=\"$option\" $checked/>";
	}//genCheckBox

	private function genSubmitField(){
		$namespace = $this->namespace;
		return  "
		<input type=\"hidden\" id=\"$namespace-submit\" name=\"$namespace-submit\" value=\"1\" />";
	}//genSubmitField
	
}//class

?>