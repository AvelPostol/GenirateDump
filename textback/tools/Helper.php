<?php
class Helper {

    
    public function adaptDB($p){

        $dataBaseInstance = new DataBase();
        $mysqli = $dataBaseInstance->mysqli;
        
        $tableName = $p['table_name'];
        $datas = $p['data'];
        
        // Получаем список столбцов и их типов из базы данных
        $columns = $this->getTableColumns($tableName, $mysqli);
        
        foreach($datas as $datakey => $data){
            foreach ($data as $key => $value) {
                if (array_key_exists($key, $columns)) {
                    $columnType = $columns[$key];
                    $convertedValue = $this->convertValue($value, $columnType);
                    $datas[$datakey][$key] = $convertedValue;
                }
                else{
                    unset($datas[$datakey][$key]);
                }
            }
        }

        
        
        return $datas;
    }

    // Получаем список столбцов и их типов из базы данных
    private function getTableColumns($tableName, $mysqli) {
        $columns = array();
        $query = "DESCRIBE $tableName";
        $result = $mysqli->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
        } else {
            echo "Ошибка выполнения запроса: " . $mysqli->error;
            exit();
        }
        return $columns;
    }


    // Преобразование значения в соответствии с типом столбца
    private function convertValue($value, $columnType) {
        if (strpos($columnType, 'timestamp') !== false) {
            // Преобразование timestamp
            $dateTime = new DateTime($value);
            return $dateTime->format('Y-m-d H:i:s');
        } elseif (strpos($columnType, 'int') !== false) {
            // Преобразование к целому числу
            return (int)$value;
        } elseif (strpos($columnType, 'float') !== false) {
            // Преобразование к числу с плавающей точкой
            return (float)$value;
        }
        // Возвращаем значение без преобразования для других типов
        return $value;
    }

    // Проверка наличия столбца в таблице
    private function columnExistsInTable($tableName, $columnName, $mysqli) {
        $query = "SHOW COLUMNS FROM $tableName LIKE '$columnName'";
        $result = $mysqli->query($query);
        return $result && $result->num_rows > 0;
    }
}


