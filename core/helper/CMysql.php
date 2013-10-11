<?php
namespace core\helper;

use core\CApplication;

class CMysql
{
	private $db;
	protected $dbhost;
	private $dbuser;
	private $dbpasw;
	private $port;

    protected $dbname;
    protected $stmt;
	
	function __construct($dbcfg="db")
	{
        if(is_string($dbcfg)){
            $dbcfg=CApplication::App()->config[$dbcfg];
        }
		
		$this->dbhost=$dbcfg['host'];
		$this->dbuser=$dbcfg['username'];
		$this->dbpasw=$dbcfg['password'];
		$this->dbname=$dbcfg['dbname'];
        $this->port=$dbcfg['port'];

		$dsn = "mysql:host=$this->dbhost;port=$this->port;dbname=$this->dbname";

		try{
			$this->db=new \PDO($dsn,$this->dbuser,$this->dbpasw);
		}catch(\PDOException $e){
			echo '数据库连接失败:',$e->getMessage();
		}
		$this->db->query("set names utf8");

	}

    /*直接执行sql语句*/
    public function sqlexec($sql)
    {
        $result=$this->db->exec($sql);
        return $result;
    }

    //执行一条sql语句，并且获得所有结果
    public function sqlquery($sql)
    {
        $stmt=$this->db->query($sql);

        if (empty($stmt))
        {
            return null;
        }

        $result=$stmt->fetchAll(\pdo::FETCH_ASSOC);
        return $result;
    }

    //执行一条sql语句，获得单行结果
    public function sqlqueryone($sql)
    {
        $stmt=$this->db->query($sql);

        if (empty($stmt))
        {
            return null;
        }
        $result=$stmt->fetch(\pdo::FETCH_ASSOC);
        return $result;
    }

    //执行一条sql语句，获得一列的值
    public function sqlqueryscalar($sql,$column=0){
        $stmt=$this->db->query($sql);

        if (empty($stmt))
        {
            return null;
        }
        $result=$stmt->fetchColumn($column);
        return $result;
    }

    public function pre($sql){
        $this->stmt=$this->db->prepare($sql);
    }

    public function bindIntParam($key,$val){
        $this->stmt->bindParam($key,$val,\PDO::PARAM_INT);
    }

    public function bindStrParam($key,$val){
        $this->stmt->bindParam($key,$val,\PDO::PARAM_STR);
    }

    //开始事务
    public function beginTransaction(){
        $this->db->beginTransaction();
    }
    //提交事务
    public function commit(){
        $this->db->commit();
    }
    //回滚事务
    public function rollBack(){
        $this->db->rollBack();
    }

    public function lastInsertId(){
        return $this->db->lastInsertId();
    }

    public function __call($method,$args) {
        if(in_array(strtolower($method),array('fetch','fetchAll','execute','fetchColumn','rowCount'),true)) {
            return $this->stmt->$method;
        }else{
            return;
        }
    }

    //将查询结果以key value列形式返回
    public function kvResult($result,$key,$value=''){
        if(is_array($result)){
            $res=$result;
        }elseif(!empty($result)){
            $res=$this->sqlquery($result);
        }else{
            return false;
        }

        $kvAry=array();
        foreach ($res as $val) {
            if($value==''){
                $kvAry[$val["$key"]]=$val;
            }else{
                $kvAry[$val["$key"]]=$val["$value"];
            }
        }

        return $kvAry;
    }


}