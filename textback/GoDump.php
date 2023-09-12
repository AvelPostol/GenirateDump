<?php

require_once ('tools/Chats.php');
require_once ('tools/Chanel.php');
require_once ('tools/ChatHistory.php');
require_once ('tools/DataBase.php');
require_once ('tools/Tegs.php');


class ChatManager {
  private $main;

  public function __construct($token) {
      $this->chanel = new Chanel($token);
      $this->chats = new Chats($token);
      $this->ChatHistory = new ChatHistory($token);
      $this->DataBase = new DataBase($token);
      $this->Tags = new Tags($token);

      $db = $this->db = 'syngeta';
      $user = $this->user = 'iroot';
      $pass = $this->user = 'G@&5$@pJe87VxmS2KKSs';
      $this->mysqli = new mysqli("localhost", $user, $pass, $db);
  }

  public function writeLog($data) {
    $logFile = 'wh.txt';
    $formattedData = var_export($data, true);
    file_put_contents($logFile, '<?php $array = ' . $formattedData . ';', FILE_APPEND);
  }

  public function AlterGet() {
    
    $table_name = 's_chats';

    $query = "SELECT tbChatId FROM $table_name"; 
    $result = $this->mysqli->query($query); 
    
    while ($row = $result->fetch_assoc()) { 
        $dates[] = $row;
    }

    return $dates;
  }
  public function getChanel(){
    /*
    * ПОДГОТОВКА КАНАЛОВ
    *
    */
    $chanels = $this->chanel->getChanel();
    $this->DataBase->syncDataWithDatabase([
      'data' => $chanels,
      'table_name' => 's_channels',
      'key' => 'id',
    ]);

    return $chanels;
  }
  public function getChatAndTags($chanels){
    /*
    * ПОДГОТОВКА ДИАЛОГОВ
    *
    */
    $allChats = [];
    $alltags = [];
    $skip = 0;
    $top = 10;

      foreach($chanels as $chanel){
          $data = $this->chats->getChats(['step' => $top, 'dinamStep' => $skip, 'chanel' => $chanel]);
          foreach($data['data'] as $chat){
            $allChats[] = $chat;
            /*
            * ПОДГОТОВКА ТЕГОВ ИЗ ДИАЛОГОВ
            *
            */
            if(isset($chat['tagSet'][0])){
              foreach($chat['tagSet'] as $key => $item){
                $id = $chat['chatId'].$key;
                $alltags[] = [
                  'id'             => $id,
                  'name'           => $item['name'], 
                  'reason'         => $item['reason'],
                  'assignedTs'     => $item['assignedTs'],
                  'chatId'         => $chat['chatId'],
                  'accountId'      => $chat['accountId']
                ];
              }
            }

          }
          $chatsCount = count($data['data']);
        }

    return ['chats' => $allChats, 'tags' => $alltags];

  }
  public function getMessages($chats){
    $skip = 0;
    $ChatHistory = [];
    $top = 50;

    foreach($chats as $chat){
      $data = $this->ChatHistory->getMessagesForChat(['dinamStep' => $skip,'tbChatId' => $chat['tbChatId']]);
      foreach($data['data'] as $chat){
        $ChatHistory[] = $chat;
      }
    }
    return $ChatHistory;
  }

  public function getAllChats() {

    $chanels = self::getChanel();

    $ChatAndTags = self::getChatAndTags($chanels);

    $this->DataBase->syncDataWithDatabase([
      'data' => $ChatAndTags['chats'],
      'table_name' => 's_chats',
      'key' => 'id',
    ]);

    $this->DataBase->syncDataWithDatabase([
      'data' => $ChatAndTags['tags'],
      'table_name' => 's_tags',
      'key' => 'id',
    ]);

    $messages = self::getMessages($ChatAndTags['chats']);
 
    $this->DataBase->syncDataWithDatabase([
      'data' => $messages,
      'table_name' => 's_messages',
      'key' => 'id',
    ]);
  }

  /*public function getWH($p) {
    self::writeLog($p);
  }*/
}

$apiToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzY29wZXMiOlsiYWNjb3VudDphZGRfdXNlcnMiLCJhY2NvdW50OmVkaXRfb3duX3Byb2ZpbGUiLCJhY2NvdW50OmVkaXRfdXNlcnMiLCJhY2NvdW50OnJlbW92ZV91c2VycyIsImFjY291bnQ6dmlld191c2VycyIsImFwaXRva2Vuczppc3N1ZSIsImF0dGFjaG1lbnRzOnVwbG9hZCIsImNoYW5uZWw6Y3JlYXRlIiwiY2hhbm5lbDpnZXRfYnlfYWNjb3VudCIsImNoYXQ6Z2V0IiwiY2hhdDppbml0aWF0ZSIsImNoYXQ6bWFya19yZWFkIiwiY2hhdDptYXJrX3VucmVhZCIsImNoYXQ6cmVwbHkiLCJlbmR1c2VyX25vdGlmaWNhdGlvbnM6Z2V0X2luZm8iLCJlbmR1c2VyX25vdGlmaWNhdGlvbnM6Z2V0X3N1YnNjcmlwdGlvbnMiLCJlbmR1c2VyX25vdGlmaWNhdGlvbnM6bWFuZ2Vfd2lkZ2V0cyIsImVuZHVzZXJfbm90aWZpY2F0aW9uczpyZW1vdmVfc3Vic2NyaXB0aW9ucyIsImVuZHVzZXJfbm90aWZpY2F0aW9uczpzZW5kIiwiaW50ZXJhY3RpdmVfY2hhaW5zOm1hbmFnZSIsImludm9pY2VzOnBheSIsIm1lc3NhZ2VUZW1wbGF0ZTpnZXQiLCJtZXNzYWdlVGVtcGxhdGU6bWFuYWdlIiwibm90aWZpY2F0aW9uX3dlYmhvb2tzOmFkZCIsIm5vdGlmaWNhdGlvbl93ZWJob29rczpyZW1vdmUiLCJyZXBvcnRzOnZpZXciLCJzdWJzY3JpcHRpb25zOmFjdGl2YXRlIiwic3Vic2NyaXB0aW9uczpnZXQiLCJ3aWRnZXQ6Z2V0Iiwid2lkZ2V0Om1vZGlmeSJdLCJhY2NvdW50LmlkIjoiNjcyMjNiYTMtNDdiZS00ZjkyLTljMDctMTc5MzdmZmY1OTliIiwidXNlci5pZCI6IjQzNDQ3YjIzLTFmY2QtNDc0Zi04MDdjLWUxZTcwODM4MDY3YSIsIm5iZiI6MTY5MjcwNDA0OSwianRpIjoiNTRlZmI1NTItNmZkNi04MzU4LTdhN2EtMDE4YTFkMDYwYTNlIiwiaWF0IjoxNjkyNzA0MDQ5LCJleHAiOjE3MjQyNDAwNDksImlzcyI6Imh0dHBzOi8vaWQudGV4dGJhY2suaW8vYXV0aC8iLCJzdWIiOiI0MzQ0N2IyMy0xZmNkLTQ3NGYtODA3Yy1lMWU3MDgzODA2N2EifQ.He6RHJpPQqh3FpqyVMIh5qNTkyZJIJdp-JZCwiTu5xw';
$chatManager = new ChatManager($apiToken);

$allChats = $chatManager->getAllChats();