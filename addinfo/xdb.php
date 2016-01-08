<?

class xdbc {
	private $host;
	private $user;
	private $pass;
	protected $dbnm;
	protected $error;
	protected $conn;
	protected $resultq;
	protected $datasrq;
	protected $lastid;

	public function __construct($host='', $user='', $pass='', $dbnm=''){
		$host=trim($host);
		$user=trim($user);
		$pass=trim($pass);
		$dbnm=trim($dbnm);

		$this->host=$host;
		$this->user=$user;
		$this->pass=$pass;
		$this->dbnm=$dbnm;

		$this->conn=mysql_connect($this->host,$this->user,$this->pass) or trigger_error(mysql_error(),E_USER_ERROR);

		if(!$this->conn) die('Obj xdbc - init: Fail to open conection');

		} //end construct

	public function sltdb($dbnm=''){
		$dbnm=trim($dbnm);

		if(!empty($dbnm) && $dbnm!=''){
			//Select DB Name $dbnm
			mysql_select_db($dbnm, $this->conn);
			}
		else {
			//Select Default DB
			mysql_select_db($this->dbnm, $this->conn);
			}

		} //end f sltdb

	public function query($sql){
		$success=false;
		$sql=trim($sql);
		if(!empty($sql) && $sql!='') {
			if(!empty($this->resultq)) { mysql_free_result($this->resultq); }
			$this->resultq=mysql_query($sql, $this->conn) or die(mysql_error());
			$success=true;
			}
		else {
			$this->error.=date("Y-m-d H:i:s")." [E Query] : No se pudo ejecutar la consulta $sql<br />";
			}
		return $success;
		}

	public function xinsert($sql){
#		$this->sltdb();
		$success=false;
		$success=mysql_query($sql,$this->conn); // or die(mysql_error());
		if($success){
			$this->lastid=mysql_insert_id($this->conn);
			}
		return $success;
		}

	public function update($sql){
		$success=false;
		$success=mysql_query($sql,$this->conn);
		return $success;
		}

	public function re_connect(){
		mysql_close($this->conn);
		$this->conn=mysql_connect($this->host,$this->user,$this->pswd) or trigger_error(mysql_error(),E_USER_ERROR);
		}

	public function get_result(){
		return $this->resultq;
		}
	public function get_qnumrows(){
		return mysql_num_rows($this->get_result());
		}
	public function get_datarq(){
		$this->datasrq=mysql_fetch_assoc($this->resultq);
		return $this->datasrq;
		}
	public function get_lastid(){
		return $this->lastid;
		}
	public function get_errorl(){
		return $this->error;
		}
	public function __destruct(){
		if(!empty($this->resultq)) { mysql_free_result($this->resultq); }
		if($this->conn) { mysql_close($this->conn); }
		$this->datasrq = null;
		}
	}

class xdb911 extends xdbc {
	private $host='localhost'; // E: localhost or ip address
	private $user='databaseusername'; // username of database
	private $pass='databasepassword'; // user password of database
	protected $dbnm='databasename'; // name of database
	protected $error;
	protected $conn;
	protected $resultq;
	protected $datasrq;
	protected $lastid;
	
	public function __construct(){
		$this->conn=mysql_connect($this->host,$this->user,$this->pass) or trigger_error(mysql_error(),E_USER_ERROR);
		if(!$this->conn) die('Obj database - init: Fail to open conection');
		if($this->conn) $this->sltdb() ;
		} //end construct
	public function addcontact($al,$cn,$ad1,$ad2,$ct,$st,$zc,$pf,$un){
		$success=false;
		$idc=$un."_".str_replace(" ","_",$al);
		if($pf =='') $pf='NULL'  ;
		
		$sqlx="INSERT INTO contactos(idcontacto,alias,callername,address1,address2,city,state,zipcode,plusfour,user) values('$idc','$al','$cn','$ad1','$ad2','$ct','$st',$zc,$pf,'$un');";
#		printf $sqlx;
		$success = $this->xinsert($sqlx);
		return $success;
		}
	public function havecontacts($user) {
		$xrows=0;
		$sqlx="SELECT * FROM contactos WHERE user='$user';";
		$this->query($sqlx);
		$xrows=$this->get_qnumrows();
		return $xrows;
		}
	public function addheaddid($idc,$did){
		$success=false;
		$sqlx="INSERT INTO cabecera(srcp,idcontacto) values($did,'$idc');";
		$success = $this->xinsert($sqlx);
		if($success == 'true'){
			$sqlx="INSERT INTO asociados(srcp,srcx) values('$did','$did');";
			$success = $this->xinsert($sqlx);
			}
		return $success;
		}
	public function add911didvi($user,$idc,$did){
		$success=false;
		$sqlx="SELECT * FROM contactos WHERE user='$user' and idcontacto='$idc';";
		$this->query($sqlx);
		$xdata=$this->get_datarq();
#		if($xdata['plusfour']== 'NULL'){
			$pf='';
#			}
#		else {
#			$pf=
#			}
		$cadena="$idc '".$xdata['address1']."' '".$xdata['address2']."' '".$xdata['city']."' '".$xdata['state']."' ".$xdata['zipcode']." ".$pf." '".$xdata['callername']."'";
		return system("vi911insert $cadena");
		}
	} //end class xdb911

?>
