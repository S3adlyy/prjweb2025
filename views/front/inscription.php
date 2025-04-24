<?php 
session_start();
define('BASE_URL', '/prjweb2025/'); 
require_once __DIR__ . '/../../controllers/UtilisateurController.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Utilisateur.php';

// Traitement du formulaire
// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  try {
      // Validation des données
      $errors = [];
      // Nettoyage des entrées
      $nom = htmlspecialchars(trim($_POST['nom']));
      $prenom = htmlspecialchars(trim($_POST['prenom']));
      $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
      $password = $_POST['password']; // Keep as plain text
      $confirmPassword = $_POST['confirmPassword'];
      $role = htmlspecialchars($_POST['role']);
      $statut = htmlspecialchars($_POST['statut']);
      $dateInscription = date('Y-m-d H:i:s');
      $photoUrl = !empty($_POST['photoUrl']) ? filter_var(trim($_POST['photoUrl']), FILTER_SANITIZE_URL) : null;

      // Validation avancée
      if (empty($nom) || strlen($nom) < 2) {
          $errors[] = "Le nom doit contenir au moins 2 caractères";
      }
      
      if (empty($prenom) || strlen($prenom) < 2) {
          $errors[] = "Le prénom doit contenir au moins 2 caractères";
      }
      
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $errors[] = "L'email n'est pas valide";
      }
      
      if (strlen($password) < 8) {
          $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
      } elseif ($password !== $confirmPassword) {
          $errors[] = "Les mots de passe ne correspondent pas";
      }
      // REMOVED password_hash() - password remains plain text
      
      if (empty($role)) {
          $errors[] = "Le rôle est obligatoire";
      }
      
      if (empty($statut)) {
          $errors[] = "Le statut est obligatoire";
      }

      // Si pas d'erreurs, procéder à l'inscription
      if (empty($errors)) {
          $utilisateur = new Utilisateur(
              null,
              $nom,
              $prenom,
              $email,
              $password, // Storing plain text password
              $role,
              $statut,
              $dateInscription,
              $photoUrl
          );
          
          $controller = new UtilisateurController();
          $userId = $controller->ajouterUtilisateur($utilisateur);

          if ($userId !== false) {
              $_SESSION['success_message'] = "Inscription réussie! Vous pouvez maintenant vous connecter.";
              header('Location: ' . BASE_URL . 'views/front/login2.php');
              exit;
          } else {
              throw new Exception("Échec de l'insertion dans la base de données");
          }
      } else {
          $_SESSION['form_errors'] = $errors;
          $_SESSION['form_data'] = $_POST;
      }
      
  } catch (PDOException $e) {
      error_log("Erreur PDO: " . $e->getMessage());
      $_SESSION['form_errors'] = ["Une erreur technique est survenue. Veuillez réessayer."];
  } catch (Exception $e) {
      error_log("Erreur: " . $e->getMessage());
      $_SESSION['form_errors'] = [$e->getMessage()];
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer un compte - EasyParki</title>
  <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <!-- <link rel="stylesheet" href="./styles/inscription.css"> -->
  <style>
    :root {
  --primary: #3AB0FF;
  --primary-dark: #2980b9;
  --error: #e74c3c;
  --success: #2ecc71;
  --warning: #f39c12;
  --text: #ecf0f1;
  --light: rgba(255, 255, 255, 0.9);
  --dark: rgba(0, 0, 0, 0.7);
  --bg-dark: #0f172a;
  --bg-form: rgba(15, 23, 42, 0.85);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
}

body {
  min-height: 100vh;
  color: var(--text);
  display: flex;
  justify-content: center;
  align-items: center;
  background: var(--bg-dark);
  font-size: 16px;
  padding: 20px;
}

spline-viewer {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

/* .logo-container {
  position: absolute;
  top: 20px;
  left: 20px;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.2);
  overflow: hidden;
  z-index: 20;
  transition: all 0.4s ease;
}

.logo-container img {
  width: 65%;
  object-fit: contain;
  filter: drop-shadow(0 2px 5px rgba(0,0,0,0.4));
}

.logo-container:hover {
  transform: scale(1.07);
  background: rgba(255, 255, 255, 0.2);
} */
 
.logo-container {
    position: absolute;
    top: 25px;
    left: 30px; 
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.06);
    border-radius: 50%;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
    overflow: hidden;
    animation: floatSubtle 6s ease-in-out infinite;
    z-index: 20;
    transition: transform 0.3s ease;
  }

  .logo-container img {
    width: 120%;
    height: 120%;
    object-fit: cover;
    transform: scale(1.05);
    transition: transform 0.4s ease;
    border-radius: 50%;
  }

  @keyframes floatSubtle {
    0%, 100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-4px);
    }
  }

  .logo-container:hover img {
    transform: scale(1.12);
  }

