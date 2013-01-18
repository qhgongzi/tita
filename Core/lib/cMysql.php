<?php 
class cMysql
{
	private $db;
	protected $dbhost;
	private $dbuser;
	private $dbpasw;
	private $port;

    protected $dbname;
    protected $where="";
    protected $stmt;
	
	function __construct($dbcfg="db")
	{
		$app=CApplication::App();
		$this->dbhost=$app->config["$dbcfg"]['host'];
		$this->dbuser=$app->config["$dbcfg"]['username'];
		$this->dbpasw=$app->config["$dbcfg"]['password'];
		$this->dbname=$app->config["$dbcfg"]['dbname'];
        $this->port=$app->config["$dbcfg"]['port'];

		$dsn = "mysql:host=$this->dbhost;port=$this->port;dbname=$this->dbname";

		try{
			$this->db=new PDO($dsn,$this->dbuser,$this->dbpasw);
		}catch(PDOException $e){
			echo '数据库连接失败:',$e->getMessage();
			exit;
		}
		$this->db->query("set names utf8");

	}

    /*直接执行sql语句*/
    public function sqlexec($sql)
    {
        $result=$this->db->exec($sql);
        return $result;
    }

    public function sqlquery($sql)
    {
        $stmt=$this->db->query($sql);

        if (empty($stmt))
        {
            return null;
        }

        $result=$stmt->fetchAll(pdo::FETCH_ASSOC);
        return $result;
    }

    public function sqlqueryone($sql)
    {
        $stmt=$this->db->query($sql);

        if (empty($stmt))
        {
            return null;
        }
        $result=$stmt->fetch(pdo::FETCH_ASSOC);
        return $result;
    }

    public function sqlqueryscalar($sql,$column=0){
        $stmt=$this->db->query($sql);

        if (empty($stmt))
        {
            return null;
        }
        $result=$stmt->fetchColumn($column);
        return $result;
    }

    public function addCondition($param){
        if($this->where==''){
           $this->where="where $param ";
        }else{
            $this->where.="and $param ";
        }
    }

    public function pre($sql){
        $this->stmt=$this->db->prepare($sql);
    }

    public function bindIntParam($key,$val){
        $this->stmt->bindParam($key,$val,PDO::PARAM_INT);
    }

    public function bindStrParam($key,$val){
        $this->stmt->bindParam($key,$val,PDO::PARAM_STR);
    }

    public function __call($method,$args) {
        if(in_array(strtolower($method),array('fetch','fetchAll','execute','fetchColumn','rowCount'),true)) {
            return $this->stmt->$method;
        }else{
            return;
        }
    }






}