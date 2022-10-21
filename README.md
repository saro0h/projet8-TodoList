# Améliorez une application existante de ToDo & Co


[![SymfonyInsight](https://insight.symfony.com/projects/7a29dd28-b529-4a40-aa4f-cb4d5e780faf/big.svg)](https://insight.symfony.com/projects/7a29dd28-b529-4a40-aa4f-cb4d5e780faf)


This project is deployed on 

````
https://todolist.hajbensalem.fr

````


# Initialize project locally

## Versions
* PHP 8.1.6
* Symfony 6.1.4
* Doctrine 2.7.1
* mariadb-10.4.24

## Requirement
* PHP
* Symfony 
* Composer


## Installation Steps

1. Fork the project repository from 

````
https://github.com/knouz15/projet8-TodoList

````

2. Lancer la commande suivante pour installer les dépendances du projet 

````
composer install

````

3. Modifiez le fichier .env avec vos parametres de BDD

````
DATABASE_URL="mysql://root:password@127.0.0.1:3306/nom_de_la_db" 

````

4. Créer la base de données

````
php bin/console doctrine:database:create

````

5. Forcer la synchronisation de la base de donnée

````
php bin/console doctrine:schema:update --force

````
ou Créez les différentes tables de la base de données en appliquant les migrations :

    php bin/console doctrine:migrations:migrate


6. Pour démarrer le projet avec un jeu de donnée de test, lancer simplement cette commande :

````
bin/console doctrine:fixtures:load 

````

7. Tester le projet avec PHPUnit en lancçant la console à la racine du projet avec la commande

````
vendor/bin/phpunit

````

8. Lancer le serveur local via la commande symfony

````
symfony serve

````

9. Accéder à l'URL : https://127.0.0.1:8000

ou

en exécutant la commande suivante : cd public && php -S localhost:8000


10. Pour voir le taux de couverture de code, il suffit d'accéder à la route suivante :

http://{SERVER_NAME}/test-coverage/index.html

où {SERVER_NAME} est le nom du domaine de l'application.

Ex : http://localhost:8000/nom_projet/public/test-coverage/index.html

11. Si vous souhaitez générer à nouveau le rapport de taux de couverture de code, il suffit de lancer la commande qui suit :

vendor/bin/phpunit --coverage-html public/test-coverage

Puis accéder à nouveau à l'URL ci-dessus pour voir le nouveau rapport généré.
Contribuer au projet

12. Pour contribuer au projet, vous trouverez dans les sources un fichier qui se nomme CONTRIBUTING.MD . 
Il contient les instructions et les pratiques permettant de contribuer au projet.

