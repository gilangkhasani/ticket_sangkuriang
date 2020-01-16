<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Master_model extends CI_Model
	{
		public function read($table_name, $search, $order_by, $limit){
			$new_condition = "";
			if($search != NULL){
				$condition = "WHERE ";
				foreach ($search as $key => $res) {
					if($key == 'between'){
						$condition .= $search[$key]['field']." BETWEEN '".$search[$key]['start_date']."' AND '".$search[$key]['end_date']."' AND ";
					} else {
						$condition .= $key." = '".$res."' AND ";
					}
					
				}
				$new_condition = substr($condition, 0, -4);
			}
			
			$text = "select * from ".$table_name." $new_condition ".$order_by." ".$limit;
			$query = $this->db->query($text);
			return $query->result_array();
		}

		public function insert($data, $table_name){
			$result = $this->db->insert($table_name,$data);
			if($result) {
				return $result;
			} else {
			   return $result;
			}
		}

		public function update($data, $table_name, $data_where){
			
			foreach ($data_where as $row ) {
				foreach($row as $key => $val) {
					$this->db->where($key, $val);
				}
			}
			$result = $this->db->update($table_name, $data);
			if($result) {
			   return TRUE;
			} else {
			   return FALSE;
			}
		}

		public function ticketUpdate($data, $table_name, $id, $id_value){
			$this->db->where($id, $id_value);
			$result = $this->db->update($table_name, $data);
			if($result) {
			   return TRUE;
			} else {
			   return FALSE;
			}
		}
	   
		public function delete($table_name, $data_where){
			foreach ($data_where as $row ) {
				foreach($row as $key => $val) {
					$this->db->where($key, $val);
				}
			}
			$result = $this->db->delete($table_name);
			
			if($result) {
			   return TRUE;
			} else {
			   return FALSE;
			}
		}
		
		public function generateNumber($table_name){
			$query = $this->db->query("
				SELECT MAX(id) AS id FROM (SELECT SUBSTR(no_ticket,2) + 1 as id
				FROM $table_name
				ORDER BY created_date ASC) a
			");
			
			return $query->row_array();
		}
	   
	}