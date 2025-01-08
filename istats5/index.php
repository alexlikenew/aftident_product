<?php
// error_reporting(E_ALL);
define('_ROOT_DIR_',dirname(__FILE__).'/');
require_once(_ROOT_DIR_.'Mobile_Detect.php');
Class TC_Stats{
	public $Db, $prefix, $config, $Mobile_Detect;
	public function __construct(){
		if(file_exists($file = _ROOT_DIR_ . '../config.inc.php')){
			require_once($file);
			$this->DB_Connect()
				 ->CheckExistsTable();
		}else{
			$this->Error("Brak pliku {$file}");
		}
		$this->config = new StdClass();
		$this->config->count_back_years = 5;
		$this->Mobile_Detect = new Mobile_Detect;
		$this->typ_urzadzenia = ($this->Mobile_Detect->isMobile() ? ($this->Mobile_Detect->isTablet() ? 'tablet' : 'phone') : 'computer');
	}
	
	public function DB_Connect(){
		try{
			$this->Db = new PDO('mysql:host='.base64_decode(DB_HOST).';dbname='.base64_decode(DB_NAME).';encoding=utf8', base64_decode(DB_USER), base64_decode(DB_PASS));
			$this->Db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->prefix = DB_PREFIX;
		
		}
		catch(PDOException $e){
			echo 'Połączenie nie mogło zostać utworzone: ' . $e->getMessage();
		}
		return $this;
	}
	
	public function CheckExistsTable(){
		$tables = $this->Db->query("SHOW TABLES");
		foreach($tables as $t=>$i){
			$dx=array_values($i);
			$tab[]=$dx[0];
		}
		if(!in_array($this->prefix.'tc_stats',$tab)){
			$this->Db->query("CREATE TABLE IF NOT EXISTS `{$this->prefix}tc_stats` (
			  `id` int(20) NOT NULL AUTO_INCREMENT,
			  `data_add` int(10) NOT NULL,
			  `ip` varchar(20) NOT NULL,
			  `hostname` varchar(150) NOT NULL,
			  `city` varchar(30) NOT NULL,
			  `region` varchar(50) NOT NULL,
			  `loc` varchar(50) NOT NULL,
			  `org` varchar(255) NOT NULL,
			  `url_link` varchar(255) NOT NULL,
			  `http_user_agent` varchar(255) NOT NULL,
			  `typ_urzadzenia` varchar(255) NOT NULL,
			  `count_unit` int(10) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		}
		return $this;
	}
	
	public function SaveRequest(){
		if(!isset($_COOKIE[__CLASS__])){
			$time = self::GetSecOnDay();
			setcookie(__CLASS__,base64_encode(json_encode($time)) , time()+$time->time_left);
			$this->Insert('tc_stats',array('data_add' => time(),'ip' => $_SERVER['REMOTE_ADDR'],'hostname' => '','city' => '','region' => '','loc' => '','org' => '','url_link' => $_REQUEST['url_href_link'],'http_user_agent'=>$_SERVER['HTTP_USER_AGENT'],'typ_urzadzenia'=>$this->typ_urzadzenia));
		}else{
			$t = $this->Select("SELECT * FROM <@>tc_stats WHERE ip='{$_SERVER['REMOTE_ADDR']}' ORDER BY id DESC")->{0};
			$this->Update('tc_stats',array('count_unit'=>(int)$t->count_unit+1),'id='.$t->id);
		}
		header('Content-Type: image/jpeg');
		$im = imagecreatetruecolor(1, 1);
		imagejpeg($im, NULL, 1);
		imagedestroy($im);
		
	}
	
	
	public function Select($sql,$rObj=true){
		$sql = str_replace('<@>',DB_PREFIX,$sql);
		try{
			$DbOut = $this->Db->query($sql);
			if($rObj==true)
				$out = new StdClass();
			else
				$out = array();
		
			$x=0;
			foreach($DbOut as $i){
				if($rObj==true){
						$out->{$x} = json_decode(json_encode($i),false);
				}else{
					$out[] = $i;
				}
				$x++;
			}
				return $out;
		}catch(PDOException $e){
			  echo 'Błąd zapytania: ' . $e->getMessage();
	    }
	}
	public function Update($table,$data=array(),$where=''){
		try{
		$table=$this->prefix . $table;
		foreach($data as $key=>$val){
				$SQL_AND[]="{$key}=:{$key}";
		}
		$this->InsertRow = $this->Db->prepare('UPDATE `'.$table.'` SET '.implode(',',$SQL_AND).' WHERE '.$where);
		foreach($data as $key=>$val){
			$this->InsertRow->bindValue(':'.$key, $val, PDO::PARAM_STR);
		}
			
		$this->InsertRow->execute();
		}catch(PDOException $e){
			echo $e->getMessage();
			
		}
		return $this;
	}
	public function Insert($table,$data=array()){
		$table=$this->prefix . $table;
		$this->InsertRow = $this->Db->prepare('INSERT INTO `'.$table.'` ('.implode(', ',array_keys($data)).') VALUES(:'.implode(', :',array_keys($data)).');');
		foreach($data as $key=>$val){
			$this->InsertRow->bindValue(':'.$key, $val, PDO::PARAM_STR);
		}
		$this->InsertRow->execute();
		return $this;
	}
	public static function GetSecOnDay(){
		$date = new DateTime(date('Y-m-d'));
		
		$obj = new StdClass();
		$obj->elapsed_time = ((int)date("G")*3600)+((int)date("i")*60)+(int)date("s");
		$obj->time_left = 86400 - $obj->elapsed_time;
		$obj->start_time = (int)$date->format('U');
		$obj->end_time = 86400 + $obj->start_time;
		return $obj;
	}
	public function Error($txt){
		echo '<h2>'.$txt.'</h2>';
	}
	public function ShowPanel(){
		
		$t=$this->Select("SELECT * FROM <@>tc_stats WHERE hostname=''");
		foreach($t as $i){
			if($i->ip !== '127.0.0.1'){
					$json = json_decode(file_get_contents("http://ipinfo.io/{$i->ip}"));
				$this->Update('tc_stats',array('hostname'=>$json->hostname, 'city'=>$json->city, 'region'=>$json->region, 'loc'=>$json->loc, 'org'=>$json->org ),'id='.$i->id);
			}
		}
		$time= self::GetSecOnDay();
		$sql_1 = $this->Select("SELECT * FROM <@>tc_stats WHERE `data_add` >= {$time->start_time} AND data_add <= {$time->end_time}");
		$ile1=0;
		foreach($sql_1 as $ui){
			$ile1++;
		}
		$start_time=$time->start_time-(7*86400);
		$sql_2 = $this->Select("SELECT * FROM <@>tc_stats WHERE `data_add` >= {$start_time} AND data_add <= {$time->end_time}");
		$ile2=0;
		foreach($sql_2 as $ui){
			$ile2++;
		}
		$start_time=$time->start_time-(31*86400);
		$sql_3 = $this->Select("SELECT * FROM <@>tc_stats WHERE `data_add` >= {$start_time} AND data_add <= {$time->end_time}");
		$ile3=0;
		foreach($sql_3 as $ui){
			$ile3++;
		}
		$sql_4 = $this->Select("SELECT * FROM <@>tc_stats ORDER BY id DESC");
		$sql_4des = $this->Select("SELECT * FROM <@>tc_stats WHERE typ_urzadzenia='computer' or typ_urzadzenia='' ORDER BY id DESC");
		$sql_4mob = $this->Select("SELECT * FROM <@>tc_stats WHERE typ_urzadzenia='phone' or  typ_urzadzenia='tablet' ORDER BY id DESC");
		$ile4=0;
		foreach($sql_4 as $ui){
			$ile4++;
		}
		$ile_tabela = $this->GetCollectDatas();
		$ile_tabela_des = $this->GetCollectDatas(" AND (typ_urzadzenia='computer' OR typ_urzadzenia='')");
		$ile_tabela_mob = $this->GetCollectDatas(" AND (typ_urzadzenia='phone' OR typ_urzadzenia='tablet')");
		/*
			DANE DLA TABELKI INFORMACJE OGÓLNE
		*/
		$_unix = new DateTime((string)'01-01-'.date('Y'));
		$_unix=$_unix->format('U');
		$_unix2 = new DateTime((string)'01-'.date('m').'-'.date('Y'));
		$_unix2=$_unix2->format('U');
		$_unix3 = new DateTime((string)'31-'.date('m').'-'.date('Y'));
		$_unix3=$_unix3->format('U');
		$_unix4 = new DateTime(date('d-m-Y'));
		$_unix4=$_unix4->format('U');
		$_unix4a = $_unix4 + 86400;
		$_unix5 = time()-3600;
		$sql_informacje_ogolne = $this->Select("SELECT 
			(SELECT SUM(count_unit) FROM <@>tc_stats ) as suma_wszystkich_wywolan,
			(SELECT SUM(count_unit) FROM <@>tc_stats WHERE data_add >= '{$_unix}') as suma_w_tym_roku,
			(SELECT SUM(count_unit) FROM <@>tc_stats WHERE data_add >= '{$_unix2}' AND data_add <= '{$_unix3}') as suma_w_tym_miesiacu,
			(SELECT SUM(count_unit) FROM <@>tc_stats WHERE data_add >= '{$_unix4}' AND data_add <= '{$_unix4a}') as suma_w_tym_dniu,
			(SELECT SUM(count_unit) FROM <@>tc_stats WHERE data_add >= '{$_unix5}' AND data_add <= '".time()."') as suma_ostatnia_godzina,
			(SELECT COUNT(DISTINCT `ip`) FROM <@>tc_stats) AS unikalnych_hostow
		")->{0};
		// pre($sql_informacje_ogolne);
		if(isset($_GET['SHOW_PANEL']))
			require_once(dirname(__FILE__).'/themes.html');
		
	}
	public function GetUnixTimeFromYear($year,$where=null){
		for($x=1;$x<=12;$x++){
			$d = new DateTime("{$year}-{$x}-01 00:00:00");
			$start = $d->format('U');
			$dd = new DateTime("{$year}-{$x}-31 00:00:00");
			$end  = $dd->format('U');
			$tab[$x]=$this->Select("SELECT count(id) as ile FROM <@>tc_stats WHERE `data_add` >= {$start} AND data_add <= {$end} {$where}")->{0}->ile;
		}
			return $tab;
	}
	public function GetCollectDatas($where=null){ 
		$start = (int)date("Y");	$start=$start-$this->config->count_back_years;
		$stop = (int)date("Y");
		for($x=$stop;$x>=$start;--$x){
			$year[$x] =$this->GetUnixTimeFromYear($x,$where);
		}
		
		return $year;
		
		
		
	}
	
	
}
$TC_Stats= new TC_Stats();
if(isset($_REQUEST['url_href_link'])){
	$TC_Stats->SaveRequest();
}else{
	$TC_Stats->ShowPanel();
}

function pre($s){
	echo '<pre>';
	print_r($s);
	echo '</pre>';
	
}