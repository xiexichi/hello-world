<?php
	
	//Mypdo类
	class PdoModel{
		//错误信息
		public $error;

		//数据库默认配置项
		protected $config = array(
		/*--------mysql数据库默认配置项-------*/
			//pdo驱动的数据库设置
			'type' => 'mysql',		//数据库类型
			'host' => 'localhost',	//数据库服务器地址
			'port' => '3306',		//数据哭端口
			// 'user' => 'root',		//数据库用户名
			'user' => 'miao',		//数据库用户名
			// 'pass' => 'CH5BPQtcW4vTBn4v',	//数据库密码
			'pass' => 'CH5BPQtcW4vTBn4v',	//数据库密码
			'dbname' => 'miao',	//数据库名
			'charset' => 'utf8',	//数据库字符集
			'prefix' => ''			//表前缀
		);

		//存放PDO对象的属性
		protected $pdo;
		//存放PDOStatement对象
		protected $stmt;

		//构造方法实例化pdo对象
		public function __construct(){
			//如果配置文件有数据库的配置项，就替换类的配置项
			if(isset($GLOBALS['config']['pdo'])){
				$this->config = $GLOBALS['config']['pdo'];
			}
			//如果pdo对象没有实例化，执行实例化pdo对象方法
			if(!($this->pdo instanceof self)){
				$this->pdo = $this->getPDO($this->config);
			}
			//开启pdo的异常提示模式
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			//设置PDO::fetch系列的返回数组类型为assoc
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
			
			//返回pdo对象
			return $this->pdo;
		}

		//实例化pdo对象方法
		/*
		*@param1  array(type = assoc)  $config   数据库的配置项
		*return   pdo连接数据库对象
		*/
		private function getPDO($config){
			try{
				//实例化pdo对象
				return new PDO(
					"{$this->config['type']}:host={$this->config['host']};port={$this->config['port']};
					dbname={$this->config['dbname']};charset={$this->config['charset']}",
					$this->config['user'],$this->config['pass']
				);

			}catch(PDOException $e){
				//异常信息提示
				echo "连接数据库失败<br/>";
				echo '错误编码：'.$e->getCode().'<br/>';
				echo '错误原因：'.iconv('GBK','UTF-8',$e->getMessage()).'<br/>';
			}	
		}

		//写方法(更新和删除)
		public function db_exec($sql){
			try{
				return $this->pdo->exec($sql);
			}catch(PDOException $e){
				//异常信息提示
				$this->db_error($e,'sql语句错误',$sql);
				return false;
			}
		}


		//预防PDO的exec出现BUG的后备方法
		protected function stmt_query($sql){
			try{
				$this->stmt = $this->pdo->query($sql);
				return $this->stmt->rowCount();
			}catch(PDOException $e){
				//异常信息提示
				$this->db_error($e,'sql语句错误',$sql);
				return false;
			}
		}

		/**
		 * 占位符执行sql方法
		 * @param  [string] $sql  [带占位符的sql]
		 * @param  [array] $data [参数数组]
		 * @return [mixed]        [执行sql的结果]
		 */
		public function db_execute($sql,$data){
			try{
				$this->stmt = $this->pdo->prepare($sql);
				$this->stmt->execute($data);
				if(!$this->stmt){
					throw new PDOException('sql语句错误');
				}
			}catch(PDOException $e){
				//异常信息提示
				$this->db_error($e,'sql语句错误',$sql);
				return false;
			}
		}

		/**
		 * 使用占位符方式获取一条数据
		 * @param  [string] $sql  [带占位符的sql]
		 * @param  [array] $data [参数数组]
		 * @return [mixed]        [执行sql的结果]
		 */
		public function db_executeOne($sql,$data){
			$this->db_execute($sql,$data);
			if(empty($this->stmt)){
				return false;
			}
			return $this->stmt->fetch();
		}

		/**
		 * 使用占位符方式获取多条数据
		 * @param  [string] $sql  [带占位符的sql]
		 * @param  [array] $data [参数数组]
		 * @return [mixed]        [执行sql的结果]
		 */
		public function db_executeAll($sql,$data){
			$this->db_execute($sql,$data);
			if(empty($this->stmt)){
				return false;
			}
			return $this->stmt->fetchAll();
		}


		//写方法(插入)
		protected function db_insert($sql){
			try{
				$this->pdo->exec($sql);
				return $this->pdo->lastInsertId();
			}catch(PDOException $e){
				//异常信息提示
				$this->db_error($e,'sql语句错误',$sql);			
				return false;
			}
		}

		/**
		 * 公开的插入方法
		 * @param  [type] $sql [description]
		 * @return [type]      [description]
		 */
		public function insert($sql){
			try{
				$this->pdo->exec($sql);
				return $this->pdo->lastInsertId();
			}catch(PDOException $e){
				//异常信息提示
				$this->db_error($e,'sql语句错误',$sql);	
				return false;		
			}
		}

		/*
		*读的基本方法
		*@param1 string $sql 查询的sql语句
		*return mixed  成功返回一个PDOStatement对象   失败返回fasle
		*/
		protected function db_query($sql){
			try{
				$this->stmt = $this->pdo->query($sql);
				if(!$this->stmt){
					throw new PDOException('sql语句错误');
				}
			}catch(PDOException $e){
				//异常信息提示
				$this->db_error($e,'sql语句错误',$sql);
				return false;
			}
		}

		/*
		*读取一条记录方法
		*@param1 string $sql 查询的sql语句
		*return  mixed  成功放回一个数组  失败返回fasle
		*/
		protected function db_getOne($sql){
			$this->db_query($sql);
			if(empty($this->stmt)){
				return false;
			}
			return $this->stmt->fetch();
		}

		/*
		*读取多条记录方法
		*@param1 string $sql 查询的语句
		*return mixed  成功返回一个数组   失败返回fasle
		*/
		protected function db_getAll($sql){
			$this->db_query($sql);
			if(empty($this->stmt)){
				return false;
			}
			return $this->stmt->fetchAll();
		}

		/**
		 * 查询单条数据的公有方法
		 * @param  [string] $sql [sql语句]
		 * @return [array]      [一维数组]
		 */
		public function find($sql){
			return $this->db_getOne($sql);
		}

		/**
		 * 查询多条数据的公有方法
		 * @param  [string] $sql [sql语句]
		 * @return [array]      [二维数组]
		 */		
		public function select($sql){
			return $this->db_getAll($sql);
		}

		/*
		*异常提示方法
		*@param1 string $msg 错误提示信息
		*/
		private function db_error($e,$msg,$sql=''){
			echo $msg.'<br/>';
			echo 'sql语句: '.$sql.'<br/>';
			echo '错误编码：'.$e->getCode().'<br/>';
			echo '错误原因：'.$e->getMessage().'<br/>';
			return false;
		}

		/*
		*pdo事务处理方法
		*@param1  string  $sql  执行的sql语句
		*@return  成功返回true   失败返回false
		*/
		protected function db_commit($sql){
			//开启事务
			$this->pdo->beginTransaction();
			//执行sql失败，返回false
			if(!$row = $this->db_exec($sql)){
				$this->pdo->rollBack();
				return false;
			}
			//执行成功,提交事务
			$this->pdo->commit();
			//受影响行数
			return $row;
		}

		/**
		 * [beginTransaction 开启事务]
		 * @return [type] [description]
		 */
		public function beginTransaction(){
			//开启事务
			$this->pdo->beginTransaction();
		}

		/**
		 * [rollBack 回滚]
		 * @return [type] [description]
		 */
		public function rollBack(){
			$this->pdo->rollBack();
		}

		/**
		 * [commit 提交事务]
		 */
		public function commit(){
			//执行成功,提交事务
			$this->pdo->commit();
		}

		/**
		 * 字段值添加单引号
		 */
		protected function add_quotes($data){
			if(is_array($data)){
				foreach($data as $k => $v){
					$data[$k] = "'".$v."'";
				}
			}else{
				$data = "'".$data."'";
			}
			return $data;
		}




		protected function getTable()
		{
			return $this->config['prefix'].$this->table;
		}

		
	}




