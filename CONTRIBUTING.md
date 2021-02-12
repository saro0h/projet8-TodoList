Contributing
============

If you want improve this project or add new feature, please follow this instructions :

 - Don't forget to check your code with [PSR-1 and PSR-12.](https://www.php-fig.org/psr/)
    You can use [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) for that. 
 - Follow the [Symfony best practices](https://symfony.com/doc/3.4/best_practices/introduction.html) guideline (version 3.4).
    

## The fundamentals

 1. Fork the project & clone locally.
 2. Create an upstream remote and sync your local copy before you branch.
 3. Branch for each separate piece of work.
 4. Do the work and write good commit messages.
 5. Push to your origin repository.
 6. Create a new PR in GitHub.
 7. Respond to any code review feedback.

## Tests

 - Test are done with [Phpunit](https://phpunit.de/manual/6.5/en/index.html) only.
        
        ./vendor/bin/phpunit

 - code coverage reports goes to web/code-coverage


## What to improve

 - If you want to update to Symfony 4 and flex, do it before the project grows.
 - Add a proper way to deactivate accounts. User should be able to deactivate/delete their own account. 
 - A User should not be able to mark an other user task as done/undone.
 - A task could have assignees. Only assignees or the task owner could mark the task done/undone.