.form-container {
  backdrop-filter: blur(16px);
  width: 100%;
  max-width: 600px;
  background: var(--bg-form);
  border-radius: 16px;
  padding: 30px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
  animation: fadeIn 0.8s ease-out;
  position: relative;
  overflow: hidden;
  margin: 20px 0;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px);}
  to   { opacity: 1; transform: translateY(0);}
}

.form-header {
  margin-bottom: 25px;
  text-align: center;
}

.form-header h1 {
  font-size: 2rem;
  margin-bottom: 8px;
  color: #fff;
  font-weight: 600;
}

.form-header p {
  font-size: 1rem;
  opacity: 0.8;
}

.form-group {
  margin-bottom: 18px;
  position: relative;
}

.form-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #3AB0FF;
  font-size: 0.95rem;
}

.form-control {
  width: 100%;
  padding: 14px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  color: var(--text);
  font-size: 1rem;
  transition: all 0.3s;
}

.form-control::placeholder {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.9rem;
}

.form-control:focus {
  background: rgba(255, 255, 255, 0.15);
  border-color: var(--primary);
  outline: none;
  box-shadow: 0 0 0 2px rgba(58, 176, 255, 0.3);
}

.password-wrapper {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(255,255,255,0.6);
  cursor: pointer;
  font-size: 1rem;
}

.btn {
  width: 100%;
  padding: 14px;
  background: var(--primary);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 10px;
  box-shadow: 0 4px 15px rgba(58, 176, 255, 0.3);
}

.btn:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
}

.message-box {
  padding: 14px;
  border-radius: 8px;
  font-size: 0.9rem;
  text-align: center;
  margin-bottom: 20px;
  display: block;
  opacity: 1;
  animation: fadeIn 0.5s ease-out;
}

.error-box {
  background: rgba(231, 76, 60, 0.2);
  border: 1px solid var(--error);
  color: #ff6b6b;
}

.success-box {
  background: rgba(46, 204, 113, 0.2);
  border: 1px solid var(--success);
  color: #51cf66;
}

.required-star {
  color: var(--error);
  margin-left: 3px;
}

.login-link {
  text-align: center;
  margin-top: 20px;
  font-size: 0.95rem;
}

.login-link a {
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
}

.login-link a:hover {
  color: white;
  text-decoration: underline;
}

/* Validation popups */
.validation-popup {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  opacity: 0;
  transition: all 0.3s ease;
  pointer-events: none;
}

.valid {
  background: var(--success);
  opacity: 1;
}

.invalid {
  background: var(--error);
  opacity: 1;
}

.warning {
  background: var(--warning);
  opacity: 1;
}

.validation-message {
  position: absolute;
  bottom: -30px;
  left: 0;
  width: 100%;
  font-size: 0.75rem;
  padding: 5px;
  border-radius: 4px;
  background: rgba(0,0,0,0.7);
  color: white;
  opacity: 0;
  transition: all 0.3s ease;
  pointer-events: none;
  z-index: 10;
}

.form-group:focus-within .validation-message {
  opacity: 1;
  bottom: -25px;
}

