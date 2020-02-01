<?php 

	class dbinfo {
		public $servername = "localhost";
		public $username = "toptmlog_user2";
		public $password = "bI,%MKJoWjO{";
		public $db = "toptmlog_user";
	}
	class db extends dbinfo {
		// Create connection
		function conn(){
			return $conn = new mysqli($this->servername, $this->username, $this->password, $this->db);
		}
	}


	class work extends db {
		function user_name ($uid){
			$sql = "SELECT name FROM `hm2_users` WHERE id=$uid";
			$result = $this->conn()->query($sql);
			return $row = $result->fetch_assoc();
		}
		function last_sent_id () {
			$sql = "SELECT `last_sent_id` FROM `hm2_last_sent_id` LIMIT 1 ";
			$result = $this->conn()->query($sql);
			$row = $result->fetch_assoc();
			return $row['last_sent_id'];
		}
		function update_last_sent_id ($id){
			$sql = "UPDATE hm2_last_sent_id SET last_sent_id=$id WHERE id=1";
			$this->conn()->query($sql);
		}
		function get_msg(){
			$last_sent_id = $this->last_sent_id();
			$sql = "SELECT * FROM `hm2_history` WHERE type='withdrawal'AND id>$last_sent_id LIMIT 1 ";
			$result = $this->conn()->query($sql);
			if($result->num_rows>0){
				$row = $result->fetch_assoc();
			}else{
				$row = FALSE;
			}		
			return $row;
		}
		function send_notification () {
			 $arr = $this->get_msg();
			 if(!$arr){
				$lol = "no result";
				exit();
			 }else{
				 $lol = "result found";
			 }
			 //return $lol;
			 
			$id = $arr['id'];
			//$this->update_last_sent_id($id);

			$description = $arr['description'];			
			$uid = $arr['user_id'];
			$un = $this->user_name($uid);
			$un = $un['name'];

			$msg = "Hello <b>$un</b> Your $description  And withdrawal id is $id";
			
			$token = 'token';
			$chat_id = '-337238934';
			$url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$msg&parse_mode=html";
			file_get_contents($url);

			$this->update_last_sent_id($id);
		}
	}
	CONST BR = "<br>";
	$obj = new work();
	$arr = $obj->send_notification();
	
?>