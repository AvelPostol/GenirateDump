<?php

class UserProjects {

  private $CurlManager;

  public function __construct($token) {
    $this->token = $token;
  }

  public function get() {

    $this->CurlManager = new CurlManager($this->token, $metod);
    $response = $this->CurlManager->Get(['CheckContentName' => 'projects', 'ContentPars' => true]);
    return $response;
  }
}
/*
'from' => $this->from,'to' => $this->to,
$this->baseUrl = 'https://api.livedune.com';
$this->requestUrl = $this->baseUrl . '/projects';

$metod = '/api-orders-exchange-public/orders/' . $p['orderId'] . '/file';
$chatManager = new ChatManager($metod);*/