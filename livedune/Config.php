<?php

class Config {

  private $main;
  private $config;

  public function __construct() {

      $token = $this->config = 'aa631ef45782f5b0.85372829';
      
     /* $db = $this->db = '*****';
      $user = $this->user = 'iroot';
      $pass = $this->user = 'G@&5$@pJe87VxmS2KKSs';
      $mysql = $this->mysqli = new mysqli("localhost", $user, $pass, $db);

      $this->DataBase = new \DataBase($mysql);*/
  }
  /*
  public function mysql() {
    return $this->DataBase;
  }*/

  public function token() {
    return $this->$token;
  }

  public function ContructFunc($type) {
    return $data;
  }

  

}

?>