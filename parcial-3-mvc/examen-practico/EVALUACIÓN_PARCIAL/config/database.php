<?php
// Cristhian Alexis ortiz Valentin 3-3

    class database {
        private $host = "localhost";
        private $db = "proyecto";
        private $username = "root";
        private $password = "";

        public function __construct()
        {
            
        }

        public function connect(){
            try {
                $PDO=new PDO("mysql:host=$this->host;dbname=$this->db",$this->username,
                $this->password);
                return $PDO;
            }
            catch(PDOException $e) {
                return $e->getMessage();
            }
        }
    }

?>    