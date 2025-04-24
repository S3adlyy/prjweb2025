<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Utilisateur.php';
require_once __DIR__ . '/../../controllers/UtilisateurController.php';

session_start();

// Redirect if not admin
if (!isset($_SESSION['user'])) {
    header('Location: /prjweb2025/views/front/login2.php');
    exit();
}

$pdo = Config::getConnexion();
$controller = new UtilisateurController($pdo);

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateur = new Utilisateur(
                null,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role'],
                'actif',
                date('Y-m-d H:i:s'),
                $_POST['photoUrl'] ?? null
            );
            
            try {
                $controller->ajouterUtilisateur($utilisateur);
                $_SESSION['success'] = "Utilisateur ajouté avec succès!";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: dashboard.php');
            exit;
        }
        break;
        
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateur = new Utilisateur(
                $id,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['password'] ? $_POST['password'] : null,
                $_POST['role'],
                $_POST['statut'],
                $_POST['dateInscription'],
                $_POST['photoUrl'] ?? null
            );
            
            try {
                $controller->updateUtilisateur($utilisateur);
                $_SESSION['success'] = "Utilisateur modifié avec succès!";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: dashboard.php');
            exit;
        }
        break;
        
    case 'delete':
        try {
            if ($controller->deleteUtilisateur($id)) {
                $_SESSION['success'] = "Utilisateur supprimé avec succès!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: dashboard.php');
        exit;
        
    case 'toggleStatus':
        $user = $controller->getUtilisateur($id);
        if ($user) {
            $newStatus = $user->getStatut() === 'actif' ? 'inactif' : 'actif';
            $utilisateur = new Utilisateur(
                $id,
                $user->getNom(),
                $user->getPrenom(),
                $user->getEmail(),
                $user->getMotDePasse(),
                $user->getRole(),
                $newStatus,
                $user->getDateInscription(),
                $user->getPhotoUrl()
            );
            
            try {
                $controller->updateUtilisateur($utilisateur);
                $_SESSION['success'] = "Statut utilisateur modifié avec succès!";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        header('Location: dashboard.php');
        exit;
}

$utilisateurs = $controller->getAllUtilisateurs();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Gestion Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa !important;
            font-family: 'Poppins', sans-serif;
            color: black !important;
        }

        :root {
            --primary-dark: #0a1d37;
            --accent-blue: #4da6ff;
            --dark-blue: #001f3f;
        }

        .sidenav {
            background-color: var(--primary-dark) !important;
            height: 100vh;
            position: fixed;
            width: 250px;
        }

        .navbar-main {
            background-color: var(--dark-blue) !important;
            border-bottom: 2px solid var(--accent-blue) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(195deg, var(--accent-blue), #3a8df1) !important;
        }

        .btn-primary {
            background-color: var(--dark-blue) !important;
        }

        .badge.bg-success {
            background-color: var(--accent-blue) !important;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .sidenav {
                width: 0;
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
        }

        .logo-container {
            width: 50px;
            height: 50px;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            overflow: hidden;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .strength-0 { width: 0%; background-color: #dc3545; }
        .strength-1 { width: 25%; background-color: #ff6b6b; }
        .strength-2 { width: 50%; background-color: #ffc107; }
        .strength-3 { width: 75%; background-color: #4da6ff; }
        .strength-4 { width: 100%; background-color: #28a745; }

        .sidenav {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 280px;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 20px 0;
    box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}



.admin-title {
    padding: 25px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.admin-title h4 {
    color: white;
    font-weight: 600;
    margin: 0;
    font-size: 1.2rem;
    letter-spacing: 0.5px;
}

.nav-links {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 20px 0;
    overflow-y: auto;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 15px 25px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    margin: 5px 0;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.05);
    color: white;
    border-left: 4px solid #3498db;
}

.nav-link.active {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-left: 4px solid #3498db;
}

.nav-link i {
    font-size: 1.2rem;
    margin-right: 15px;
    width: 20px;
    text-align: center;
}

.nav-link span {
    font-size: 0.95rem;
}


.logout-link {
    color: rgba(255, 255, 255, 0.6);
}

.logout-link:hover {
    color: #e74c3c;
    border-left: 4px solid #e74c3c;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .sidenav {
        width: 80px;
        overflow: hidden;
    }
    
    .logo-container, .admin-title, .nav-link span {
        display: none;
    }
    
    .nav-link {
        justify-content: center;
        padding: 20px 0;
    }
    
    .nav-link i {
        margin-right: 0;
        font-size: 1.4rem;
    }
}
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <div class="sidenav">
    <!-- Logo -->
    <div class="logo-container">
            <img src="../front/images/logo2.png" alt="Logo EasyParki">
    </div>

    <div class="admin-title">
        <h4>Admin Dashboard</h4>
    </div>
    
    <nav class="nav-links">
        <a href="dashboard.php" class="nav-link active">
            <i class="bi bi-people"></i>
            <span>Gestion Utilisateurs</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-car-front"></i>
            <span>Gestion Véhicules</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-calendar-event"></i>
            <span>Réservations</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-gear"></i>
            <span>Paramètres</span>
        </a>
        
        <div class="logout-container">
            <a href="/prjweb2025/views/front/login2.php" class="nav-link logout-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </nav>
</div>

    <!-- Main content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Gestion des Utilisateurs</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-plus-circle"></i> Ajouter
                            </button>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $_SESSION['success'] ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= $_SESSION['error'] ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>
                            
                            <div class="search-box mb-4">
                                <i class="bi bi-search"></i>
                                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un utilisateur...">
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="usersTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Photo</th>
                                            <th>Nom & Prénom</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Statut</th>
                                            <th>Date Inscription</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($utilisateurs && is_array($utilisateurs)): ?>
                                            <?php foreach ($utilisateurs as $user): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user['ID_Utilisateur'] ?? '') ?></td>
                                                    <td>
                                                        <img src="<?= htmlspecialchars($user['Photo_URL'] ?? '/prjweb2025/assets/images/default-user.png') ?>" 
                                                             class="user-img" 
                                                             alt="Photo utilisateur">
                                                    </td>
                                                    <td><?= htmlspecialchars(($user['Nom'] ?? '') . ' ' . ($user['Prenom'] ?? '')) ?></td>
                                                    <td><?= htmlspecialchars($user['Email'] ?? '') ?></td>
                                                    <td>
                                                        <?= htmlspecialchars(ucfirst($user['Role'] ?? '')) ?>
                                                        <i class="bi bi-<?= 
                                                            ($user['Role'] ?? '') === 'admin' ? 'person-gear' : 
                                                            (($user['Role'] ?? '') === 'chauffeur' ? 'person-workspace' : 'person') 
                                                        ?>"></i>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= ($user['Statut'] ?? '') === 'actif' ? 'success' : 'secondary' ?>">
                                                            <?= htmlspecialchars(ucfirst($user['Statut'] ?? '')) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($user['Date_inscription'] ?? '') ?></td>
                                                    <td>
                                                        <!-- Edit Button -->
                                                        <button class="btn btn-sm btn-warning me-2" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editUserModal"
                                                                data-id="<?= $user['ID_Utilisateur'] ?? '' ?>"
                                                                data-nom="<?= htmlspecialchars($user['Nom'] ?? '') ?>"
                                                                data-prenom="<?= htmlspecialchars($user['Prenom'] ?? '') ?>"
                                                                data-email="<?= htmlspecialchars($user['Email'] ?? '') ?>"
                                                                data-role="<?= htmlspecialchars($user['Role'] ?? '') ?>"
                                                                data-statut="<?= htmlspecialchars($user['Statut'] ?? '') ?>"
                                                                data-photo="<?= htmlspecialchars($user['Photo_URL'] ?? '') ?>"
                                                                data-date="<?= htmlspecialchars($user['Date_inscription'] ?? '') ?>">
                                                            <i class="bi bi-pencil"></i> Modifier
                                                        </button>
                                                        
                                                        <!-- Delete Button -->
                                                        <a href="dashboard.php?action=delete&id=<?= $user['ID_Utilisateur'] ?? '' ?>" 
                                                           class="btn btn-sm btn-danger me-2"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </a>
                                                        
                                                        <!-- Toggle Status Button -->
                                                        <a href="dashboard.php?action=toggleStatus&id=<?= $user['ID_Utilisateur'] ?? '' ?>" 
                                                           class="btn btn-sm btn-<?= ($user['Statut'] ?? '') === 'actif' ? 'secondary' : 'success' ?>">
                                                            <i class="bi bi-<?= ($user['Statut'] ?? '') === 'actif' ? 'x-circle' : 'check-circle' ?>"></i>
                                                            <?= ($user['Statut'] ?? '') === 'actif' ? 'Désactiver' : 'Activer' ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Aucun utilisateur trouvé</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="dashboard.php?action=add" id="addUserForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="addNom" class="form-label">Nom*</label>
                                <input type="text" class="form-control" id="addNom" name="nom" required>
                                <div class="invalid-feedback">Veuillez entrer un nom valide</div>
                            </div>
                            <div class="mb-3">
                                <label for="addPrenom" class="form-label">Prénom*</label>
                                <input type="text" class="form-control" id="addPrenom" name="prenom" required>
                                <div class="invalid-feedback">Veuillez entrer un prénom valide</div>
                            </div>
                            <div class="mb-3">
                                <label for="addEmail" class="form-label">Email*</label>
                                <input type="email" class="form-control" id="addEmail" name="email" required>
                                <div class="invalid-feedback" id="emailFeedback">Veuillez entrer un email valide</div>
                            </div>
                            <div class="mb-3">
                                <label for="addPassword" class="form-label">Mot de passe*</label>
                                <input type="password" class="form-control" id="addPassword" name="password" required>
                                <div class="password-strength strength-0" id="passwordStrength"></div>
                                <div class="invalid-feedback" id="passwordFeedback">Veuillez entrer un mot de passe valide</div>
                            </div>
                            <div class="mb-3">
                                <label for="addRole" class="form-label">Rôle*</label>
                                <select class="form-select" id="addRole" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="chauffeur">Chauffeur</option>
                                    <option value="client" selected>Client</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="addPhoto" class="form-label">URL Photo (optionnel)</label>
                                <input type="url" class="form-control" id="addPhoto" name="photoUrl" placeholder="https://example.com/image.jpg">
                                <div class="invalid-feedback">Veuillez entrer une URL valide</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="dashboard.php?action=edit&id=0" id="editForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="editId">
                            <div class="mb-3">
                                <label for="editNom" class="form-label">Nom*</label>
                                <input type="text" class="form-control" id="editNom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPrenom" class="form-label">Prénom*</label>
                                <input type="text" class="form-control" id="editPrenom" name="prenom" required>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email*</label>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                <input type="password" class="form-control" id="editPassword" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="editRole" class="form-label">Rôle*</label>
                                <select class="form-select" id="editRole" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="chauffeur">Chauffeur</option>
                                    <option value="client">Client</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editStatut" class="form-label">Statut*</label>
                                <select class="form-select" id="editStatut" name="statut" required>
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editPhoto" class="form-label">URL Photo (optionnel)</label>
                                <input type="url" class="form-control" id="editPhoto" name="photoUrl">
                            </div>
                            <input type="hidden" name="dateInscription" id="editDateInscription">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[2].textContent.toLowerCase();
                const email = row.cells[3].textContent.toLowerCase();
                
                if (name.includes(searchValue) || email.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Edit modal setup
        const editModal = document.getElementById('editUserModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const form = document.getElementById('editForm');
                
                // Update form action with user ID
                const userId = button.getAttribute('data-id');
                form.action = `dashboard.php?action=edit&id=${userId}`;
                
                // Fill form fields
                document.getElementById('editId').value = userId;
                document.getElementById('editNom').value = button.getAttribute('data-nom');
                document.getElementById('editPrenom').value = button.getAttribute('data-prenom');
                document.getElementById('editEmail').value = button.getAttribute('data-email');
                document.getElementById('editRole').value = button.getAttribute('data-role');
                document.getElementById('editStatut').value = button.getAttribute('data-statut');
                document.getElementById('editPhoto').value = button.getAttribute('data-photo');
                document.getElementById('editDateInscription').value = button.getAttribute('data-date');
            });
        }

        // Password validation
        document.getElementById('addPassword').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const feedback = document.getElementById('passwordFeedback');
            
            const checks = {
                length: password.length >= 6,
                number: /\d/.test(password),
                upper: /[A-Z]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };
            
            const strength = Object.values(checks).filter(Boolean).length;
            strengthBar.className = 'password-strength strength-' + strength;
            
            if (strength < 4) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                
                if (!checks.length) {
                    feedback.textContent = "Le mot de passe doit contenir au moins 6 caractères";
                } else if (!checks.number) {
                    feedback.textContent = "Le mot de passe doit contenir au moins un chiffre";
                } else if (!checks.upper) {
                    feedback.textContent = "Le mot de passe doit contenir au moins une majuscule";
                } else if (!checks.special) {
                    feedback.textContent = "Le mot de passe doit contenir au moins un caractère spécial";
                }
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                feedback.textContent = "";
            }
        });
    </script>
</body>
</html>