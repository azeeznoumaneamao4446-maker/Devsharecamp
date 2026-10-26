<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$email = '';
$justRegistered = isset($_GET['inscription']) && $_GET['inscription'] === 'ok';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Merci de renseigner ton email et ton mot de passe.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE email = ?');
        $stmt->execute([$email]);
        $etudiant = $stmt->fetch();

        if (!$etudiant || !password_verify($password, $etudiant['mot_de_passe'])) {
            $errors[] = 'Email ou mot de passe incorrect.';
        } else {
            $_SESSION['user'] = [
                'num_etd' => $etudiant['num_etd'],
                'nom'     => $etudiant['nom'],
                'prenom'  => $etudiant['prenom'],
                'email'   => $etudiant['email'],
                'role'    => $etudiant['role'],
            ];
            header('Location: index.php');
            exit;
        }
    }
}

$pageTitle = 'Connexion — DevShareCampus';
require __DIR__ . '/partials/header.php';
?>
        <section class="auth">
            <div class="auth-card">
                <h2 class="auth-title">Se connecter</h2>
                <p class="auth-subtitle">Retrouve tes cours, TD, TP et vidéos.</p>

                <?php if ($justRegistered): ?>
                    <p class="form-success">Compte créé avec succès — tu peux te connecter.</p>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <ul class="form-errors">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form class="auth-form" method="post" action="login.php" novalidate>
                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <input class="form-input" type="email" id="email" name="email"
                               value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="mot_de_passe">Mot de passe</label>
                        <input class="form-input" type="password" id="mot_de_passe" name="mot_de_passe" required>
                    </div>

                    <button class="btn btn-solid auth-submit" type="submit">Se connecter</button>
                </form>

                <p class="auth-switch">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
            </div>
        </section>
<?php require __DIR__ . '/partials/footer.php'; ?>
