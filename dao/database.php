<?php
    include ('config.php');
    
    class dataConex {
        public function conexion() {
            try {
                $PDO = new PDO("mysql:host=".SERVER.";dbname=".DATABASE,USER,PASSWORD);
                return $PDO;
            } catch (PDOException $ex) {
                return $ex->getMessage();
            }
        }
    }
?>