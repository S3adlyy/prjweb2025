<?php
session_start();

$user = $_SESSION['user'];
$statut = $user['statut'] ?? $user['Statut'] ?? 'actif';
$dateInscription = $user['date_inscription'] ?? $user['Date_inscription'] ?? '17/04/2025';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - EasyParki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/dashboard.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidenav">
        <div class="sidenav-header">
            <img src="images/logo2.png" alt="EasyParki Logo">
            <h4>EasyParki</h4>
        </div>
        
        <div class="mt-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="../pages/dashboard.html">
                        <i class="bi bi-house-door"></i>
                        <span>Accueil</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./inscription.php">
                        <i class="bi bi-person-plus"></i>
                        <span>Inscription</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="profil.php">
                        <i class="bi bi-person-circle"></i>
                        <span>Profil</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/contact.html">
                        <i class="bi bi-envelope"></i>
                        <span>Contact</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/autres.html">
                        <i class="bi bi-three-dots"></i>
                        <span>Autres</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="sidenav-footer">
            <a href="../index.html" class="btn btn-outline-light w-100">
                <i class="bi bi-box-arrow-left me-2"></i> Retour au site
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="container profile-container">
        <div class="text-center mb-4">
            <img src="<?= htmlspecialchars($user['photo_url'] ?? 'assets/img/default-user.png') ?>" id="profileImagePreview" class="profile-picture">
            <h2 class="user-name"><?= htmlspecialchars($user['Prenom'] . ' ' . $user['Nom']) ?></h2>
            <span class="user-role"><?= htmlspecialchars(ucfirst($user['Role'])) ?></span>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="info-card">
                    <h5><i class="bi bi-person-badge me-2"></i>Informations personnelles</h5>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-envelope me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Email</small>
                            <p class="mb-0"><?= htmlspecialchars($user['Email']) ?></p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Statut</small>
                            <p class="mb-0">
                                <span class="badge bg-<?= strtolower($statut) === 'actif' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars(ucfirst($statut)) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-event me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Inscription</small>
                            <p class="mb-0"><?= htmlspecialchars($dateInscription) ?></p>
                        </div>
                    </div>
                </div>

                <div class="photo-form">
                    <h5><i class="bi bi-camera me-2"></i>Changer la photo de profil</h5>
                    
                    <div class="photo-upload-container" onclick="document.getElementById('photoFileInput').click()">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <p>Cliquez pour télécharger ou glisser-déposer</p>
                        <input type="file" id="photoFileInput" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="photoUrlInput" class="form-label small text-muted">Ou entrer une URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="text" class="form-control" id="photoUrlInput" placeholder="https://exemple.com/photo.jpg" oninput="updateImagePreview()">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateImagePreview()">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button class="btn btn-custom w-100" onclick="savePhoto()">
                        <i class="bi bi-save me-2"></i>Sauvegarder la photo
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <div class="password-form">
                    <h5><i class="bi bi-shield-lock me-2"></i>Changer le mot de passe</h5>
                    <form id="changePasswordForm">
                        <div class="form-group">
                            <label for="currentPassword" class="form-label small text-muted">Mot de passe actuel</label>
                            <div class="password-input-group">
                                <input type="password" class="form-control" id="currentPassword" placeholder="Entrez votre mot de passe actuel">
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('currentPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="newPassword" class="form-label small text-muted">Nouveau mot de passe</label>
                            <div class="password-input-group">
                                <input type="password" class="form-control" id="newPassword" placeholder="Créez un nouveau mot de passe">
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('newPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <p class="small text-muted mb-2">Le mot de passe doit contenir :</p>
                                <ul class="list-unstyled">
                                    <li class="requirement" id="req-length">Minimum 8 caractères</li>
                                    <li class="requirement" id="req-uppercase">Au moins une majuscule</li>
                                    <li class="requirement" id="req-lowercase">Au moins une minuscule</li>
                                    <li class="requirement" id="req-number">Au moins un chiffre</li>
                                    <li class="requirement" id="req-special">Au moins un caractère spécial</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword" class="form-label small text-muted">Confirmer le mot de passe</label>
                            <div class="password-input-group">
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirmez le nouveau mot de passe">
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('confirmPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback d-none" id="passwordMatchError">
                                Les mots de passe ne correspondent pas
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-custom w-100 mt-3">
                            <i class="bi bi-arrow-repeat me-2"></i>Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
                <button type="submit" class="btn btn-outline-danger px-4">
                   <a href="login2.php"><i class="bi bi-box-arrow-right me-2"></i> Déconnexion</a>
                </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- *********************************************** -->
    <script>
        // Photo upload functionality
        function updateImagePreview() {
            const url = document.getElementById('photoUrlInput').value.trim();
            const preview = document.getElementById('profileImagePreview');
            
            if (url) {
                preview.src = url;
                preview.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    preview.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            }
        }

        function savePhoto() {
            const urlInput = document.getElementById('photoUrlInput');
            const fileInput = document.getElementById('photoFileInput');
            const preview = document.getElementById('profileImagePreview');
            
            // Check if URL is provided
            if (urlInput.value.trim()) {
                // Show loading state
                const btn = event.target;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sauvegarde...';
                btn.disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    // Show success state
                    btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Sauvegardé!';
                    preview.classList.add('animate__animated', 'animate__tada');
                    
                    setTimeout(() => {
                        btn.innerHTML = '<i class="bi bi-save me-2"></i>Sauvegarder la photo';
                        btn.disabled = false;
                        preview.classList.remove('animate__animated', 'animate__tada');
                        
                        // Show success toast
                        showToast("Photo de profil mise à jour avec succès!", "success");
                    }, 1500);
                }, 1000);
            } 
            // Check if file is selected
            else if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    
                    // Show loading state
                    const btn = event.target;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sauvegarde...';
                    btn.disabled = true;
                    
                    // Simulate upload
                    setTimeout(() => {
                        // Show success state
                        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Sauvegardé!';
                        preview.classList.add('animate__animated', 'animate__tada');
                        
                        setTimeout(() => {
                            btn.innerHTML = '<i class="bi bi-save me-2"></i>Sauvegarder la photo';
                            btn.disabled = false;
                            preview.classList.remove('animate__animated', 'animate__tada');
                            
                            // Show success toast
                            showToast("Photo de profil mise à jour avec succès!", "success");
                        }, 1500);
                    }, 1000);
                };
                
                reader.readAsDataURL(file);
            } else {
                showToast("Veuillez sélectionner une photo ou entrer une URL", "danger");
            }
        }

        // Handle file selection
        document.getElementById('photoFileInput').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('profileImagePreview').src = e.target.result;
                    document.getElementById('photoUrlInput').value = ''; // Clear URL input
                };
                
                reader.readAsDataURL(file);
            }
        });

        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Password validation
        document.getElementById('newPassword').addEventListener('input', function() {
            const password = this.value;
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };
            
            // Update requirement indicators
            document.getElementById('req-length').className = requirements.length ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-uppercase').className = requirements.uppercase ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-lowercase').className = requirements.lowercase ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-number').className = requirements.number ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-special').className = requirements.special ? 'requirement met' : 'requirement unmet';
            
            // Check password match
            checkPasswordMatch();
        });
        
        document.getElementById('confirmPassword').addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorElement = document.getElementById('passwordMatchError');
            
            if (confirmPassword && newPassword !== confirmPassword) {
                errorElement.classList.remove('d-none');
                document.getElementById('confirmPassword').classList.add('is-invalid');
            } else {
                errorElement.classList.add('d-none');
                document.getElementById('confirmPassword').classList.remove('is-invalid');
            }
        }
        
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Validate password requirements
            const requirements = {
                length: newPassword.length >= 8,
                uppercase: /[A-Z]/.test(newPassword),
                lowercase: /[a-z]/.test(newPassword),
                number: /[0-9]/.test(newPassword),
                special: /[^A-Za-z0-9]/.test(newPassword)
            };
            
            const allRequirementsMet = Object.values(requirements).every(Boolean);
            
            if (!allRequirementsMet) {
                alert("Veuillez respecter toutes les exigences de mot de passe");
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert("Les mots de passe ne correspondent pas");
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> En cours...';
            submitBtn.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                // Show success state
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Succès!';
                
                // Reset form after delay
                setTimeout(() => {
                    this.reset();
                    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Mettre à jour';
                    submitBtn.disabled = false;
                    
                    // Show success toast
                    showToast("Mot de passe changé avec succès!", "success");
                }, 1000);
            }, 1500);
        });
        
        function showToast(message, type) {
            const toastContainer = document.createElement('div');
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '11';
            
            const toast = document.createElement('div');
            toast.className = `toast show align-items-center text-white bg-${type}`;
            toast.role = 'alert';
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            document.body.appendChild(toastContainer);
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                toastContainer.remove();
            }, 3000);
        }
        
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const iconSidenav = document.getElementById('iconSidenav');
            if (iconSidenav) {
                iconSidenav.addEventListener('click', function() {
                    document.querySelector('.sidenav').classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>











































































