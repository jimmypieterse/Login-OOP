<?php
// Functie: config.php
// Auteur: jimmy piet

define(DATABASE, "login_systeem");
define(SERVERNAME, "localhost");
define(USERNAME, "root");
define(PASSWORD, "");

        public function dbConnect(): PDO {
            $db_connection = new PDO(
                "mysql:host=" . DATABASE_HOST . ";dbname=" . DATABASE_NAME . ";charset=utf8mb4",
                DATABASE_USERNAME,
                DATABASE_PASSWORD
            );
            $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db_connection;
        }
?>