# Calculator
Demonstrative application containing simple calculator.


Prerequisites
---
You must have an installed docker and docker-compose tools for running this application.

Previously [Install Docker](https://docs.docker.com/install/linux/docker-ce/ubuntu/#install-docker-ce) on your PC.
Provided link leads to installation guide for the Ubuntu.
For other OS installs please see the official documentation.

And [Install Docker Compose](https://docs.docker.com/compose/install/) as it necessary for our application.


Installation
---
Clone this repository on your computer using command below
```
git clone git@github.com:powerdigital/calc-symfony.git calculator
```
This command will create calculator directory in your filesystem pointing from your current position.
Go into the docker directory in the created project folder

```
cd calculator/docker/
```
Build docker containers bunch
```
docker-compose build
```
And then start application
```
docker-compose up -d
```

You can see running containers using command
```
docker-compose ps
```
And now lets dive into the created container (string docker_php_1 in the command below is the name of container)
```
docker exec -it docker_php_1 /bin/bash
```
Make sure that you are in /var/www/symfony folder in our container filesystem
```
pwd
```
Currently we need to update some dependencies using update.sh script placed in application root directory
```
bash update.sh
```
That's it!

Usage
---
Attention: Make sure that you correctly installed all necessary tools and dependencies described in the previous sections.
If yes, move forward! 
> UI: Go to this address using your favorite web browser 
```
http://0.0.0.0:8080
```
Enjoy playing with calculator! It's so fun! Joke)

> CLI: You can use calculator as console script
```
php bin/calc --expr '5+5'
php bin/calc --help
```

Technologies
---
During creation this awesome and cozy calculator used or includes set of technologies
- PHP 7.* programming language
- Symfony 4 framework
- PHPUnit testing environment
- Webpack
- React.js javascript platform
- Axios library
- Docker containers technology
- Nginx web server
- PHP-FPM FastCGI process manager
- yarn package manager
- bash scripts


License
---
MIT
