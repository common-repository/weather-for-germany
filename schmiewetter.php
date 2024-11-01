<?php
/*
 Plugin Name: schmie_Wetter
 Plugin URI: http://schmiddi.co.cc/wordpress_plugins/
 Description: WetterWidget fuer Deutschland
 Author: Michael Schmitt (schmiddim@gmx.at)
 Version: 1.4.4
 Author URI: http://schmiddi.co.cc/wordpress_plugins/
 License: GPL 2.0, @see http://www.gnu.org/licenses/gpl-2.0.html
 @date 05.06.11
 
 
 */
#error_reporting(-1);
require_once (dirname(__FILE__).'/CSchmieWetter.php');
require_once (dirname(__FILE__).'/WordpressHelper.php');


function schmie_wetter_init() {

	// Ueberorueftt Wordpress-Funktion, Abbruch wenn nicht vorhanden
	if ( !function_exists('wp_register_sidebar_widget') )
	return;

	// Ausgabe Frontend
	function  schmie_wetter($args) {
		extract($args);
		// Auslesen der Optionen
		$options = get_option('schmie_wetter');
		$titel = htmlspecialchars($options['titel'], ENT_QUOTES);
		$plz=htmlspecialchars($options['plz'], ENT_QUOTES);
		$vorschau=htmlspecialchars($options['vorschau'], ENT_QUOTES);
		$override=htmlspecialchars($options['overrideOrt'], ENT_QUOTES);
		$ort=htmlspecialchars($options['ort'], ENT_QUOTES);
		$tableLayout=htmlspecialchars($options['tableLayout'], ENT_QUOTES);
		$highLow=htmlspecialchars($options['highLow'], ENT_QUOTES);
		// Ausgabe des Widgets
		echo $before_widget ;		
		echo "$before_title<h3 class='widget-title'>$titel</h3>$after_title";		
		#echo "$before_title<h3>$titel</h3>$after_title";		
		$sw = new SchmieWetter($plz, $vorschau);
		
		if ($override =="overrideOrt")		//Ortsname ueberschreiben?
			$sw->overrideCityName($ort);				

		if ($tableLayout =="tableLayout")		//TableLayout?
			$sw->makeTableLayout();
		if ($highLow =="highLow")
			$sw->showHighLow();
		$sw->getWeather();				//Daten regeln
		echo $sw;						//Ausgabe
		echo $after_widget;
		
	}

	// back end controller
	function schmie_wetter_controll() {
	 	//Felder erzeugen
	   	$wh = new SideBarWidgetHelper('schmie_wetter');	   		   	 	
	   	$wh->addOption('titel',SideBarWidgetHelper::TEXT, 'Titel', 'wetterWetter');
	 	$wh->addOption('plz',SideBarWidgetHelper::TEXT, 'Postleitzahl', '64285');
	 	$wh->addOption('vorschau',SideBarWidgetHelper::TEXT, 'Wieviele Tage soll ich in die Zukunft schauen?', '4');
	   	$wh->addOption('ort',SideBarWidgetHelper::TEXT, 'Ortsname', '');
	   	$wh->addOption('overrideOrt',SideBarWidgetHelper::CHECKBOX, 'eigenen Ortsnamen verwenden', '');
		$wh->addOption('highLow',SideBarWidgetHelper::CHECKBOX, 'Extrema des Tages anzeigen', '1');	   	
		$wh->addOption('tableLayout',SideBarWidgetHelper::CHECKBOX, 'Tabellenlayout verwenden', '0');
	   	
	   	
	   	echo $wh;		//Ausgeben
		$wh->save();	//überprüft, ob was speichernswertes im Post Array ist 
	}//schmie_wetter_control
	$pluginName = 'schmie_wetter';	             
	$ops = array(  'classname' => $pluginName,
					'description' =>'Zeigt das Wetter in deutschen Staedten nach Postleitzahl an.' );
	
	//Widget registrieren
	wp_register_sidebar_widget($pluginName, 'Schmie_Wetter','schmie_Wetter', $ops);	
	register_widget_control($pluginName, 'schmie_wetter_controll');

}//schmie_wetter_init

add_action('widgets_init', 'schmie_wetter_init');

?>
