<?php
namespace SyngetaLiveDune;

error_reporting(E_ERROR);
ini_set('display_errors', 1);

require_once ('head.php');

class GoDump {

  private $main;
  private $config;

  public function __construct() {

      $this->UserProjects = new \UserProjects($token);
      $this->UserAccounts = new \UserAccounts($token);
      $this->ListOfAllPostsForAnAccount = new \ListOfAllPostsForAnAccount($token);
      $this->AccountFollowerHistory = new \AccountFollowerHistory($token);

      $this->StoriesForYourAccount = new \StoriesForYourAccount($token);
      $this->GettingStatisticsForPost = new \GettingStatisticsForPost($token);

      $this->Config = new \Config();
      $this->DataBase = new \DataBase($this->Config->mysql());
  }

  public function ListOfUserProjects() {
    $UserProj = $this->UserProjects->get();
  }

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
  }

  public function Main() {

    $this->ListOfUserProjects();

  }

}

$chatManager = new GoDump();

$allChats = $chatManager->Main();





