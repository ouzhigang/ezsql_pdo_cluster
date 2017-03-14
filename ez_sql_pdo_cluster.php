<?php

/*
基于主从读写链接方式的ezsql

dsn_cfg_arr的第1个元素为主数据库
后面的全部是从数据库

如果dsn_cfg_arr只有1个元素，主从数据库都是使用第1个元素

dsn_cfg_arr里面的元素:
array(
	"dsn" => "mysql:host=127.0.0.1;dbname=test",
	"user" => "root",
	"pwd" => "root"
)

*/

class ezSQL_pdo_cluster {
	
	private $db_read;
	private $db_write;
	
	public $rows_affected;
	public $insert_id;
	
	function __construct($dsn_cfg_arr = array()) {
		
		$dsn_cfg_arr_count = count($dsn_cfg_arr);
		if($dsn_cfg_arr_count <= 0) {
			throw new Exception("没有正确传入配置参数");
		}
		
		$this->db_read = NULL;
		$this->db_write = NULL;
		
		$this->rows_affected = 0;
		$this->insert_id = 0;
		
		if($dsn_cfg_arr_count == 1) {
			//只有1个配置，读和写都是同1个数据库链接
			
			//读
			$this->db_read = new ezSQL_pdo($dsn_cfg_arr[0]["dsn"], $dsn_cfg_arr[0]["user"], $dsn_cfg_arr[0]["pwd"]);
			
			//var_dump($dsn_cfg_arr[0]);
			//exit();
			
			//写
			$this->db_write = $this->db_read;
		}
		else {
			//如果是多个数据库链接配置，读则使用第2个以后的配置，写则使用第1个配置
			
			//读
			$tmp = array_slice($dsn_cfg_arr, 1);
			$i = array_rand($tmp);
			$this->db_read = new ezSQL_pdo($tmp[$i]["dsn"], $tmp[$i]["user"], $tmp[$i]["pwd"]);
			
			//var_dump($tmp[$i]);
			//exit();
			
			//写
			$this->db_write = new ezSQL_pdo($dsn_cfg_arr[0]["dsn"], $dsn_cfg_arr[0]["user"], $dsn_cfg_arr[0]["pwd"]);
		}
		
	}
	
	public function show_errors() {
		$this->db_read->show_errors();
		$this->db_write->show_errors();
	}

	public function hide_errors() {
		$this->db_read->hide_errors();
		$this->db_write->hide_errors();
	}
	
	public function query($query) {
	
		$query = ltrim($query);
		
		if(stripos($query, "select") === 0) {
		
			$r = $this->db_read->query($query);
			
			$this->rows_affected = $this->db_read->rows_affected;
			
			return $r;
		}
		else {
			$r = $this->db_write->query($query);
			
			$this->rows_affected = $this->db_write->rows_affected;
			$this->insert_id = $this->db_write->insert_id;
			
			return $r;
		}
	}
	
	public function flush() {
		$this->db_read->flush();
		$this->db_write->flush();
	}
	
	public function get_var($query = null, $x = 0, $y = 0) {
		return $this->db_read->get_var($query, $x, $y);
	}
	
	public function get_row($query = null, $output = OBJECT, $y = 0) {
		return $this->db_read->get_row($query, $output, $y);
	}
	
	public function get_col($query = null, $x = 0) {
		return $this->db_read->get_col($query, $x);
	}
	
	public function get_results($query = null, $output = OBJECT) {
		return $this->db_read->get_results($query, $output);
	}
	
	public function get_col_info($info_type = "name", $col_offset =- 1) {
		return $this->db_read->get_col_info($info_type, $col_offset);
	}
	
}
