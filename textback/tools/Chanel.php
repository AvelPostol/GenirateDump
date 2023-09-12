<?php

class Chanel {
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
      $this->requestUrl = $this->baseUrl . '/v1/channels/light';
      $this->top = 1;
      $this->ch = curl_init();
  }

  public function getChanel() {
    $accumulatedMessages = array();

    $full_request_url = $this->requestUrl;

    curl_setopt($this->ch, CURLOPT_URL, $full_request_url);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array($this->authHeader));

    $jsonResponse = curl_exec($this->ch);


    if ($jsonResponse === false) {
      echo 'cURL Error: ' . curl_error($this->ch);
      $continue = false; // Останавливаем цикл в случае ошибки
    } else {
      $response = json_decode($jsonResponse, true);
      if (isset($response['$value']) && !empty($response['$value'])) {
        foreach ($response['$value'] as $message) {
          $accumulatedMessages[] = $message;
        }
      }
    }

    curl_close($this->ch);
    return $accumulatedMessages;
  }
}

