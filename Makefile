.DEFAULT_GOAL := sh
# Commande par défaut : make

sh:
	docker compose exec -it php sh
	# Execute a bash shell inside the PHP container

cache:
#symofny
	docker compose exec -it php php bin/console cache:clear
	# Clear the Symfony cache inside the PHP container

logs:
	docker compose logs -f --tail=100
	# Follow the logs of all Docker containers, showing the last 1OO lines

help:
	# lister les commandes que l'on veut



# Utiliser make sh/cache/logs pour pouvoir directement executer la commande voulu, cela remplace la commande entière