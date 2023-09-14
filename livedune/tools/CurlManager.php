<?php

class CurlManager {
  private $token;
  private $authHeader;
  private $baseUrl;
  private $requestUrl;
  private $top;
  private $ch;

  public function __construct($apiKey, $metod) {
    $this->apiKey = $apiKey;
    $this->authHeader = 'apiKey: ' . $this->apiKey;
    $this->baseUrl = 'https://api.livedune.com';
    $this->requestUrl = $this->baseUrl . $metod;
    $this->top = 100;
    $this->ch = curl_init();
  } 

  public function CreateQueryOrders($p,$ProcesParam) {

    /*if($p['CheckContentName'] == 'orders'){
      $queryParams = http_build_query([
        'pageIndex' => $ProcesParam['pageIndex'],
        'pageSize' => $ProcesParam['pageSize'],
        'from' => $p['from'],
        'to' => $p['to'],
      ]);
    }

    if($p['CheckContentName'] == 'order'){
      $queryParams = http_build_query([]);
    }

    if($p['CheckContentName'] == 'file'){
      $queryParams = http_build_query([
        'filename' => $p['filename']
      ]);
    }
    */

    $queryParams = http_build_query([]);
   
    return $queryParams;
  }

  public function Get($p) {
    
    $continue = true;
    $accumulatedMessages = array();

    $ProcesParam['pageIndex'] = 1;
    $ProcesParam['pageSize'] = 100;

    while ($continue) {

      // Создание cURL-запроса
      curl_setopt($this->ch, CURLOPT_URL, $this->requestUrl . '?' . $this->CreateQueryOrders($p, $ProcesParam));
      curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
          $this->authHeader
      ]);

      $jsonResponse = curl_exec($this->ch);
      $response = json_decode($jsonResponse, true);
      print_r($response);
      die();

      if ($jsonResponse === false) {
        echo 'cURL Error: ' . curl_error($this->ch);
        $continue = false; // Останавливаем цикл в случае ошибки
      } else {

      if($p['ContentPars']){
        if (isset($response[$p['CheckContentName']]) && !empty($response[$p['CheckContentName']])) {
          foreach ($response[$p['CheckContentName']] as $message) {
            if(isset($message)){
              $accumulated[] = $message;
            }
          }
        }
        else{
          $continue = false;
        }
      }
      else{
        if($p['CheckContentName'] == '***'){
          $accumulated = $jsonResponse;
          $continue = false;
        }
        else{
          $accumulated = $response;
          $continue = false;
        }  
      }
    
      $ProcesParam['pageIndex']++;

      }
    }

    curl_close($this->ch);
    return ['data' => $accumulated];
  }
}