/* Password strength meter */
.password-strength {
  height: 4px;
  background: rgba(255,255,255,0.1);
  border-radius: 2px;
  margin-top: 8px;
  overflow: hidden;
  position: relative;
}

.strength-meter {
  height: 100%;
  width: 0;
  background: var(--error);
  transition: all 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
  body {
    padding: 10px;
  }
  
  .form-container {
    padding: 25px;
  }
  
  .form-header h1 {
    font-size: 1.8rem;
  }
}

@media (max-width: 480px) {
  .form-container {
    padding: 20px;
  }
  
  .form-control {
    padding: 12px;
  }
  
  .btn {
    padding: 12px;
  }
}
  </style>
</head>

<body>

<spline-viewer url="https://prod.spline.design/3lhc1gVLc3h0YnfN/scene.splinecode"></spline-viewer>

<!-- <div class="logo-container">
  <img src="./images/logo2.png" alt="EasyParki Logo">
</div> -->
<!-- Logo -->
<div class="logo-container">
    <img src="./images/logo2.png" alt="Logo EasyParki">
  </div>

<div class="form-container">
  <div class="form-header">
    <h1>Créer un compte</h1>
    <p>Rejoignez notre communauté maintenant</p>
  </div>

  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="message-box success-box">
      <?= $_SESSION['success_message']; ?>
      <?php unset($_SESSION['success_message']); ?>
    </div>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['form_errors'])): ?>
    <div id="errorBox" class="message-box error-box">
      <?php foreach ($_SESSION['form_errors'] as $error): ?>
        <?= $error ?><br>
      <?php endforeach; ?>
      <?php unset($_SESSION['form_errors']); ?>
    </div>
  <?php endif; ?>

  <form id="createUserForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <div class="form-group">
      <label class="form-label">Nom <span class="required-star">*</span></label>
      <input type="text" name="nom" id="nom" class="form-control" placeholder="Votre nom" 
             value="<?= isset($_SESSION['form_data']['nom']) ? htmlspecialchars($_SESSION['form_data']['nom']) : '' ?>">
      <div class="validation-popup" id="nomValidation"></div>
      <div class="validation-message" id="nomMessage"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Prénom <span class="required-star">*</span></label>
      <input type="text" name="prenom" id="prenom" class="form-control" placeholder="Votre prénom" 
             value="<?= isset($_SESSION['form_data']['prenom']) ? htmlspecialchars($_SESSION['form_data']['prenom']) : '' ?>">
      <div class="validation-popup" id="prenomValidation"></div>
      <div class="validation-message" id="prenomMessage"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Email <span class="required-star">*</span></label>
      <input type="email" name="email" id="email" class="form-control" placeholder="exemple@email.com" 
             value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>">
      <div class="validation-popup" id="emailValidation"></div>
      <div class="validation-message" id="emailMessage"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Mot de passe <span class="required-star">*</span></label>
      <div class="password-wrapper">
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
        <i class="fas fa-eye toggle-password" data-target="password"></i>
        <div class="validation-popup" id="passwordValidation"></div>
      </div>
      <div class="password-strength">
        <div class="strength-meter" id="strengthMeter"></div>
      </div>
      <div class="validation-message" id="passwordMessage"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Confirmation <span class="required-star">*</span></label>
      <div class="password-wrapper">
        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="••••••••">
        <i class="fas fa-eye toggle-password" data-target="confirmPassword"></i>
        <div class="validation-popup" id="confirmPasswordValidation"></div>
      </div>
      <div class="validation-message" id="confirmPasswordMessage"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Rôle <span class="required-star">*</span></label>
      <select id="role" name="role" class="form-control">
        <option value="">-- Sélectionnez --</option>
        <option value="admin" <?= isset($_SESSION['form_data']['role']) && $_SESSION['form_data']['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
        <option value="chauffeur" <?= isset($_SESSION['form_data']['role']) && $_SESSION['form_data']['role'] === 'chauffeur' ? 'selected' : '' ?>>Chauffeur</option>
        <option value="client" <?= isset($_SESSION['form_data']['role']) && $_SESSION['form_data']['role'] === 'client' ? 'selected' : '' ?>>Client</option>
      </select>
      <div class="validation-popup" id="roleValidation"></div>
      <div class="validation-message" id="roleMessage"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Statut <span class="required-star">*</span></label>
      <select id="statut" name="statut" class="form-control">
        <option value="">-- Sélectionnez --</option>
        <option value="actif" <?= isset($_SESSION['form_data']['statut']) && $_SESSION['form_data']['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
        <option value="inactif" <?= isset($_SESSION['form_data']['statut']) && $_SESSION['form_data']['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
      </select>
      <div class="validation-popup" id="statutValidation"></div>
      <div class="validation-message" id="statutMessage"></div>
    </div>

    <div class="form-group">
      <label class="form-label">URL de la photo</label>
      <input type="url" name="photoUrl" id="photoUrl" class="form-control" placeholder="https://example.com/photo.jpg" 
             value="<?= isset($_SESSION['form_data']['photoUrl']) ? htmlspecialchars($_SESSION['form_data']['photoUrl']) : '' ?>">
      <div class="validation-popup" id="photoUrlValidation"></div>
      <div class="validation-message" id="photoUrlMessage"></div>
    </div>

    <input type="hidden" name="dateInscription" id="dateInscription">
    <input type="hidden" name="create" value="1">

    <button type="submit" class="btn">Créer mon compte</button>

    <div class="login-link">
      Déjà membre ? <a href="#">Connectez-vous</a>
    </div>
  </form>
</div>

<script>
  // Messages de validation
  const validationMessages = {
    nom: {
      empty: "🚨 Le nom est requis",
      invalid: "📛 2 caractères minimum",
      valid: "✅ Nom valide"
    },
    prenom: {
      empty: "🚨 Le prénom est requis",
      invalid: "📛 2 caractères minimum",
      valid: "✅ Prénom valide"
    },
    email: {
      empty: "🚨 L'email est requis",
      invalid: "📧 Format d'email invalide",
      valid: "✅ Email valide"
    },
    password: {
      empty: "🔒 Le mot de passe est requis",
      tooShort: "📏 8 caractères minimum",
      noNumber: "1️⃣ Ajoutez un chiffre",
      noUpper: "🔠 Ajoutez une majuscule",
      noSpecial: "🌟 Ajoutez un caractère spécial",
      valid: "🔐 Mot de passe sécurisé"
    },
    confirmPassword: {
      empty: "🔒 Confirmez le mot de passe",
      mismatch: "⚠️ Les mots de passe ne correspondent pas",
      valid: "✅ Correspondance confirmée"
    },
    role: {
      empty: "🎭 Sélectionnez un rôle",
      valid: "✅ Rôle sélectionné"
    },
    statut: {
      empty: "🔄 Sélectionnez un statut",
      valid: "✅ Statut sélectionné"
    },
    photoUrl: {
      invalid: "🌅 URL invalide (facultatif)",
      valid: "✅ URL valide (facultatif)"
    }
  };

  // Affichage/Masquage des mots de passe
  document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', () => {
      const input = document.getElementById(icon.dataset.target);
      input.type = input.type === 'password' ? 'text' : 'password';
      icon.classList.toggle('fa-eye-slash');
    });
  });

  // Date actuelle au format MySQL
  document.getElementById("dateInscription").value = new Date().toISOString().slice(0, 19).replace('T', ' ');

  // Fonction pour afficher la validation
  function showValidation(element, messageElement, type, message) {
    // Reset classes
    element.className = 'validation-popup';
    messageElement.textContent = message;
    
    if (type === 'valid') {
      element.classList.add('valid');
      element.innerHTML = '<i class="fas fa-check"></i>';
    } else if (type === 'invalid') {
      element.classList.add('invalid');
      element.innerHTML = '<i class="fas fa-times"></i>';
    } else if (type === 'warning') {
      element.classList.add('warning');
      element.innerHTML = '<i class="fas fa-exclamation"></i>';
    }
  }

  // Validation en temps réel
  document.getElementById('nom').addEventListener('input', function() {
    const value = this.value.trim();
    const validation = document.getElementById('nomValidation');
    const message = document.getElementById('nomMessage');
    
    if (value === '') {
      showValidation(validation, message, 'invalid', validationMessages.nom.empty);
    } else if (value.length < 2) {
      showValidation(validation, message, 'invalid', validationMessages.nom.invalid);
    } else {
      showValidation(validation, message, 'valid', validationMessages.nom.valid);
    }
  });

  document.getElementById('prenom').addEventListener('input', function() {
    const value = this.value.trim();
    const validation = document.getElementById('prenomValidation');
    const message = document.getElementById('prenomMessage');
    
    if (value === '') {
      showValidation(validation, message, 'invalid', validationMessages.prenom.empty);
    } else if (value.length < 2) {
      showValidation(validation, message, 'invalid', validationMessages.prenom.invalid);
    } else {
      showValidation(validation, message, 'valid', validationMessages.prenom.valid);
    }
  });

  document.getElementById('email').addEventListener('input', function() {
    const value = this.value.trim();
    const validation = document.getElementById('emailValidation');
    const message = document.getElementById('emailMessage');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (value === '') {
      showValidation(validation, message, 'invalid', validationMessages.email.empty);
    } else if (!emailRegex.test(value)) {
      showValidation(validation, message, 'invalid', validationMessages.email.invalid);
    } else {
      showValidation(validation, message, 'valid', validationMessages.email.valid);
    }
  });

  document.getElementById('password').addEventListener('input', function() {
    const value = this.value;
    const validation = document.getElementById('passwordValidation');
    const message = document.getElementById('passwordMessage');
    const meter = document.getElementById('strengthMeter');
    
    // Réinitialiser
    let strength = 0;
    meter.style.width = '0%';
    meter.style.backgroundColor = '#e74c3c';
    
    if (value === '') {
      showValidation(validation, message, 'invalid', validationMessages.password.empty);
      return;
    }
    
    // Vérifier la longueur
    if (value.length >= 8) strength += 20;
    
    // Vérifier les chiffres
    if (/\d/.test(value)) strength += 20;
    
    // Vérifier les majuscules
    if (/[A-Z]/.test(value)) strength += 20;
    
    // Vérifier les caractères spéciaux
    if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) strength += 20;
    
    // Vérifier la longueur minimale
    if (value.length >= 12) strength += 20;
    
    // Mettre à jour la jauge
    meter.style.width = `${strength}%`;
    
    // Définir la couleur en fonction de la force
    if (strength < 40) {
      meter.style.backgroundColor = '#e74c3c';
      showValidation(validation, message, 'invalid', validationMessages.password.tooShort);
    } else if (strength < 60) {
      meter.style.backgroundColor = '#f39c12';
      showValidation(validation, message, 'warning', validationMessages.password.noNumber);
    } else if (strength < 80) {
      meter.style.backgroundColor = '#ffcc00';
      showValidation(validation, message, 'warning', validationMessages.password.noUpper);
    } else if (strength < 100) {
      meter.style.backgroundColor = '#66cc33';
      showValidation(validation, message, 'warning', validationMessages.password.noSpecial);
    } else {
      meter.style.backgroundColor = '#2ecc71';
      showValidation(validation, message, 'valid', validationMessages.password.valid);
    }
  });

  document.getElementById('confirmPassword').addEventListener('input', function() {
    const value = this.value;
    const password = document.getElementById('password').value;
    const validation = document.getElementById('confirmPasswordValidation');
    const message = document.getElementById('confirmPasswordMessage');
    
    if (value === '') {
      showValidation(validation, message, 'invalid', validationMessages.confirmPassword.empty);
    } else if (value !== password) {
      showValidation(validation, message, 'invalid', validationMessages.confirmPassword.mismatch);
    } else {
      showValidation(validation, message, 'valid', validationMessages.confirmPassword.valid);
    }
  });

  document.getElementById('role').addEventListener('change', function() {
    const value = this.value;
    const validation = document.getElementById('roleValidation');
    const message = document.getElementById('roleMessage');
    
    if (value === '') {
      showValidation(validation, message, 'invalid', validationMessages.role.empty);
    } else {
      showValidation(validation, message, 'valid', validationMessages.role.valid);
    }
  });

  document.getElementById('statut').addEventListener('change', function() {
    const value = this.value;
    const validation = document.getElementById('statutValidation');
    const message = document.getElementById('statutMessage');
    
    if (value === '') {
      showValidation(validation, message, 'invalid', validationMessages.statut.empty);
    } else {
      showValidation(validation, message, 'valid', validationMessages.statut.valid);
    }
  });

  document.getElementById('photoUrl').addEventListener('input', function() {
    const value = this.value.trim();
    const validation = document.getElementById('photoUrlValidation');
    const message = document.getElementById('photoUrlMessage');
    
    if (value === '') {
      validation.className = 'validation-popup';
      message.textContent = '';
    } else {
      try {
        new URL(value);
        showValidation(validation, message, 'valid', validationMessages.photoUrl.valid);
      } catch (e) {
        showValidation(validation, message, 'invalid', validationMessages.photoUrl.invalid);
      }
    }
  });

  // Validation avant soumission
  document.getElementById("createUserForm").addEventListener("submit", function(e) {
    let isValid = true;
    const errors = [];
    
    // Vérifier chaque champ
    const nom = document.getElementById("nom").value.trim();
    if (nom === '' || nom.length < 2) {
      errors.push(validationMessages.nom.invalid);
      isValid = false;
    }
    
    const prenom = document.getElementById("prenom").value.trim();
    if (prenom === '' || prenom.length < 2) {
      errors.push(validationMessages.prenom.invalid);
      isValid = false;
    }
    
    const email = document.getElementById("email").value.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      errors.push(validationMessages.email.invalid);
      isValid = false;
    }
    
    const password = document.getElementById("password").value;
    if (password.length < 8) {
      errors.push(validationMessages.password.tooShort);
      isValid = false;
    }
    
    const confirmPassword = document.getElementById("confirmPassword").value;
    if (password !== confirmPassword) {
      errors.push(validationMessages.confirmPassword.mismatch);
      isValid = false;
    }
    
    const role = document.getElementById("role").value;
    if (role === '') {
      errors.push(validationMessages.role.empty);
      isValid = false;
    }
    
    const statut = document.getElementById("statut").value;
    if (statut === '') {
      errors.push(validationMessages.statut.empty);
      isValid = false;
    }
    
    if (!isValid) {
      e.preventDefault();
      const errorBox = document.getElementById("errorBox") || document.createElement('div');
      errorBox.id = 'errorBox';
      errorBox.className = 'message-box error-box';
      errorBox.innerHTML = errors.join("<br>");
      
      if (!document.getElementById("errorBox")) {
        const formHeader = document.querySelector('.form-header');
        formHeader.insertAdjacentElement('afterend', errorBox);
      } else {
        errorBox.style.display = 'block';
      }
      
      // Scroll vers les erreurs
      errorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });

  // Afficher les messages de validation au chargement si erreurs
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['form_errors'])): ?>
      const errorBox = document.getElementById('errorBox');
      if (errorBox) {
        errorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    <?php endif; ?>
  });
</script>

<script>
  window.onload = function() {
    const shadowRoot = document.querySelector('spline-viewer').shadowRoot;
    if (shadowRoot) {
        const logo = shadowRoot.querySelector('#logo');
        if (logo) logo.remove();
    }
  }
</script>

</body>
</html>
<?php unset($_SESSION['form_data']); ?>