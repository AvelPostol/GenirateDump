<?php
class Tags {
  private $token;
  private $authHeader;
  private $baseUrl;
  private $top;
  private $ch;

  public function __construct($token) {
    $this->token = $token;
    $this->authHeader = 'Authorization: Bearer ' . $this->token;
    $this->baseUrl = 'https://api.textback.io/api';
    $this->top = 1;
    $this->ch = curl_init();
  }

  public function writeLog($data) {
    $logFile = 'log-tags.txt';
    $formattedData = var_export($data, true);
    file_put_contents($logFile, '<?php $array = ' . $formattedData . ';', FILE_APPEND);
  }

  public function getTags($tbChatId) {
    $accumulatedMessages = array();

    $requestUrl = $this->baseUrl . '/chats/' . $tbChatId . '/tags';

    curl_setopt($this->ch, CURLOPT_URL, $requestUrl);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array($this->authHeader));

    $jsonResponse = curl_exec($this->ch);
    $response = json_decode($jsonResponse, true);

    if ($jsonResponse === false) {
      echo 'cURL Error: ' . curl_error($this->ch);
      $continue = false; // Останавливаем цикл в случае ошибки
    } else {
      if (isset($response['$items']) && !empty($response['$items'])) {
        foreach ($response['$items'] as $message) {
          $accumulatedMessages[] = $message;
        }
      }
    }

    curl_close($this->ch);
    return $accumulatedMessages;
  }
}