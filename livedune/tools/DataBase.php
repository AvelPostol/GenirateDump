<?php

require_once ('tools/Helper.php');

class DataBase {

  private $db;
  private $user;
  private $pass;
  public $mysqli;
  
  public function __construct() {
    $this->Helper = new Helper();
    $db = $this->db = 'syngeta';
    $user = $this->user = 'iroot';
    $pass = $this->user = 'G@&5$@pJe87VxmS2KKSs';
    $this->mysqli = new mysqli("localhost", $user, $pass, $db);
  }

  public function syncDataWithDatabase($p)
    {
        $data = $this->Helper->adaptDB([
            'data' => $p['data'], 
            'table_name' => $p['table_name'],
          ]);

        $to_upds = [];
        $to_adds = [];
        $to_miss = [];

        $table_name = $p['table_name'];
        $key = $p['key'];

        $existing_keys = [];
        
        // Получаем уникальные значения ключевого поля из массива $data 
        $unique_keys = array_unique(array_column($data, $key)); 
        
        // Получаем список всех полей таблицы
        $query = "DESCRIBE $table_name";
        $result = $this->mysqli->query($query);

        $table_fields = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $table_fields[] = $row['Field'];
            }
        } else {
            echo "Ошибка выполнения запроса3: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
            exit();
        }


        // Получаем уникальные значения ключевого поля из таблицы 
        $query = "SELECT * FROM $table_name"; 
        $result = $this->mysqli->query($query); 

        

        if ($result) { 
            $existing_data = [];
            while ($row = $result->fetch_assoc()) { 
                $existing_data[$row[$key]] = $row;
                $existing_keys[] = $row[$key];
                $dates[] = $row;
            } 
        } else { 
            echo "Ошибка выполнения запроса1: (" . $this->mysqli->errno . ") " . $this->mysqli->error; 
            exit(); 
        }


        // Определяем значения для добавления (присутствуют только в $data)
        $to_add_keys = array_diff($unique_keys, $existing_keys);
 
        // Обновление данных 
          foreach ($data as $item) { 
            $key_value = $item[$key]; 
            if (isset($existing_data[$key_value])) { 

                $existing_item = $existing_data[$key_value];
                $need_update = false;

                foreach ($item as $field => $value) { 
                    if ($field !== $key) { 
                        if ($value != $existing_item[$field]) {
                            $need_update = true;
                            break;
                        }
                    } 
                }

                if ($need_update) {
                    $update_fields = []; 
                    foreach ($item as $field => $value) { 
                        if ($field !== $key) { 
                            if (is_numeric($value)) {
                                $update_fields[] = "$field = $value";
                            } else {
                                $update_fields[] = "$field = '$value'";
                            }
                        } 
                    } 

                    $update_query = "UPDATE $table_name SET " . implode(', ', $update_fields) . " WHERE $key = '$key_value'"; 

                    $to_upds[] = $item;
                  // Выполните запрос на обновление в базе данных 
                  $this->mysqli->query($update_query); 
                }
            } 
        }

        
    

        // Добавление новых данных
        foreach ($data as $item) {
            $key_value = $item[$key];
            if (in_array($key_value, $to_add_keys)) {
                $to_adds[] = $item;

                // Выполните операцию добавления, исключив поля из $exclude_fields
                $fields = [];
                $placeholders = [];
                $values = [];
                foreach ($item as $field => $value) {
                        $fields[] = "$field";
                        $placeholders[] = "?";
                        $values[] = $value;
                }

                // Подготовленный запрос с плейсхолдерами (?)
                $add_query = "INSERT INTO $table_name (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";

                // Создание подготовленного выражения
                $stmt = $this->mysqli->prepare($add_query);

                // Привязка параметров к плейсхолдерам
                $stmt->bind_param(str_repeat('s', count($values)), ...$values);

                // Выполнение запроса
               try {
                 $stmt->execute();
                } catch (Exception $e) {
                    var_dump($e);
                    die();
                }

                // Закрытие подготовленного выражения
                $stmt->close();
            }
        }

        return array(
            'to_upds' => $to_upds,
            'to_adds' => $to_adds,
            'to_miss' => $to_miss
        );

        
      
    }

}



/*
public function delete(){
     // Определяем ключи, которые есть в базе данных, но нет во входящем массиве
 $missing_keys = array_diff($existing_keys, $unique_keys);

 foreach($dates as $row) { 
         $key_value = $row[$key];
         if (in_array($key_value, $missing_keys)) {
             $to_miss[] = $row;
         }
 }
     // Удаление данных по отсутствующим ключам
 foreach ($missing_keys as $missing_key) {
         $delete_query = "DELETE FROM $table_name WHERE $key = '$missing_key'";
         // Выполните запрос на удаление из базы данных
        $mysqli->query($delete_query);
 }
}
*/