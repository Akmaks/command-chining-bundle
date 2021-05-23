# CommandChainingBundle

Bundle provides some functional for calling console commands in chain

## Installation
1. Install package
```
composer require akmaks/command-chaining-bundle
```
2. Create file config/packages/akmaks_command_chaining.yaml and configure your chains
```
parameters:
    chains:
        App\UI\Console\FirstMainCommand:
            - App\UI\ConsoleSecondCommand
            - App\UI\ConsoleThirdCommand
            - App\UI\ConsoleSecondCommand
        App\UI\Console\SecondMainCommand:
            - App\UI\ConsoleSecondCommand
            - App\UI\ConsoleThirdCommand
            - App\UI\ConsoleSecondCommand
```
3. Implements CommandChainingInterface in chain commands

For master commands:
```
class CreateCommand extends Command implements CommandChainingInterface
...
    public function isMasterCommand(): bool
    {
        return true;
    }
```

For chain commands:
```
class ConsoleSecondCommand extends Command implements CommandChainingInterface
...
    public function isMasterCommand(): bool
    {
        return false;
    }
...
```
4. Check result:
```
bin/console app:first:main
[2021-05-25 21:07:25]: app:first:main is a master command of a command chain that has registered member commands
[2021-05-25 21:07:25]: app:first:second, app:first:third, app:first:second: are registered as a members of app:first:main command chain
[2021-05-25 21:07:25]: Executing app:first:main command itself first
[2021-05-25 21:07:25]: Executing app:first:main chain members
[2021-05-25 21:07:25]: Execution of app:first:main chain completed
```
