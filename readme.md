[![Codacy Badge](https://app.codacy.com/project/badge/Grade/f2852b96fc6346babb25b88ae73f0ca5)](https://www.codacy.com/gh/nvendeville/P8_ToDoList/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nvendeville/P8_ToDoList&amp;utm_campaign=Badge_Grade)

# To Do List
Une application vous permettant de gérer l'ensemble de vos tâches sans effort

## Prerequest
Composer https://getcomposer.org/download/

## Install and run the application

- **Step 1** : In your Terminal run ``git clone https://github.com/nvendeville/P8_ToDoList.git``

- **Step 2** : In your Terminal run ``cd P8_todolist``

- **Step 3** : In your Terminal run the command ``composer install``

- **Step 4** : Rename the file **.env.dist** to **.env**

- **Step 5** : Choose a name for your DataBase

- **Step 6** : Update ``###> doctrine/doctrine-bundle ###`` in your file **.env**

    - Uncomment the ligne related to your SGBQ

      DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db" **for sqlite**
      DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name" **for mysql**
      DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8" **for postgresql**
      DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=mariadb-10.5.8" **for mariadb**
      DATABASE_URL="oci8://db_user:db_password@127.0.0.1:1521/db_name **for oracle**

    - Set the db_user and/or db_password and/or db_name (name chosen on step 4)

- **Step 7** : In your Terminal, create and set your database
    - Run ``php bin/console doctrine:database:create`` give the name chosen on step 4
    - Run ``php bin/console make:migration``
    - Run ``php bin/console doctrine:migrations:migrate``

- **Step 8** : In your Terminal, load the available set of data
    - Run ``php bin/console doctrine:fixtures:load``
    - Available data :
        - 1 user with username "user1" with ROLE_ADMIN
        - 1 user with username "user2" with ROLE_USER
        - 1 user with username "anonymous" with ROLE_ADMIN
          (These 3 created users have "coucou" as password)
        - 3 task for each created user
          (These tasks will be marked as "done" randomly)

- **Step 9** : In your Terminal run the command ``symfony serve``

- **Step 10** : From your browser go to http://locahost:8000. This will route you to the application homepage.

## Contribute to the application

### Prerequests to any new commit
- **Code quality and best practices** :
    - **Code quality** :
  
  Please consider the official recommendations to manage your **code style** by consulting https://www.php-fig.org/psr/
  To help you to check your code, you can register your repository to Codacy. This tool will run several linters to check your code.
  today, the reached badge is A, please don't commit any codes if the result in Codacy is given under this level.
    - **Best practices** :
  
  Please apply Synmfony best practices mentioned in https://symfony.com/doc/current/best_practices.html for any new features

- **PHPUnit**

  The application is covered by functional and unit tests. Please cover any new features with tests and ensure the test coverage of the application does not fall below 90%

### Push a commit
  - No commit are allowed to the master branch. Create a new branch before any commit.
  - After committing, create a pull request.
  - No pull request will be merged without a review.
