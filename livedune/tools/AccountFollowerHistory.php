<?php

require_once ('tools/DataBase.php');

class ChatHistory {
  private $token;
  private $authHeader;
  private $baseUrl;
  private $requestUrl;
  private $top;
  private $ch;

  public function __construct($token) {
      $this->token = $token;
      $this->authHeader = 'Authorization: Bearer ' . $this->token;
      $this->baseUrl = 'https://api.textback.io/api';
      $this->requestUrl = $this->baseUrl . '/v1/chatHistory/queries/lastDays';
      $this->top = 50;
      $this->ch = curl_init();
  }

  public function writeLog($data) {
    $logFile = 'log-chathistory.txt';
    $formattedData = var_export($data, true);
    file_put_contents($logFile, '<?php $array = ' . $formattedData . ';', FILE_APPEND);
  }

  public function getMessagesForChat($p) {
    
    $continue = true;
    $accumulatedMessages = array();
    $top = 50;
    $skip = $p['dinamStep'];

    while ($continue) {

      $query_params = array(
        '$top' => 50, // Используем переданный размер чанка
        'days' => 10000,
        '$skip' => $skip,
        'tbChatId' => $p['tbChatId'],
      );

      $full_request_url = $this->requestUrl . '?' . http_build_query($query_params);
      curl_setopt($this->ch, CURLOPT_URL, $full_request_url);
      curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->ch, CURLOPT_HTTPHEADER, array($this->authHeader));

      $jsonResponse = curl_exec($this->ch);
      if ($jsonResponse === false) {
        echo 'cURL Error: ' . curl_error($this->ch);
        $continue = false; // Останавливаем цикл в случае ошибки
      } else {
        $response = json_decode($jsonResponse, true);
        if (isset($response['$items']) && !empty($response['$items'])) {
          foreach ($response['$items'] as $message) {
            if(isset($message)){
              $accumulatedMessages[] = $message;
            }
          }
        }

        if (isset($response['$nextPage'])) {
          $nextPage = $response['$nextPage'];
          $skip += $top; // Увеличиваем смещение на размер чанка
        } else {
          $continue = false;
        }
     
    
        
      }

 
    }
    //self::writeLog($accumulatedMessages);
    curl_close($this->ch);
    return ['data' => $accumulatedMessages, 'dinamStep' => $skip];
  }
}
