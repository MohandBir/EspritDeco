# 🛒 Esprit Déco

Site e-commerce de décoration intérieure développé avec Symfony 7.
Gestion complète du catalogue, panier, commandes et paiement en ligne.

## 🛠️ Technologies

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-000000?style=flat&logo=symfony&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat&logo=bootstrap&logoColor=white)
![Stripe](https://img.shields.io/badge/Stripe-008CDD?style=flat&logo=stripe&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white)

## ✨ Fonctionnalités

### 🟢 Front Office
- Catalogue produits responsive (1 / 2 / 4 cartes selon écran)
- Fiche produit avec carrousel d'images
- Inscription avec vérification par email
- Système de panier (session + BDD selon connexion)
- Filtres par catégorie et recherche par mots-clés
- Tunnel de vente complet avec paiement **Stripe**

### 🟡 Back Office (Admin)
- Tableau de bord administrateur
- CRUD produits complet
- Upload et gestion d'images multiples (AJAX)
- Gestion des commandes et changement de statut
- Gestion des catégories et des utilisateurs

## 🚀 Installation
### 1. Prérequis — Docker Desktop

> Docker Desktop est nécessaire pour lancer le projet.

**Windows :**
- Téléchargez Docker Desktop : https://www.docker.com/products/docker-desktop
- Lancez l'installeur et suivez les étapes
- Redémarrez votre machine si demandé
- Vérifiez l'installation :
```bash
docker --version
```

**Linux (Ubuntu) :**
```bash
sudo apt update
sudo apt install docker.io docker-compose -y
sudo systemctl start docker
```

---

### 2. Cloner le projet

```bash
git clone https://github.com/MohandBir/Voyage
cd liste-voyages
```

### 3. Configurer l'environnement

Copiez le fichier `.env` :
```bash
cp .env .env.local
```

Modifiez `.env.local` :
```env
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```

### 4. Lancer le projet avec Docker

```bash
docker compose up -d
```

### 5. Installer les dépendances et initialiser la base de données

```bash
docker exec -it ecom_php sh
php composer install
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### 6. Accéder à l'application

Ouvrez votre navigateur : http://localhost:8080

---

## 📁 Structure

```
src/
├── Controller/    # ProductController, CartController, OrderController...
├── Entity/        # Product, Category, Image, Cart, CartLine, Order, OrderLine
├── Form/
├── Repository/
└── DataFixtures/
```