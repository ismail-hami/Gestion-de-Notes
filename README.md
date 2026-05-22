# Gestion de Notes

> Application web de gestion de notes étudiants — PHP/Symfony 7 & MySQL

## 📖 About
Application web permettant de gérer les notes des étudiants,
les modules et les coefficients. Développée avec Symfony 7 et MySQL.

## ✨ Features
- Affichage des notes par étudiant
- Gestion des modules et coefficients
- Base de données relationnelle

## 🛠️ Tech Stack
- PHP / Symfony 7
- MySQL / Doctrine ORM
- Twig

## 🚀 Installation
git clone https://github.com/yourname/gestionnotes.git
cd gestionnotes
composer install

# Setup .env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/gestionnotes"

php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
symfony server:start

## 👤 Author
**Ismail Hami**
- GitHub: https://github.com/ismail-hami
- LinkedIn: https://www.linkedin.com/in/ismail-hami-53bba1370/
