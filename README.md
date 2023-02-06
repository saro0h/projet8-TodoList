ToDoList
========

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

Project requirements :
PHP 7.2


Project installation :
```
git clone https://github.com/jonatanocr/projet8-TodoList.git
          
cd projet8-TodoList

composer install

composer update

php bin/console doctrine:database:create

php bin/console doctrine:schema:update --force

php bin/console server:start
```
