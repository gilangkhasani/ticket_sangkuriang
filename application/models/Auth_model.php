<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model
{
	public function login($data, $table_name){
		$this->db->where('username', $data['username']);
		$this->db->where('password', md5($data['password']));
		$query = $this->db->get($table_name);
		return $query->row();
	}

   public function logout($data, $table_name){
		$this->db->where('token', $data['token']);
		$result = $this->db->update($table_name, $data);
		if($result) {
		   return TRUE;
		} else {
		   return FALSE;
		}
	}
}