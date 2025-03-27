# todolist

## Installation de l'application

- Choisir un dossier pour accueillir le projet et s'y positionner dans une boite de commande (exemple : C/ProjetOpenClassroom). 
- cloner le projet avec git en copiant l'URL du repository et exécuter la commande : git clone [https://github.com/AurelienDemblans/BilemoAPI.git](https://github.com/AurelienDemblans/todolist2.git)
- Dans le dossier que la commande git clone vient de créer , se positionner sur /todolist
- lancer la commande "composer install"
- créer la base de données, avec les commandes : 
 `php bin\console do:da:cr --env=dev` et `php bin\console do:da:cr --env=test`
- mettre à jour le schéma de la base de données de dev et de test avec la commande : 
 `php bin/console doctrine:migrations:migrate` et `php bin\console do:sc:up -f --env=test`
- lancer les fixtures avec la commande : 
`php bin/console do:fi:lo` et `php bin/console do:fi:lo --env=test`

lancer la commande symfony server:start 

Pour voir les différentes routes possible vous pouvez allez sur : http://127.0.0.1:8000/api/doc dans votre navigateur

pour tester les routes vous pouvez le faire directement sur la page du nelmio ou bien avec postman
