<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Utilisateur.php';

class AuthController {
    private $pdo;

    public function __construct($pdo = null) {
        try {
            $this->pdo = $pdo ?: config::getConnexion();
            if ($this->pdo === null) {
                throw new Exception("La connexion à la base de données a échoué");
            }
        } catch (Exception $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            throw $e;
        }
    }
    public function handleLogin() {
        session_start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['motDePasse'] ?? '';
    
            try {
                $user = $this->authentifier($email, $password);
                
                if ($user) {
                    // Check if account is active
                    if ($user['Statut'] !== 'actif') {
                        $_SESSION['error'] = "Votre compte est inactif. Contactez l'administrateur.";
                        header('Location: /prjweb2025/views/front/login2.php');
                        exit();
                    }
                    
                    $_SESSION['user'] = $user;
                    
                    // Role-based redirection
                    switch ($user['Role']) {
                        case 'admin':
                            header('Location: /prjweb2025/views/back/dashboard.php');
                            break;
                        case 'chauffeur':
                            header('Location: /prjweb2025/views/front/driver_dashboard.php');
                            break;
                        case 'client':
                        default:
                            header('Location: /prjweb2025/views/front/main.php');
                    }
                    exit();
                } else {
                    $_SESSION['error'] = "Email ou mot de passe incorrect";
                    header('Location: /prjweb2025/views/front/login2.php');
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Une erreur est survenue lors de la connexion";
                error_log("Login error: " . $e->getMessage());
                header('Location: /prjweb2025/views/front/login2.php');
                exit();
            }
        }
    }
    
    public function authentifier($email, $password) {
        try {
            $query = $this->pdo->prepare('SELECT * FROM utilisateur WHERE Email = :email');
            $query->execute([':email' => $email]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
            
            // Plain text comparison
            if ($user && $password === $user['Mot_de_passe']) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function checkEmailExists($email) {
        try {
            $query = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE Email = :email');
            $query->execute([':email' => $email]);
            echo json_encode(['exists' => $query->fetchColumn() > 0]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

// Handle actions
if (isset($_GET['action'])) {
    $controller = new AuthController();
    switch ($_GET['action']) {
        case 'login':
            $controller->handleLogin();
            break;
        case 'checkEmail':
            if (isset($_GET['email'])) {
                $controller->checkEmailExists($_GET['email']);
            }
            break;
    }
}