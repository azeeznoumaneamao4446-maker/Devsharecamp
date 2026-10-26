# DevShareCampus

Plateforme de partage de ressources pédagogiques (cours, TD, TP, vidéos) pour les étudiants de L1/L2 en Informatique et Génie Logiciel.

Maîtrise d'œuvre : Dans Dos Services.

## Stack
PHP 8 · MySQL (InnoDB, utf8mb4) · HTML/CSS · JavaScript

## Installation (WampServer)
1. Copier le dossier dans `C:\wamp64\www\DevShareCamp`.
2. Importer `sql/devsharecamp_corrige.sql` dans phpMyAdmin (base `devsharecamp`).
3. Copier `config/db.example.php` en `config/db.php` et adapter les identifiants.
4. Ouvrir `http://localhost/DevShareCamp/`.

## Fonctionnalités
- Inscription / connexion (mots de passe hashés)
- Liste dynamique des matières et ressources, filtres et recherche

## À venir
Page matière, lecteur vidéo, historique, back-office admin (upload).
