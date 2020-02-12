# TodoList #
  
![alt text](https://portfolio.damienvalade.fr/img/projects/TodoList.jpg)
  
Projet OpenClassrooms : Améliorez une application existante de ToDo & Co
  
## Informations du projet ##
Projet de la formation ***Développeur d'application - PHP / Symfony***.  
  
**Améliorez une application existante de ToDo & Co** - 
[Lien de la formation](https://openclassrooms.com/fr/paths/59-developpeur-dapplication-php-symfony)  
  
## Badges du projet ##
  
[![Maintainability](https://api.codeclimate.com/v1/badges/cfc4df621746b9690458/maintainability)](https://codeclimate.com/github/damienvalade/OC-P8-TODOLIST/maintainability)

[![Test Coverage](https://api.codeclimate.com/v1/badges/cfc4df621746b9690458/test_coverage)](https://codeclimate.com/github/damienvalade/OC-P8-TODOLIST/test_coverage)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/527c8f25617349fa9ec765d47f789cce)](https://www.codacy.com/manual/damienvalade/OC-P8-TODOLIST?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=damienvalade/OC-P8-TODOLIST&amp;utm_campaign=Badge_Grade)

[![Dependabot](https://badgen.net/badge/Dependabot/enabled/green?icon=dependabot)](https://dependabot.com/)  
  
## Description du besoin ##

### Corrections d'anomalies ###

### Une tâche doit être attachée à un utilisateur ###

Actuellement, lorsqu’une tâche est créée, elle n’est pas rattachée à un utilisateur. Il vous est demandé d’apporter les 
corrections nécessaires afin qu’automatiquement, à la sauvegarde de la tâche, l’utilisateur authentifié soit rattaché à 
la tâche nouvellement créée.

Lors de la modification de la tâche, l’auteur ne peut pas être modifié.

Pour les tâches déjà créées, il faut qu’elles soient rattachées à un utilisateur “anonyme”.
Choisir un rôle pour un utilisateur

Lors de la création d’un utilisateur, il doit être possible de choisir un rôle pour celui-ci. Les rôles listés sont les 
suivants :

  - rôle utilisateur (ROLE_USER) ;
  - rôle administrateur (ROLE_ADMIN).

Lors de la modification d’un utilisateur, il est également possible de changer le rôle d’un utilisateur.

### Implémentation de nouvelles fonctionnalités ###

#### Autorisation ####

Seuls les utilisateurs ayant le rôle administrateur (ROLE_ADMIN) doivent pouvoir accéder aux pages de gestion des 
utilisateurs.

Les tâches ne peuvent être supprimées que par les utilisateurs ayant créé les tâches en question.

Les tâches rattachées à l’utilisateur “anonyme” peuvent être supprimées uniquement par les utilisateurs ayant le 
rôle administrateur (ROLE_ADMIN).

#### Implémentation de tests automatisés ####

Il vous est demandé d’implémenter les tests automatisés (tests unitaires et fonctionnels) nécessaires pour assurer que 
le fonctionnement de l’application est bien en adéquation avec les demandes.

Ces tests doivent être implémentés avec PHPUnit ; vous pouvez aussi utiliser Behat pour la partie fonctionnelle.

Vous prévoirez des données de tests afin de pouvoir prouver le fonctionnement dans les cas explicités dans ce document.

## Installation ##

1. Clonez le repo :
```
    git clone https://github.com/damienvalade/OC-P8-TODOLIST.git
```

2. Modifier le .env avec vos informations.
 
3. Installez les dependances :
```
    composer install
```

4. Mettre en place la BDD :
```
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
```