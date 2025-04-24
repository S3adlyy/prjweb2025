<?php
require_once __DIR__ . '/../config/config.php';

class Utilisateur
{
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $motDePasse;
    private $role;
    private $statut;
    private $dateInscription;
    private $photoUrl;

    public function __construct(
        $id = null,
        $nom = null,
        $prenom = null,
        $email = null,
        $motDePasse = null,
        $role = null,
        $statut = null,
        $dateInscription = null,
        $photoUrl = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->statut = $statut;
        $this->dateInscription = $dateInscription;
        $this->photoUrl = $photoUrl;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getMotDePasse() {
        return $this->motDePasse;
    }
    public function getRole() { return $this->role; }
    public function getStatut() { return $this->statut; }
    public function getDateInscription() { return $this->dateInscription; }
    public function getPhotoUrl() { return $this->photoUrl; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setPrenom($prenom) { $this->prenom = $prenom; return $this; }
    public function setEmail($email) { $this->email = $email; return $this; }
    public function setMotDePasse($motDePasse) { $this->motDePasse = $motDePasse; return $this; }
    public function setRole($role) { $this->role = $role; return $this; }
    public function setStatut($statut) { $this->statut = $statut; return $this; }
    public function setPhotoUrl($photoUrl) { $this->photoUrl = $photoUrl; return $this; }

    /**
     * Hydrate user object from array
     */
    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Convert user object to array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role,
            'statut' => $this->statut,
            'dateInscription' => $this->dateInscription,
            'photoUrl' => $this->photoUrl
        ];
    }
}
?>