<?php
require_once ('tools/DataBase.php');

class Chats {
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
      $this->requestUrl = $this->baseUrl . '/v1/chats';
      $this->top = 1;
      $this->ch = curl_init();
  }

  public function writeLog($data) {
    $logFile = 'log-chats.txt';
    $formattedData = var_export($data, true);
    file_put_contents($logFile, '<?php $array = ' . $formattedData . ';', FILE_APPEND);
  }

  

  
  public function getChats($p) {

    $top = $p['step'];
    $chunkSize = 1000;
    $skip = $p['dinamStep'];
    $chanel = $p['chanel'];
    $step = 25;

    

    $continue = true;
    $accumulatedChats = array();

    while ($continue) {

      $query_params = array(
        'from' => 0, // Замените на нужную временную метку в миллисекундах
        '$top' => 10, // Используем переданный размер чанка
        '$skip' => $skip,
        'channelId' => $chanel['id']
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

        $cou = count($response['$items']);

        if (isset($response['$items']) && !empty($response['$items'])) {
          foreach ($response['$items'] as $chat) {
            // Извлекаем messagingStats из исходного массива

            $messagingStats = $chat['messagingStats'];

            if(isset($chat['remoteContact']['nationalName']['firstName'])){
              $chat['firstName'] = $chat['remoteContact']['nationalName']['firstName'];
            }
            
            if(isset($chat['remoteContact']['nationalName']['lastName'])){
              $chat['lastName'] = $chat['remoteContact']['nationalName']['lastName'];
            }

            $chat = array_merge($chat, $messagingStats);
            
            $accumulatedChats[] = $chat;
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

    curl_close($this->ch);
    return ['data' => $accumulatedChats, 'dinamStep' => $skip];
  }
}


/*
 10 => 
  array (
    'id' => 'tg_14508_1006822772',
    'accountId' => '67223ba3-47be-4f92-9c07-17937fff599b',
    'chatId' => '1006822772',
    'channel' => 'tg',
    'channelId' => 14508,
    'remoteAddress' => '1006822772',
    'remoteAddresses' => 
    array (
      0 => '1006822772',
    ),
    'address' => 
    array (
      'accountId' => '67223ba3-47be-4f92-9c07-17937fff599b',
      'channel' => 'tg',
      'channelId' => 14508,
      'remoteAddress' => '1006822772',
      'chatId' => '1006822772',
      'tbChatId' => 67544009,
    ),
    'messagingStats' => 
    array (
      'total' => 23,
      'received' => 14,
      'sent' => 9,
      'delivered' => 9,
      'unread' => 0,
      'totalNotificationsSent' => 0,
      'lastInboundMessageTimestamp' => '2023-06-04T17:09:13Z',
      'receivedUserSentMessages' => 13,
    ),
    'lastMessage' => 
    array (
      'reads' => 
      array (
      ),
      'markRead' => false,
      'deliveryStatuses' => 
      array (
      ),
      'textMarkup' => 'PLAIN',
      'compositeMessageType' => 'REGULAR_MESSAGE',
      'pushType' => 'REGULAR',
      'reason' => 'operator_sent_message',
      'id' => '86fa77b8-9e76-275b-6571-018887713d8d',
      'direction' => 'outbound',
      'headers' => 
      array (
      ),
      'text' => 'https://www.syngenta.ru/lawn-and-garden там есть подробная информация по всем препаратам, которые зарегистрированы для применения в ЛПХ, ссылки на контакты всех официальных дистрибьюторов по прода...',
      'attachments' => 
      array (
      ),
      'buttons' => 
      array (
      ),
      'actionNumbers' => 
      array (
      ),
      'sentTimestamp' => 1685899525517,
      'readByAnyOperator' => false,
    ),
    'metadata' => 
    array (
      'phones' => 
      array (
      ),
      'emails' => 
      array (
      ),
      'changedTs' => '2023-06-04T17:09:13.300Z',
    ),
    'remoteContact' => 
    array (
      'nationalName' => 
      array (
        'firstName' => 'Оксана',
        'displayName' => 'Оксана ',
      ),
      'channelUserId' => '1006822772',
      'channelUsername' => 'OksanaKostarnova',
      'userPicUrl' => 'https://tbclientfilesprod.blob.core.windows.net/67223ba3-47be-4f92-9c07-17937fff599b-received/2023-06-04T13:40:31.710-AgACAgIAAxUAAWR8lEA4oz7yUguYZ2ntB1Rd8fxBAAKqpzEbdOUCPCfAfrgxrdTvAQADAgADYQADLwQ.jpg?sig=msmpmN9qD9vjPu1xoOCkjnKcLrxD4zzXmRcUQNNvAjM%3D&se=2033-06-03T23%3A52%3A32Z&sv=2019-02-02&sp=r&sr=b',
    ),
    'unreadCount' => 0,
    'tbChatId' => 67544009,
    'tagSet' => 
    array (
      0 => 
      array (
        'name' => 'Инсектициды',
        'reason' => 'operator_assigned',
        'assignedTs' => '2023-06-04T16:54:37.215Z',
      ),
      1 => 
      array (
        'name' => 'Контрафакт',
        'reason' => 'operator_assigned',
        'assignedTs' => '2023-06-04T16:54:40.790Z',
      ),
      2 => 
      array (
        'name' => 'Вторая линия',
        'reason' => 'operator_assigned',
        'assignedTs' => '2023-06-04T16:54:45.410Z',
      ),
      3 => 
      array (
        'name' => 'мелкая фасовка СЗР',
        'reason' => 'operator_assigned',
        'assignedTs' => '2023-06-04T16:54:19.964Z',
      ),
      4 => 
      array (
        'name' => 'Сайт',
        'reason' => 'user_subscribed_on_widget',
        'assignedTs' => '2023-06-04T13:44:54.238Z',
      ),
      5 => 
      array (
        'name' => 'ЛПХ',
        'reason' => 'operator_assigned',
        'assignedTs' => '2023-06-04T16:54:25.842Z',
      ),
      6 => 
      array (
        'name' => 'Астраханская область',
        'reason' => 'operator_assigned',
        'assignedTs' => '2023-06-04T16:54:32.287Z',
      ),
    ),
    'createdTs' => '2023-06-04T13:44:47.959Z',
    'importedChat' => false,
    'importedChatMerged' => false,
    'allMessagesImported' => false,
    'blocked' => false,
    'remoteAddressNotTel' => '1006822772',
    'firstName' => 'Оксана',
    'total' => 23,
    'received' => 14,
    'sent' => 9,
    'delivered' => 9,
    'unread' => 0,
    'totalNotificationsSent' => 0,
    'lastInboundMessageTimestamp' => '2023-06-04T17:09:13Z',
    'receivedUserSentMessages' => 13,
  ),
*/