# Reverse Auction - Symfony 6

Ce projet est une application Symfony de mise aux enchères inversées, avec gestion des rôles (`Client`, `Admin`), des enchères, des portefeuilles et des transactions.

## 🔧 Technologies

- Symfony 6
- Doctrine ORM
- SQLite (modifiable en MySQL/PostgreSQL)
- EasyAdmin pour l’administration
- Bootstrap pour l’interface client

## 📦 Fonctionnalités

- Inscription / Connexion sécurisée (Clients/Admins)
- Mise en enchère inversée avec clôture automatique
- Tableau de bord client avec solde
- Transactions de jetons
- Espace d’administration complet

## 🚀 Lancer le projet

```bash
git clone https://github.com/TON-UTILISATEUR/NOM-DEPOT.git
cd reverse-auction
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
🧪 Admin : /admin
🧪 Client : /login

📁 Dossiers clés
src/Entity : entités Client, Auction, Wallet, etc.

src/Controller : logique côté client et admin

templates/ : vues Twig

👤 Auteur
Imadeddine A. — Juin 2025
