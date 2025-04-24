<?php 
session_start();

$error = '';
$showLoading = false;

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
    if (strpos($error, 'mot de passe') !== false || strpos($error, 'email') !== false) {
        $showLoading = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion avec Voiture Animée</title>
  <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="./styles/login2.css">
  <style>
    .popup-error {
      position: absolute;
      top: 38%; /* Position ajustée pour être au-dessus du champ email */
      left: 50%;
      transform: translate(-50%, 0);
      background-color: rgba(255, 50, 50, 0.95);
      color: white;
      padding: 1em 2em;
      border-radius: 10px;
      font-weight: bold;
      font-family: sans-serif;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
      z-index: 10;
      animation: fadeInUp 0.6s ease-in-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translate(-50%, 20px);
      }
      to {
        opacity: 1;
        transform: translate(-50%, 0);
      }
    }
  </style>
</head>
<body>

  <!-- Background Spline -->
  <spline-viewer url="https://prod.spline.design/3lhc1gVLc3h0YnfN/scene.splinecode"></spline-viewer>

  <!-- Popup Erreur au-dessus du champ email -->
  <?php if (!empty($error)): ?>
    <div class="popup-error" id="popup-error">
      <p id="popup-message"><?= htmlspecialchars($error) ?></p>
    </div>
  <?php endif; ?>

  <!-- Logo -->
  <div class="logo-container">
    <img src="./images/logo2.png" alt="Logo EasyParki">
  </div>

  <!-- Voiture -->
  <div class="circle-frame">
    <div class="car-container">
      <div class="car" id="car">
        <div class="roof"></div>
        <div class="window"></div>
        <div class="eye-container left" id="eye-left">
          <div class="eye">
            <div class="pupil" id="pupil-left"></div>
          </div>
        </div>
        <div class="eye-container right" id="eye-right">
          <div class="eye">
            <div class="pupil" id="pupil-right"></div>
          </div>
        </div>
        <div class="hand left" id="hand-left"></div>
        <div class="hand right" id="hand-right"></div>
        <div class="wheel left"></div>
        <div class="wheel right"></div>
        <div class="headlight left"></div>
        <div class="headlight right"></div>
        <div class="grill">
          <div class="grill-line"></div>
          <div class="grill-line"></div>
          <div class="grill-line"></div>
          <div class="grill-line"></div>
          <div class="grill-line"></div>
        </div>
        <div class="bumper"></div>
      </div>
    </div>
  </div>

  <!-- Formulaire dans une carte -->
  <div class="form-card">
    <form class="form" action="../../controllers/authController.php?action=login" method="POST">
      <div class="input-group">
        <div class="input-wrapper">
          <input type="email" name="email" id="email" placeholder="Email" required autocomplete="off">
          <div class="validation-box" id="email-validation">Veuillez entrer un email valide</div>
        </div>
      </div>
      <div class="input-group">
        <div class="input-wrapper">
          <input type="password" name="motDePasse" id="password" placeholder="Mot de passe" required autocomplete="off">
          <div class="validation-box" id="password-validation">Le mot de passe doit contenir au moins 6 caractères, une majuscule, un chiffre et un caractère spécial</div>
          <button type="button" class="password-toggle" id="password-toggle">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="login-btn" name="login">Se connecter</button>

      <div class="links">
        <p><a href="forgot-password.php">Mot de passe oublié ?</a></p>
        <p>Pas encore de compte ? <a href="./inscription.php">S'inscrire</a></p>
      </div>
    </form>
  </div>

  <script>
    window.onload = function() {
        const shadowRoot = document.querySelector('spline-viewer').shadowRoot;
        if (shadowRoot) {
            const logo = shadowRoot.querySelector('#logo');
            if (logo) logo.remove();
        }
    }
  </script>
  <script>
      // Éléments du DOM
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const passwordToggle = document.getElementById('password-toggle');
  const car = document.getElementById('car');
  const eyeLeft = document.getElementById('eye-left');
  const eyeRight = document.getElementById('eye-right');
  const pupilLeft = document.getElementById('pupil-left');
  const pupilRight = document.getElementById('pupil-right');
  const handLeft = document.getElementById('hand-left');
  const handRight = document.getElementById('hand-right');
  const emailValidation = document.getElementById('email-validation');
  const passwordValidation = document.getElementById('password-validation');
  let passwordVisible = false;

  // Messages d'erreur originaux
  const emailMessages = {
    empty: "🚨 Oh là là! Le champ email est vide",
    missingAt: "🔍 Oups! Il manque le @ dans l'email",
    invalidFormat: "📧 Mince! Format d'email invalide",
    unknown: "👀 Email inconnu au bataillon",
    valid: "✅ Email valide! Tout bon"
  };

  const passwordMessages = {
    empty: "🔒 Psst! Le mot de passe est vide",
    tooShort: "📏 6 caractères minimum s'il vous plaît",
    noNumber: "1️⃣ Un petit chiffre pour la route?",
    noUpper: "🔠 Une majuscule serait parfaite",
    noSpecial: "🌟 Un caractère spécial (!@#) pour briller",
    valid: "🔐 Mot de passe sécurisé ! Parfait"
  };

  // Animation des yeux qui suivent la souris
  function updateEyes(e) {
    if (!car.classList.contains('hide-eyes')) {
      const carRect = car.getBoundingClientRect();
      const eyeLeftRect = eyeLeft.getBoundingClientRect();
      const eyeRightRect = eyeRight.getBoundingClientRect();
      
      const eyeLeftCenter = {
        x: eyeLeftRect.left + eyeLeftRect.width / 2,
        y: eyeLeftRect.top + eyeLeftRect.height / 2
      };
      
      const eyeRightCenter = {
        x: eyeRightRect.left + eyeRightRect.width / 2,
        y: eyeRightRect.top + eyeRightRect.height / 2
      };
      
      const angleLeft = Math.atan2(e.clientY - eyeLeftCenter.y, e.clientX - eyeLeftCenter.x);
      const angleRight = Math.atan2(e.clientY - eyeRightCenter.y, e.clientX - eyeRightCenter.x);
      
      const pupilDistance = 8;
      pupilLeft.style.transform = `translate(calc(-50% + ${Math.cos(angleLeft) * pupilDistance}px), calc(-50% + ${Math.sin(angleLeft) * pupilDistance}px))`;
      pupilRight.style.transform = `translate(calc(-50% + ${Math.cos(angleRight) * pupilDistance}px), calc(-50% + ${Math.sin(angleRight) * pupilDistance}px))`;
    }
  }

  // Gestion du focus sur l'email
  emailInput.addEventListener('focus', () => {
    car.classList.remove('hide-eyes', 'wink-mode');
    eyeLeft.classList.remove('closed', 'wink');
    eyeRight.classList.remove('closed', 'wink');
    handLeft.style = handRight.style = '';
  });

  // Gestion du focus sur le mot de passe
  passwordInput.addEventListener('focus', () => {
    car.classList.add('hide-eyes');
    eyeLeft.classList.add('closed');
    eyeRight.classList.add('closed');
    handLeft.style.transform = handRight.style.transform = 'rotate(45deg)';
    handLeft.style.top = handRight.style.top = '40px';
    handLeft.style.left = '50px';
    handRight.style.right = '50px';
  });

  // Basculer la visibilité du mot de passe
  function togglePassword() {
    passwordVisible = !passwordVisible;
    passwordInput.type = passwordVisible ? 'text' : 'password';
    
    if (passwordVisible) {
      car.classList.remove('hide-eyes');
      car.classList.add('wink-mode');
      eyeLeft.classList.remove('closed');
      eyeRight.classList.add('wink');
    } else if (document.activeElement === passwordInput) {
      car.classList.add('hide-eyes');
      eyeLeft.classList.add('closed');
      eyeRight.classList.add('closed');
    } else {
      car.classList.remove('wink-mode');
      eyeLeft.classList.remove('closed');
      eyeRight.classList.remove('wink');
    }
    
    // Mettre à jour l'icône
    const icon = passwordToggle.querySelector('i');
    icon.classList.toggle('fa-eye', !passwordVisible);
    icon.classList.toggle('fa-eye-slash', passwordVisible);
  }

  passwordToggle.addEventListener('click', togglePassword);

  // Validation de l'email
  emailInput.addEventListener('input', function() {
    const email = this.value;
    
    if (email === '') {
      showValidation(emailValidation, emailMessages.empty, false);
      return;
    }
    
    if (!email.includes('@')) {
      showValidation(emailValidation, emailMessages.missingAt, false);
      return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showValidation(emailValidation, emailMessages.invalidFormat, false);
      return;
    }
    
    checkEmailExists(email);
  });
  
  // Validation du mot de passe
  passwordInput.addEventListener('input', function() {
    const password = this.value;
    
    if (password === '') {
      showValidation(passwordValidation, passwordMessages.empty, false);
      return;
    }
    
    const checks = {
      length: password.length >= 6,
      number: /\d/.test(password),
      upper: /[A-Z]/.test(password),
      special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    if (!checks.length) {
      showValidation(passwordValidation, passwordMessages.tooShort, false);
    } else if (!checks.number) {
      showValidation(passwordValidation, passwordMessages.noNumber, false);
    } else if (!checks.upper) {
      showValidation(passwordValidation, passwordMessages.noUpper, false);
    } else if (!checks.special) {
      showValidation(passwordValidation, passwordMessages.noSpecial, false);
    } else {
      showValidation(passwordValidation, passwordMessages.valid, true);
    }
  });

  // Afficher un message de validation
  function showValidation(element, message, isValid) {
    element.innerHTML = message;
    element.classList.add('visible', 'animated');
    element.classList.remove('valid', 'invalid');
    
    if (isValid) {
      element.classList.add('valid');
      element.style.animation = 'bounce 0.5s';
    } else {
      element.classList.add('invalid');
      element.style.animation = 'shake 0.5s';
    }
    
    setTimeout(() => element.style.animation = '', 500);
    clearTimeout(element.hideTimeout);
    
    if (element.previousElementSibling.value === '') {
      element.hideTimeout = setTimeout(() => hideValidation(element), 3000);
    }
  }
  
  // Cacher un message de validation
  function hideValidation(element) {
    element.classList.remove('visible', 'valid', 'invalid', 'animated');
  }

  // Vérifier si l'email existe via AJAX
  function checkEmailExists(email) {
    fetch('/prjweb2025/controllers/authController.php?action=checkEmail&email=' + encodeURIComponent(email))
      .then(response => response.json())
      .then(data => {
        showValidation(emailValidation, 
          data.exists ? emailMessages.valid : emailMessages.unknown, 
          data.exists);
      })
      .catch(() => {
        showValidation(emailValidation, "⚠️ Erreur de vérification", false);
      });
  }

  // Événements
  document.addEventListener('mousemove', updateEyes);
  emailInput.addEventListener('focus', () => car.classList.add('typing'));
  emailInput.addEventListener('blur', () => car.classList.remove('typing'));

  // Masquer le logo Spline
  window.onload = function() {
    const shadowRoot = document.querySelector('spline-viewer').shadowRoot;
    if (shadowRoot) {
        const logo = shadowRoot.querySelector('#logo');
        if (logo) logo.remove();
    }
  }
  
  // Fermer la popup d'erreur après 5 secondes
  const popupError = document.getElementById('popup-error');
  if (popupError) {
    setTimeout(() => {
      popupError.style.display = 'none';
    }, 5000);
  }
  </script>

  <!-- Script pour le loading -->
  <script>
    const showLoading = <?= $showLoading ? 'true' : 'false' ?>;

    if (showLoading) {
      // Affiche loading.html dans une iframe cachée
      const loadingScreen = document.createElement('iframe');
      loadingScreen.src = 'loading.html';
      loadingScreen.style.position = 'fixed';
      loadingScreen.style.top = 0;
      loadingScreen.style.left = 0;
      loadingScreen.style.width = '100vw';
      loadingScreen.style.height = '100vh';
      loadingScreen.style.border = 'none';
      loadingScreen.style.zIndex = 999;
      document.body.appendChild(loadingScreen);

      setTimeout(() => {
        loadingScreen.remove();
      }, 2000); // Simule 2 secondes de chargement
    }
  </script>

</body>
</html>