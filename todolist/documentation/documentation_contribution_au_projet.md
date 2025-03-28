# Guide de contribution

Ce document détaille les étapes à suivre pour contribuer au projet Todolist.

## Sommaire
- [Guide de contribution](#guide-de-contribution)
  - [Sommaire](#sommaire)
  - [Prérequis](#prérequis)
  - [Cloner le projet](#cloner-le-projet)
  - [Installation et configuration](#installation-et-configuration)
  - [Workflow de contribution](#workflow-de-contribution)
    - [1. Créer une issue](#1-créer-une-issue)
    - [2. Créer une branche](#2-créer-une-branche)
    - [3. Développer votre contribution](#3-développer-votre-contribution)
    - [4. Tester votre code](#4-tester-votre-code)
      - [Tests unitaires](#tests-unitaires)
      - [Tests fonctionnels](#tests-fonctionnels)
      - [Assurance qualité](#assurance-qualité)
    - [5. Créer une Pull Request](#5-créer-une-pull-request)

## Prérequis

Pour contribuer au projet, vous aurez besoin de :

- PHP 8.2 ou supérieur
- Composer
- Git
- Un système de gestion de base de données
- un serveur web, par exemple Apache avec Wamp

## Cloner le projet

1. Clonez le projet en local :

```bash
git clone https://github.com/AurelienDemblans/todolist2.git
cd todolist2/todolist
```

## Installation et configuration

1. Installez les dépendances :

```bash
composer install
```

2. dans le fichier `.env` configurez vos variables d'environnement :


3. Configurez votre base de données dans `.env` :

```
DATABASE_URL="mysql://utilisateur:mot_de_passe@127.0.0.1:3306/nom_base_de_donnees?serverVersion=8.0"
```

5. Créez la base de données de dev et exécutez les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. Créez la base de données de test et exécutez les migrations :

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update -force --env=test
```

7. Chargez les fixtures (données de test) :

```bash
php bin/console doctrine:fixtures:load
```

7. Lancez le serveur de développement :

```bash
symfony server:start
```

## Workflow de contribution

### 1. Créer une issue

Avant de commencer à travailler sur une fonctionnalité ou un bug, créez une issue sur GitHub :

1. Accédez à l'onglet "Issues" du dépôt principal
2. Cliquez sur "New issue"
3. Sélectionnez le type d'issue approprié (bug, enhancement, etc.)
4. Remplissez le template avec :
   - Un titre clair et concis
   - Si nécessaire ajoutez : 
     - Une description détaillée
     - Des étapes pour reproduire le problème (pour les bugs)
     - Le comportement attendu
     - Des captures d'écran si nécessaire

### 2. Créer une branche

Créez une branche pour travailler sur votre issue.
Récupérez cette branche en local et mettez vous dessus :

```bash
git fetch origin
git checkout nom-de-votre-branche
```

### 3. Développer votre contribution

Pendant le développement :

- Créez des commits avec des messages clairs
- Commentez votre code lorsque nécessaire
- Documentez les nouvelles fonctionnalités

### 4. Tester votre code

#### Tests unitaires

Utilisez PHPUnit pour écrire des tests unitaires pour chaque service ou classe que vous créez :

```bash
# Exécuter tous les tests unitaires
php bin/phpunit tests/Unit

# Exécuter un test spécifique
php bin/phpunit tests/Unit/VotreTest.php
```

Exemple de test unitaire pour un service :

```php
namespace App\Tests\Unit\Service;

use App\Service\MonService;
use PHPUnit\Framework\TestCase;

class MonServiceTest extends TestCase
{
    public function testMaFonction(): void
    {
        $service = new MonService();
        $resultat = $service->maFonction('paramètre');
        
        $this->assertEquals('résultat attendu', $resultat);
    }
}
```

#### Tests fonctionnels
Si votre contribution est compliqué à tester unitairement ou simplement pour vérifier le bon fonctionnement de la fonctionnalité que vous venez de créer dans son ensemble vous pouvez ajouter des tests fonctionnels.
Les tests fonctionnels vérifient le comportement de l'application de bout en bout :

```bash
# Exécuter tous les tests fonctionnels
php bin/phpunit tests/Functional

# Exécuter un test spécifique
php bin/phpunit tests/Functional/Controller/VotreControllerTest.php
```

Exemple de test fonctionnel pour un contrôleur :

Voir les tests fonctionnels déjà présent dans l'application.

#### Assurance qualité

Avant de soumettre votre PR, exécutez :

```bash
# PHPStan pour l'analyse statique ou utiliser Codacy pour vérifier la qualité du code
php vendor/bin/phpstan analyse src tests

# PHP CS Fixer pour le style de code
php vendor/bin/php-cs-fixer fix

# Tous les tests
php bin/phpunit
```

### 5. Créer une Pull Request

Une fois votre travail terminé :

1. Poussez votre branche sur le dépot distant :

```bash
git push nom-de-votre-branche
```

2. Allez sur GitHub et créez une Pull Request (PR) depuis votre branche vers la branche principale du dépôt d'origine.

3. Remplissez le template de PR avec :
   - Une référence à l'issue correspondante (`Fixes #123`)
   - Une description détaillée des changements
   - Les étapes pour tester vos modifications

4. Attendez la revue de code et répondez aux commentaires.

5. Si des modifications sont demandées, mettez à jour votre branche
