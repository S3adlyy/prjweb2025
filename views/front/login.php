<?php 
session_start();

// Display errors
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
} else {
    $error = '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - EasyParki</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
        background: linear-gradient(135deg, #0A192F, #203a43, #2c5364);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
    }
    .login-container {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
    }
    .logo {
        text-align: center;
        margin-bottom: 25px;
    }
    .logo img {
        height: 80px;
        margin-bottom: 15px;
    }
    .form-control {
        height: 45px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .btn-login {
        background-color: #3AB0FF;
        border: none;
        height: 45px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        color: white;
    }
    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 5;
    }
    .alert {
        margin-bottom: 20px;
    }
    .error-message {
        color: red;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    }
  </style>
</head>
<body>
<div class="login-container">
    <div class="logo">
        <img src="/prjweb2025/assets/images/logo.png" alt="EasyParki Logo">
        <h3 class="text-center">Connexion Administrateur</h3>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="/prjweb2025/controllers/authController.php?action=login" method="POST">
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group position-relative">
            <input type="password" id="password" name="motDePasse" class="form-control" placeholder="Mot de passe" required>
            <button type="button" class="password-toggle" onclick="togglePassword()">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <button type="submit" class="btn btn-login" name="login">Se connecter</button>
    </form>

    <div class="text-center mt-3">
        <p><a href="forgot-password.php">Mot de passe oublié ?</a></p>
        <p>Pas encore de compte ? <a href="./inscription.php">S'inscrire</a></p>
    </div>
</div>

<script>
  function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.password-toggle i');
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleBtn.classList.remove('bi-eye');
      toggleBtn.classList.add('bi-eye-slash');
    } else {
      passwordInput.type = 'password';
      toggleBtn.classList.remove('bi-eye-slash');
      toggleBtn.classList.add('bi-eye');
    }
  }
</script>
</body>
</html>