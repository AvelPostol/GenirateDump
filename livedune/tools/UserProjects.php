<?php

class UserProjects {

  private $CurlManager;

  public function __construct($metod) {

    $this->Config = new \Config();
    $this->CurlManager = new CurlManager($this->Config->token(), $metod);

  }

  public function get() {
    $metod = '/projects';

    $this->CurlManager = new UserProjects($metod);

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