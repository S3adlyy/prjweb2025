<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Utilisateur.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
class UtilisateurController {
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

    // CRUD Operations
    public function ajouterUtilisateur(Utilisateur $utilisateur) {
        try {
            if ($this->emailExists($utilisateur->getEmail())) {
                throw new Exception("Cet email est déjà utilisé");
            }
    
            $query = $this->pdo->prepare(
                "INSERT INTO utilisateur (
                    Nom, 
                    Prenom, 
                    Email, 
                    Mot_de_passe, 
                    Role, 
                    Date_inscription, 
                    Statut, 
                    Photo_URL
                ) VALUES (
                    :nom, 
                    :prenom, 
                    :email, 
                    :mdp, 
                    :role, 
                    :date_inscription, 
                    :statut, 
                    :photo
                )"
            );
            
            $success = $query->execute([
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'email' => $utilisateur->getEmail(),
                'mdp' => $utilisateur->getMotDePasse(), // Storing plain text password
                'role' => $utilisateur->getRole() ?? 'client',
                'date_inscription' => $utilisateur->getDateInscription() ?? date('Y-m-d'),
                'statut' => $utilisateur->getStatut() ?? 'actif',
                'photo' => $utilisateur->getPhotoUrl() ?? ''
            ]);
            
            if (!$success) {
                throw new Exception("Échec de l'ajout de l'utilisateur");
            }
            
            $utilisateur->setId($this->pdo->lastInsertId());
            return $utilisateur;
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            throw new Exception("Erreur lors de l'ajout de l'utilisateur: " . $e->getMessage());
        }
    }

    public function getUtilisateur($id) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM utilisateur WHERE ID_Utilisateur = :id");
            $query->execute([':id' => $id]);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                return null;
            }
            
            return (new Utilisateur())->hydrate($data);
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            return null;
        }
    }

    public function getAllUtilisateurs() {
        try {
            $query = $this->pdo->query("SELECT * FROM utilisateur");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            return false;
        }
    }

    public function updateUtilisateur($utilisateur) {
        try {
            $query = $this->pdo->prepare(
                "UPDATE utilisateur SET 
                Nom = :nom, 
                Prenom = :prenom, 
                Email = :email, 
                Role = :role, 
                Statut = :statut,
                Photo_URL = :photo
                WHERE ID_Utilisateur = :id"
            );
            
            return $query->execute([
                'id' => $utilisateur->getId(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'email' => $utilisateur->getEmail(),
                'role' => $utilisateur->getRole(),
                'statut' => $utilisateur->getStatut(),
                'photo' => $utilisateur->getPhotoUrl()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            throw new Exception("Erreur lors de la mise à jour");
        }
    }

    public function deleteUtilisateur($id) {
        try {
            $query = $this->pdo->prepare("DELETE FROM utilisateur WHERE ID_Utilisateur = :id");
            $query->execute([':id' => $id]);
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            throw new Exception("Erreur lors de la suppression");
        }
    }

    private function emailExists($email) {
        try {
            $query = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE Email = :email");
            $query->execute([':email' => $email]);
            return $query->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            return false;
        }
    }
}
?>