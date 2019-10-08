<?php

namespace Model;

 class DBconn {
     private $conn;

     public function __construct()
     {
        //require(__DIR__ . '/../../dbproduction.php');
        require(__DIR__ . '/../../dbsettings.php');
        $this->conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

        if(!$this->conn) {
        die('Could not connect to db: ' . mysqli_error());
        }  
     }

     private function bindQueryParams($stmt, string $types, array $paramValues) {
        $paramValuesRef[] =& $types;

        for($i = 0; $i < count($paramValues); $i++) {
            $paramValuesRef[] =& $paramValues[$i];
        }

        call_user_func_array(array($stmt,'bind_param'), $paramValuesRef);
     }
     

     public function getFromDB($sql, string $types, array $paramValues) {
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong: '. mysqli_error());
            exit();
        }     
        $this->bindQueryParams($stmt, $types, $paramValues);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userData = mysqli_fetch_assoc($result);
        return $userData;
    }

    public function saveToDB($sql, string $types, array $paramValues) {
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong: '. mysqli_error());
            exit();
        } 

        $this->bindQueryParams($stmt, $types, $paramValues);
        mysqli_stmt_execute($stmt);
    }

    public function updateDB($sql, string $types, array $paramValues) {
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong: '. mysqli_error());
            exit();
        }

        $this->bindQueryParams($stmt, $types, $paramValues);
        mysqli_stmt_execute($stmt);  
    }
     
 }

