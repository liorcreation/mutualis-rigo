# Déploiement Mutualis sur Oracle Cloud Always Free

Cette procédure déploie Mutualis sur une VM Oracle Cloud Always Free avec :

- Ubuntu 24.04 ;
- PHP 8.3-FPM et Nginx ;
- PostgreSQL local persistant ;
- worker Laravel permanent ;
- scheduler Laravel exécuté chaque minute ;
- stockage local persistant sur le volume Oracle ;
- Cloudflare pour le DNS, le HTTPS et la protection en façade.

## 1. Préparer Oracle Cloud

Créer une VM **Always Free** dans la région principale du compte : Ubuntu 24.04, architecture AMD ou ARM, avec une adresse IPv4 publique. Ne sélectionner que des ressources portant explicitement le label `Always Free`.

Dans les règles réseau de la VM, autoriser les ports TCP `22`, `80` et `443`. Ne jamais exposer le port PostgreSQL `5432` publiquement.

Oracle peut demander une carte bancaire pour vérifier l’identité du compte. Cela ne signifie pas que les ressources explicitement marquées `Always Free` seront facturées.

## 2. Pointer le domaine Cloudflare

Un domaine Railway comme `mutualis.up.railway.app` ne peut pas être transféré vers Oracle ou Cloudflare. Il faut un domaine contrôlé par le propriétaire du projet, par exemple `mutualis.app`.

Créer dans Cloudflare :

```text
Type: A
Name: @ ou app
Target: IP publique de la VM Oracle
Proxy: activé après validation HTTP
```

Commencer avec le proxy Cloudflare désactivé, vérifier que le DNS pointe correctement, puis activer le proxy après la génération du certificat.

## 3. Installer l’application

Depuis une session SSH sur la VM :

```bash
sudo -i
export APP_DOMAIN=app.mutualis.example.com
export REPOSITORY_URL=https://github.com/liorcreation/mutualis-rigo.git
git clone --depth 1 --branch main "$REPOSITORY_URL" /tmp/mutualis-rigo
bash /tmp/mutualis-rigo/deploy/oracle/bootstrap.sh
```

Le script demande le mot de passe PostgreSQL sans l’écrire dans le dépôt. Il crée ensuite `.env`, installe les dépendances, construit les assets, exécute les migrations et démarre les services systemd.

## 4. Activer HTTPS

Après propagation DNS :

```bash
sudo certbot --nginx -d app.mutualis.example.com
```

Dans Cloudflare, choisir le mode SSL/TLS `Full (strict)` une fois le certificat installé.

## 5. Stockage des pièces jointes

Par défaut, le script utilise le volume Oracle :

```dotenv
FILESYSTEM_DISK=local
RIGO_PRIVATE_DISK=local
```

Pour utiliser Cloudflare R2, créer un bucket et une clé S3 dédiée, puis modifier uniquement les variables suivantes dans `/var/www/mutualis-app/.env` :

```dotenv
FILESYSTEM_DISK=s3
RIGO_PRIVATE_DISK=s3
AWS_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com
AWS_DEFAULT_REGION=auto
AWS_BUCKET=<bucket-r2>
AWS_ACCESS_KEY_ID=<access-key-r2>
AWS_SECRET_ACCESS_KEY=<secret-key-r2>
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Après toute modification :

```bash
cd /var/www/mutualis-app
php artisan config:cache
sudo systemctl restart mutualis-queue
```

## 6. Déploiements suivants

Le déploiement est volontairement explicite afin d’éviter une mise en production accidentelle :

```bash
cd /var/www/mutualis-app
sudo git fetch origin main
sudo git reset --hard origin/main
sudo -u www-data composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
sudo -u www-data npm ci --no-audit --no-fund
sudo -u www-data npm run build
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan view:cache
sudo systemctl restart mutualis-queue
```

Railway doit rester intact jusqu’à validation de la nouvelle URL, des connexions, des migrations et des téléversements.
