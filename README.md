# ToDoList

==========

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

## But

Améliorer la qualité de l'application, en charge des tâches suivantes :

-   l'implémentation de nouvelles fonctionnalités ;
-   la correction de quelques anomalies ;
-   et l'implémentation de tests automatisés ;
-   analyse du projet à l'aide d'outils;
-   si possible réduire la dette technique de l'application.

### Correction d'anomalies

Une tâche doit être attachée à un utilisateur :

-   lors de la création ;
-   auteur non modifiable lors d'une modification ;
-   auteur "anonyme" pour tâches dékà existantes.

Rôle des utilisateur :

-   choix entre ROLE_USER & ROLE_ADMIN lors de la création d'un utilisateur ;
-   role modifiable lors de la modification.

### Implémentation de nouvelles fonctionnalités

-   gestion des utilisateurs UNIQUEMENT par ROLE_ADMIN ;
-   une tâche ne peut être supprimée que par son auteur ou un admin ;
-   tâche "anonyme" peut être modifiée/supprimée que par un admin.

### Implémentation de tests automatisés

-   doivent être implémentés avec PHPUnit ;
-   prévoir des données de tests ;
-   fournir un rapport de couverture de code :

```
lien à venir
```

### Documentation technique

-   expliquer l'implémentation de l'authentification ;
-   expliquer quels fichiers sont modifiable et pourquoi ;
-   comment s'opère l'authentification ;
-   où sont stockés les utilisateurs.

Ajouter aussi un document expliquant comment doivent procéder les développeurs
souhaitant apporter des modifications au projet et détailler le processus de
qualité à utiliser ainsi que les règles à respecter.

### Audit de qualité du code & performance de l'application

Produire un audit de code sur les deux axes suivants (avant & après modifications):

-   la qualité du code à l'aide de Codacy :

```
lien à venir
```

-   la performance à l'aide d'un outils de profiling comme Blackfire :

```
lien à venir
```

## Installation

Cloner le repository :

```
https://github.com/F-Jean/projet8-TodoList.git
cd projet8-TodoList
```

Mettre à jour les dépendances :

```
composer update
```

## Configuration

Créer un fichier `.env.local` :

```
DATABASE_URL=mysql://root:password@127.0.0.1/todolist_v1
```

Créer la base de données :

```
php bin/console doctrine:database:create
```

Installer les fixtures et mettre à jour la base de données :

```
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## Démarer le serveur et aller sur le site

```
symfony server:start -d
https://127.0.0.1:8000
```

## Environnement de test

Créer un fichier `.env.test.local` :

```
DATABASE_URL=mysql://root:password@127.0.0.1/todolist_v1_test
```

Créer la base de données, installer les fixtures et mettre à jour la base de données de test :

```
composer database-test
```

### Lancer les tests

phpstan :

```
vendor/bin/phpstan analyse -c phpstan.neon
```

phpunit :

```
php bin/phpunit
```
