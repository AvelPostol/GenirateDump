<?php
namespace SyngetaLiveDune;

error_reporting(E_ERROR);
ini_set('display_errors', 1);

require_once ('head.php');

class GoDump {

  private $main;
  private $config;

  public function __construct() {

    $this->Config = new \Config();

    $this->UserProjects = new \UserProjects($this->Config->token());
    /*$this->UserAccounts = new \UserAccounts();
    $this->ListOfAllPostsForAnAccount = new \ListOfAllPostsForAnAccount();
    $this->AccountFollowerHistory = new \AccountFollowerHistory();

    $this->StoriesForYourAccount = new \StoriesForYourAccount();
    $this->GettingStatisticsForPost = new \GettingStatisticsForPost();

    
    $this->DataBase = new \DataBase($this->Config->mysql());*/
  }

  public function ListOfUserProjects() {
    $UserProj = $this->UserProjects->get();
  }
/*
  public function ListOfUserAccounts() {
    $UserAcc = $this->UserAccounts->get();
  }
  
  public function ListOfAllPostsForAnAccount() {
    $ListAllPostsAcc = $this->ListOfAllPostsForAnAccount->get();
  }

  public function AccountFollowerHistory() {
    $AccFollHist = $this->AccountFollowerHistory->get();
  }

  public function StoriesForYourAccount() {
    $StorAcc = $this->StoriesForYourAccount->get();
  }

  public function GettingStatisticsForPost() {
    $StatisPost = $this->GettingStatisticsForPost->get();
  }*/

  public function Main() {

    $this->ListOfUserProjects();

  }

}

$chatManager = new GoDump();
$chatManager->Main();





