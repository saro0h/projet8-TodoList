# Project 8 - OpenclassRooms
**Name of the project :** Améliorez une application existante de ToDo & Co (Improve an existing ToDo & Co application).

## Installation

### - Step 1  
Make sure you have Git installed and up to date on your machine
www.git-scm.com  

### - Step 2
Clone the repository on your local server
``git clone https://github.com/dokaNc/Projet8-OC.git``

### - Step 3
Make sure that composer is installed and up to date on your machine
www.getcomposer.org/doc/00-intro.md 

### - Step 4
After installing composer, please launch ``composer install`` at the root of your project.  
All dependencies will be installed and stored in the folder **/vendor**.

### - Step 5
Modify the accesses to your database in the file `.env [DATABASE_URL] (l.28)`

### - Step 6
Create the database using running the following command: `php bin/console doctrine:database:create`

### - Step 7
Create the tables in the database using running the following command: `php bin/console doctrine:schema:create`

### - Step 8
Install the data fixtures in order to be able to interact with the site. Using this command: `php bin/console doctrine:fixtures:load`

### - Step 9
If you are locally, you can start the Symfony server with the command: `php -S 127.0.0.1:8080 -t public
`

### - Step 10
If you are installing the project on a server, do not forget to point your domain to the file `/public`

### - Step 11
Here are the credentials of the administrator account to be able to add new users and / or tasks.

- Email : admin@email.com
- Password : pass


