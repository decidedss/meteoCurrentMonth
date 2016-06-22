<?php
//this is dangerous MKAY??? Only execute from cli
error_reporting(E_ERROR | E_PARSE);

if (php_sapi_name() != "cli") {
	exit;
}

//db connect
$db = pg_connect("host=localhost dbname=gis user=postgres password=password")
        or die('Could not connect: ' . pg_last_error());

$urls = array();
array_push($urls, "http://penteli.meteo.gr/stations/thessaloniki/NOAAMO.TXT");
array_push($urls, "http://penteli.meteo.gr/stations/amyntaio/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/serres/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/drama/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/xanthi/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/veroia/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/giannitsa/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/grevena/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/florina/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/ptolemaida/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/kastoria/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/orestiada/NOAAMO.txt");
array_push($urls,"http://penteli.meteo.gr/stations/rizomata/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/mavropigi/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/ardassa/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/vlasti/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/variko/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/seli/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/veroia/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/vegoritida/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/kerasia/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/kaimaktsalan/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/3-5pigadia/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/dion/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/sindos/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/neamichaniona/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/polygyros/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/kassandreia/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/kerkini/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/lagadas/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/nevrokopi/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/mikrokampos/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/fotolivos/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/paranesti/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/neaperamos/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/thasos/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/alexandroupolis/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/didymoteicho/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/metaxades/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/notiopedio/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/vlasti/NOAAMO.TXT");
array_push($urls,"http://penteli.meteo.gr/stations/eleftheroupoli/NOAAMO.TXT");

pg_query($db, 'TRUNCATE TABLE _meteo_currentmonth');


$q = pg_prepare($db, "insert_query", 'INSERT INTO _meteo_currentmonth(day,mean_temp,high,low,rain,avg_wind_speed,hight_wind,time_wind,dom_dir,month,station) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11);');

foreach($urls as $station){
echo $station ."\n";
$stationName = explode("/",$station);
$stationName = $stationName[4];

//get data
$data = trim(shell_exec('wget -qO- '.$station.'|sed \'1,10 d\'|head -n -2|column -t'));
//print_r($argv[1]);

//parse data
$data = explode("\n",$data);
//$q = pg_prepare($db, "insert_query", 'INSERT INTO _meteo_currentmonth(day,mean_temp,high,low,rain,avg_wind_speed,hight_wind,time_wind,dom_dir,month,station) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11);');
foreach($data as $line){

	$columns = preg_split('/\s+/', $line);
	//$columns = explode('  ',$line);
	$day = isset($columns[0]) ? $columns[0] : '-1';
	$mean_temp = isset($columns[1]) ? $columns[1] : '-1';
	$high = isset($columns[2]) ? $columns[2] : '-1';
	$low = isset($columns[4]) ? $columns[4] : '-1';
	$rain = isset($columns[8]) ? $columns[8] : '-1';
	$avg_wind_speed = isset($columns[9]) ? $columns[9] : '-1';
	$hight_wind = isset($columns[10]) ? $columns[10] : '-1';
	$time_wind = isset($columns[11]) ? $columns[11] : '00:00';
	$dom_dir = isset($columns[12]) ? $columns[12] : '-1';
	$month = date('m');
	//write data
	if(isset($columns[1]))
		$result = pg_execute($db, "insert_query", array($day,$mean_temp,$high,$low,$rain,$avg_wind_speed,$hight_wind,$time_wind,$dom_dir,$month,$stationName));
}
}
?>
