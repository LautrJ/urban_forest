# 🌳 Urban Forest - La Forêt Urbaine

Application web de recensement participatif du patrimoine arboré communal développée avec Symfony.

## 📋 Table des matières

- [À propos](#à-propos)
- [Fonctionnalités principales](#fonctionnalités-principales)
- [Technologies utilisées](#technologies-utilisées)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Structure du projet](#structure-du-projet)
- [Données de test](#données-de-test)
- [Troubleshooting](#troubleshooting)

## 🎯 À propos

**Urban Forest** est une application web permettant aux municipalités et aux citoyens de collaborer au recensement et à la gestion du patrimoine arboré urbain. L'application intègre une carte interactive, un système de demandes citoyennes, et calcule l'impact environnemental des arbres (stockage CO2, zones de fraîcheur, biodiversité).

### Objectifs du projet

- Recenser la population d'arbres sur une commune
- Permettre aux citoyens de déclarer leurs arbres avec validation par des agents
- Visualiser l'impact environnemental via une carte interactive
- Projeter la croissance future et les zones de fraîcheur

## ✨ Fonctionnalités principales

### Pour les visiteurs
- 🗺️ **Carte interactive** avec filtres (statut, essence, propriétaire)
- 📊 **Statistiques** sur la population d'arbres
- 🌡️ **Visualisation des zones de fraîcheur** avec projection temporelle

### Pour les citoyens
- 📝 **Déclaration d'arbres** avec géolocalisation automatique
- 📸 **Upload de photos** (jusqu'à 3 par arbre)
- 📅 **Prise de rendez-vous** avec les agents municipaux
- 📋 **Suivi de mes demandes**

### Pour les agents municipaux
- ✅ **Validation des demandes** citoyennes
- 👤 **Gestion des rendez-vous**
- 🌳 **Création d'arbres validés**
- 📊 **Dashboard avec statistiques**

### Pour les administrateurs
- 👥 **Gestion des utilisateurs et rôles**
- 🌲 **Gestion du référentiel d'essences d'arbres**
- 📈 **Accès complet aux données**

## 🛠️ Technologies utilisées

- **Backend** : Symfony 6.4 / PHP 8.3
- **Base de données** : PostgreSQL 15 + PostGIS 3.3
- **ORM** : Doctrine ORM 3.5
- **Frontend** : Twig + JavaScript (Leaflet.js pour la carte)
- **Containerisation** : Docker + Docker Compose
- **Outils dev** : Symfony CLI, Adminer

## 📦 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **Docker Desktop** (version 20.10 ou supérieure)
- **Docker Compose** (version 2.0 ou supérieure)
- **Git** (pour cloner le projet)

> **Note** : Tous les outils de développement (PHP, Composer, Symfony CLI, Node.js) sont inclus dans les conteneurs Docker. Aucune installation locale n'est nécessaire !

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/LautrJ/urban_forest.git
cd urban_forest
```

### 2. Créer le fichier .env.local

Copier le fichier `.env` et créer `.env.local` à la racine du projet :

```bash
cp .env .env.local
```

Le fichier `.env` contient déjà la configuration Docker par défaut :

```env
DATABASE_URL="postgresql://symfony_user:symfony_password@database:5432/urban_forest?serverVersion=15.4&charset=utf8"
APP_ENV=dev
APP_SECRET=c201ae15d3139006add072f403052512
```

### 3. Lancer les conteneurs Docker

```bash
docker-compose up -d
```

Cette commande va :
- Télécharger les images Docker nécessaires
- Construire le conteneur de l'application Symfony
- Démarrer PostgreSQL avec PostGIS
- Démarrer Adminer pour gérer la base de données
- Démarrer le serveur Symfony

**Temps de première installation** : 5-10 minutes selon votre connexion internet.

### 4. Installer les dépendances PHP

```bash
docker-compose exec app composer install
```

### 5. Créer la base de données et le schéma

```bash
# Créer la base de données
docker-compose exec app php bin/console doctrine:database:create

# Créer les tables
docker-compose exec app php bin/console doctrine:schema:create
```

### 6. Charger les données de test (fixtures)

```bash
docker-compose exec app php bin/console doctrine:fixtures:load
```

**Important** : Répondez `yes` quand on vous demande de confirmer. Cette commande va créer :
- Des types d'arbres (Chêne, Hêtre, Bouleau, etc.)
- Des statuts d'arbres (En projet, En demande, Validé, Supprimé)
- Des utilisateurs de test
- Des arbres exemples
- Des demandes exemples

## 🌐 Accéder à l'application

Une fois l'installation terminée, vous pouvez accéder à :

| Service | URL | Description |
|---------|-----|-------------|
| **Application Symfony** | http://localhost:8000 | Interface principale |
| **Adminer** | http://localhost:8080 | Interface de gestion BDD |

### Connexion à Adminer

Pour accéder à la base de données via Adminer :

- **Système** : PostgreSQL
- **Serveur** : database
- **Utilisateur** : symfony_user
- **Mot de passe** : symfony_password
- **Base de données** : urban_forest

### Comptes de test

Les fixtures créent automatiquement des comptes de test pour chaque rôle :

| Rôle | Email | Mot de passe | Description |
|------|-------|--------------|-------------|
| **Administrateur** | admin@urban-forest.fr | admin123 | Accès complet |
| **Agent** | agent@urban-forest.fr | agent123 | Validation demandes |
| **Citoyen** | citoyen@urban-forest.fr | citoyen123 | Déclaration arbres |

## ⚙️ Configuration

### Ports utilisés

Le projet utilise les ports suivants :

- **8000** : Application Symfony
- **8080** : Adminer (interface BDD)
- **5432** : PostgreSQL (accessible uniquement depuis les conteneurs)

Si ces ports sont déjà utilisés sur votre machine, vous pouvez les modifier dans `docker-compose.yml`.

### Variables d'environnement

Les principales variables sont dans le fichier `.env` :

```env
# Environnement (dev/prod)
APP_ENV=dev

# Clé secrète Symfony (à changer en production)
APP_SECRET=c201ae15d3139006add072f403052512

# URL de connexion PostgreSQL
DATABASE_URL="postgresql://symfony_user:symfony_password@database:5432/urban_forest?serverVersion=15.4&charset=utf8"

# Configuration emails (optionnel)
MAILER_DSN=null://null
```

## 🎮 Utilisation

### Commandes Docker fréquentes

```bash
# Démarrer les conteneurs
docker-compose up -d

# Arrêter les conteneurs
docker-compose down

# Voir les logs en temps réel
docker-compose logs -f

# Voir les logs d'un service spécifique
docker-compose logs -f app

# Redémarrer un service
docker-compose restart app

# Reconstruire les images
docker-compose build --no-cache
```

### Commandes Symfony dans Docker

Toutes les commandes Symfony doivent être exécutées dans le conteneur :

```bash
# Créer une nouvelle entité
docker-compose exec app php bin/console make:entity

# Créer une migration
docker-compose exec app php bin/console make:migration

# Exécuter les migrations
docker-compose exec app php bin/console doctrine:migrations:migrate

# Charger les fixtures
docker-compose exec app php bin/console doctrine:fixtures:load

# Vider le cache
docker-compose exec app php bin/console cache:clear

# Lister les routes
docker-compose exec app php bin/console debug:router
```

### Accéder au shell du conteneur

```bash
# Bash dans le conteneur app
docker-compose exec app bash

# Psql dans PostgreSQL
docker-compose exec database psql -U symfony_user -d urban_forest
```

## 📁 Structure du projet

```
urban_forest/
├── config/                 # Configuration Symfony
├── docker/                 # Configuration Docker
│   ├── php/
│   │   └── Dockerfile     # Image PHP personnalisée
│   └── postgres/
│       └── init/          # Scripts d'initialisation BDD
├── migrations/            # Migrations Doctrine
├── public/                # Point d'entrée web
│   ├── index.php
│   ├── uploads/          # Photos uploadées
│   └── css/              # Styles CSS
├── src/
│   ├── Controller/       # Contrôleurs
│   ├── Entity/          # Entités Doctrine
│   ├── Form/            # Formulaires Symfony
│   ├── Repository/      # Repositories Doctrine
│   └── DataFixtures/    # Données de test
├── templates/            # Vues Twig
├── var/                 # Cache et logs
├── vendor/              # Dépendances Composer
├── .env                 # Variables d'environnement
├── composer.json        # Dépendances PHP
├── docker-compose.yml   # Orchestration Docker
└── README.md           # Ce fichier
```

## 🧪 Données de test

Les fixtures chargent automatiquement :

### Types d'arbres
- Chêne pédonculé (coefficients de croissance rapide)
- Hêtre commun (croissance moyenne)
- Bouleau verruqueux (croissance rapide)
- Érable champêtre (croissance lente)
- Tilleul à grandes feuilles (croissance moyenne)

### Utilisateurs
- 1 administrateur
- 2 agents municipaux
- 3 citoyens

## 🐛 Troubleshooting

### Les conteneurs ne démarrent pas

```bash
# Vérifier les logs
docker-compose logs

# Arrêter et nettoyer
docker-compose down -v
docker-compose up -d
```

### Erreur "port already in use"

Un autre service utilise les ports 8000, 8080 ou 5432. Modifiez les ports dans `docker-compose.yml` :

```yaml
services:
  app:
    ports:
      - "8001:8000"  # Changer 8000 par 8001
```

### La base de données ne se crée pas

```bash
# Vérifier que PostgreSQL est bien démarré
docker-compose ps

# Se connecter à PostgreSQL
docker-compose exec database psql -U symfony_user

# Dans psql :
\l  # Lister les bases
\q  # Quitter
```

### Les fixtures ne se chargent pas

```bash
# Supprimer et recréer la base
docker-compose exec app php bin/console doctrine:database:drop --force
docker-compose exec app php bin/console doctrine:database:create
docker-compose exec app php bin/console doctrine:schema:create
docker-compose exec app php bin/console doctrine:fixtures:load
```

### Erreur "Class not found"

```bash
# Régénérer l'autoloader
docker-compose exec app composer dump-autoload

# Vider le cache
docker-compose exec app php bin/console cache:clear
```

### Les photos ne s'affichent pas

Vérifier les permissions du dossier uploads :

```bash
docker-compose exec app chmod -R 777 public/uploads
```

### Réinitialisation complète

Pour tout réinitialiser (⚠️ perte de données) :

```bash
# Arrêter et supprimer tout
docker-compose down -v

# Supprimer le dossier vendor
rm -rf vendor/

# Supprimer les migrations (optionnel)
rm -rf migrations/*.php

# Relancer l'installation complète
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php bin/console doctrine:database:create
docker-compose exec app php bin/console doctrine:schema:create
docker-compose exec app php bin/console doctrine:fixtures:load
```

## 📚 Documentation complémentaire

- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Documentation Doctrine](https://www.doctrine-project.org/)
- [Documentation Leaflet.js](https://leafletjs.com/)
- [Documentation PostGIS](https://postgis.net/documentation/)

## 👨‍💻 Auteur

**Jean Lautraite** - Projet étudiant ESI-1 2024-2025

## 📝 Licence

Ce projet est un projet étudiant réalisé dans le cadre d'une formation à l'Institut Limayrac.

---

**Besoin d'aide ?** Consultez la section [Troubleshooting](#troubleshooting) ou contactez l'auteur.