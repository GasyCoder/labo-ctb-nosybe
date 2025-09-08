#!/bin/bash

# Variables
PROJECT_PATH="/home/kananavy-dev/Documents/DevGasy/labo-ctb-nosybe"
SERVER_NAME="labo-ctb-nosybe.local"
CONF_FILE="/etc/apache2/sites-available/labo-ctb-nosybe.conf"

# Vérification si Apache est installé
if ! command -v apache2 &> /dev/null
then
    echo "Apache2 n'est pas installé. Installe-le avec : sudo apt install apache2 -y"
    exit
fi

# Création du fichier de configuration Apache
echo "Création du VirtualHost pour $SERVER_NAME..."
sudo bash -c "cat > $CONF_FILE" <<EOL
<VirtualHost *:80>
    ServerName $SERVER_NAME
    DocumentRoot $PROJECT_PATH/public

    <Directory $PROJECT_PATH/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/labo-ctb-nosybe-error.log
    CustomLog \${APACHE_LOG_DIR}/labo-ctb-nosybe-access.log combined
</VirtualHost>
EOL

# Activer le site et le module rewrite
echo "Activation du site et du module rewrite..."
sudo a2ensite labo-ctb-nosybe.conf
sudo a2enmod rewrite

# Ajouter l'entrée dans /etc/hosts si elle n'existe pas
if ! grep -q "$SERVER_NAME" /etc/hosts; then
    echo "127.0.0.1   $SERVER_NAME" | sudo tee -a /etc/hosts
    echo "Ajout de $SERVER_NAME à /etc/hosts"
fi

# Redémarrer Apache
echo "Redémarrage d'Apache..."
sudo systemctl reload apache2

echo "-------------------------------------------"
echo "Configuration terminée !"
echo "Accède maintenant à : http://$SERVER_NAME"
echo "-------------------------------------------"
