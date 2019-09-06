# Digital Ocean Droplet Automation

This project was made using the [Lumen PHP Framework](https://lumen.laravel.com/docs) with the objective of assisting in the automation of droplet maintenance.

## HOW-TOs

### 1. Configure the application

First of all you must follow the lumen docs for the application installation, after finishing this step you can add your custom env vars on .env file:

- DO_API_KEY={YOUR DIGITAL OCEAN API KEY}
- DO_DEFAULT_IMAGE={YOUR DIGITAL OCEAN BASE IMAGE OR SNAPSHOT}
- DO_DEFAULT_REGION={YOUR DIGITAL OCEAN REGION}
- DO_DROPLET_DEFAULT_TAG={YOUR DIGITAL OCEAN DEFAULT TAG NAME}


### 2. Register the droplet creation routines

The create droplet routine is based on the create droplet command as follows:

```create:droplet {droplet_name} {droplet_size} {total_count}```

Basically this command will create ```{total_count}``` droplets with the size ```{droplet_size}```. All the created droplets have a default format name:

{droplet_name}{droplet_size}-{random_5_digit_number}

All the created droplets will be tagged with ```DO_DROPLET_DEFAULT_TAG``` env variables, its important to guarantee the use with load-balancers.

To register this command as a routine you have to create a schedule for the command.

```php
$schedule
  ->command('create:droplet my-app- 3x 4')
  ->cron('0 12 * * *');
```

### 3. Register the droplet delete routines

The delete droplet routine is based on the following command:

```delete:droplet {droplet_name} {droplet_size}```

This command will search for droplets by name applying a ```preg_match``` to match the desired droplets. For security, there is a ```$protected_droplets``` array where you can define a list of droplets to be ignored by the command.

To register this command as a routine you have to create a schedule for the command.

```php
$schedule
  ->command('delete:droplet my-app- 3x')
  ->cron('0 12 * * *');
```

## DO YOU NEED HELP?!

[Schedule documentation](https://laravel.com/docs/5.8/scheduling#scheduling-artisan-commands)

[Commands documentation](https://laravel.com/docs/5.8/artisan#writing-commands)

[Digital Ocean API docs](https://developers.digitalocean.com/documentation/v2/)


## DO YOU WANT TO HELP?

You can submmit a pull request or open an issue :) cheers

