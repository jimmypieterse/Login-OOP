<?php
    // Functie: classdefinitie User 
    // Auteur: jimmy piet
 
    class User{
        //dbconnect functie
                public function dbConnect(): PDO {
            $db_connection = new PDO(
                "mysql:host=" . DATABASE_HOST . ";dbname=" . DATABASE_NAME . ";charset=utf8mb4",
                DATABASE_USERNAME,
                DATABASE_PASSWORD
            );
            $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db_connection;
        }
        // Eigenschappen 
        public string $username = "";
        public string $email = "";
        private string $password = "";
        
        function setPassword($password){
            $this->password = $password;
        }
        function getPassword(){
            return $this->password;
        }

        public function showUser() {
            echo "<br>Username: $this->username<br>";
            echo "<br>Password: $this->password<br>";
            echo "<br>Email: $this->email<br>";
            
        }

        public function registerUser() : array {
            $errors = $this->validateUser();

            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email address";
            }

            if ($errors) {
                return $errors;
            }

            try {
                $db_connection = $this->dbConnect();
                $check_user = $db_connection->prepare(
                    "SELECT gebruiker_id FROM gebruiker WHERE gebruikersnaam = :username OR email = :email"
                );
                $check_user->execute([
                    "username" => $this->username,
                    "email" => $this->email,
                ]);

                if ($check_user->fetch(PDO::FETCH_ASSOC)) {
                    return ["Username or email already exists."];
                }

                $insert_user = $db_connection->prepare(
                    "INSERT INTO gebruiker (gebruikersnaam, email, wachtwoord) VALUES (:username, :email, :password)"
                );
                $insert_user->execute([
                    "username" => $this->username,
                    "email" => $this->email,
                    "password" => password_hash($this->password, PASSWORD_DEFAULT),
                ]);
            } catch (PDOException $exception) {
                return ["Registration failed because of a database error."];
            }

            return [];
        }

        function validateUser(){
            $errors=[];

            if (empty($this->username)){
                array_push($errors, "Invalid username");
            } else if (strlen($this->username) < 3 || strlen($this->username) > 50){
                array_push($errors, "Username must be between 3 and 50 characters");
            } else if (empty($this->password)){
                array_push($errors, "Invalid password");
            }

            // Test username > 3 tekens
            
            return $errors;
        }

        public function loginUser(): bool {
            try {
                $db_connection = $this->dbConnect();
                $query = $db_connection->prepare(
                    "SELECT gebruiker_id, gebruikersnaam, email, wachtwoord FROM gebruiker WHERE gebruikersnaam = :username"
                );
                $query->execute(["username" => $this->username]);
                $user = $query->fetch(PDO::FETCH_ASSOC);

                if (!$user || !password_verify($this->password, $user["wachtwoord"])) {
                    return false;
                }

                $login = $db_connection->prepare(
                    "INSERT INTO `login` (gebruiker_id) VALUES (:gebruiker_id)"
                );
                $login->execute(["gebruiker_id" => $user["gebruiker_id"]]);
            } catch (PDOException $exception) {
                return false;
            }

            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            session_regenerate_id(true);
            $_SESSION["gebruiker_id"] = (int) $user["gebruiker_id"];
            $_SESSION["username"] = $user["gebruikersnaam"];
            $_SESSION["email"] = $user["email"];
            $this->username = $user["gebruikersnaam"];
            $this->email = $user["email"];

            return true;
        }

        // Check if the user is already logged in
        public function isLoggedin(): bool {
            // Check if user session has been set
            
            return false;
        }

        public function getUser(string $username): bool {
            // Connect database

		    // Doe SELECT * from user WHERE username = $username

            if (false){
                //Indien gevonden eigenschappen vullen met waarden uit de SELECT
                $this->username = 'Waarde uit de database';
                return true;
            } else {
                return false;
            }   
        }

        public function logout(){
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $_SESSION = [];
         if (ini_get("session.use_cookies")) {
                $cookie_params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    "",
                    time() - 42000,
                    $cookie_params["path"],
                    $cookie_params["domain"],
                    $cookie_params["secure"],
                    $cookie_params["httponly"]
                );
            }

            session_destroy();

        }
    }

?>