<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$old = ['nom' => '', 'prenom' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nom']    = trim($_POST['nom'] ?? '');
    $old['prenom'] = trim($_POST['prenom'] ?? '');
    $old['email']  = trim($_POST['email'] ?? '');
    $password        = $_POST['mot_de_passe'] ?? '';
    $passwordConfirm = $_POST['mot_de_passe_confirm'] ?? '';

    if ($old['nom'] === '' || $old['prenom'] === '') {
        $errors[] = 'Le nom et le prénom sont obligatoires.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if ($password !== $passwordConfirm) {
        $errors[] = 'Les deux mots de passe ne correspondent pas.';
    }

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT num_etd FROM etudiant WHERE email = ?');
        $check->execute([$old['email']]);
        if ($check->fetch()) {
            $errors[] = 'Un compte existe déjà avec cette adresse email.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $pdo->prepare(
            'INSERT INTO etudiant (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)'
        );
        $insert->execute([$old['nom'], $old['prenom'], $old['email'], $hash, 'etudiant']);

        header('Location: login.php?inscription=ok');
        exit;
    }
}

$pageTitle = 'Inscription — DevShareCampus';
require __DIR__ . '/partials/header.php';
?>
        <section class="auth">
            <div class="auth-card">
                <h2 class="auth-title">Créer un compte</h2>
                <p class="auth-subtitle">Accède aux cours, TD, TP et vidéos de ta promo.</p>

                <?php if (!empty($errors)): ?>
                    <ul class="form-errors">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form class="auth-form" method="post" action="register.php" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom</label>
                            <input class="form-input" type="text" id="nom" name="nom"
                                   value="<?= htmlspecialchars($old['nom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="prenom">Prénom</label>
                            <input class="form-input" type="text" id="prenom" name="prenom"
                                   value="<?= htmlspecialchars($old['prenom']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <input class="form-input" type="email" id="email" name="email"
                               value="<?= htmlspecialchars($old['email']) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="mot_de_passe">Mot de passe</label>
                            <input class="form-input" type="password" id="mot_de_passe" name="mot_de_passe"
                                   minlength="8" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="mot_de_passe_confirm">Confirmer</label>
                            <input class="form-input" type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm"
                                   minlength="8" required>
                        </div>
                    </div>

                    <button class="btn btn-solid auth-submit" type="submit">S'inscrire</button>
                </form>

                <p class="auth-switch">Déjà un compte ? <a href="login.php">Se connecter</a></p>
            </div>
        </section>
<?php require __DIR__ . '/partials/footer.php'; ?>
