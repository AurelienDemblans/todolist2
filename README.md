# todolist

## Installation de l'application

- lancez votre serveur (par exemple ici : wamp64). 
- Choisir un dossier pour accueillir le projet et s'y positionner dans une boite de commande (exemple : C/ProjetOpenClassroom). 
- cloner le projet avec git en copiant l'URL du repository et exécuter la commande : git clone https://github.com/AurelienDemblans/todolist2.git
- Dans le dossier que la commande git clone vient de créer , se positionner sur /todolist
- dans le fichier .env ajouter : APP_TIMEZONE=Europe/Paris et mettez à jour les informations correspondants à vos paramètres (DATABASE_URL et APP_SECRET notamment)
- lancer la commande "composer install"
- créer la base de données, avec les commandes : 
 `php bin\console do:da:cr --env=dev` et `php bin\console do:da:cr --env=test`
- mettre à jour le schéma de la base de données de dev et de test avec la commande : 
 `php bin/console doctrine:migrations:migrate` et `php bin\console do:sc:up -f --env=test`
- lancer les fixtures avec la commande : 
`php bin/console do:fi:lo` et `php bin/console do:fi:lo --env=test`

lancer la commande symfony server:start 

accédez à l'application à l'url : http://127.0.0.1:8000
