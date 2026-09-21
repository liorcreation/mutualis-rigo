# Mutualis App — Plateforme RIGO

Mutualis App est un prototype de recherche réalisé dans le cadre d'une licence à l'Université Aube Nouvelle de Ouagadougou. La plateforme facilite la publication de projets et la mutualisation de ressources financières, humaines et matérielles entre porteurs, collaborateurs et partenaires.

## Positionnement du projet

Le projet répond à un problème concret : les idées de projets existent, mais les compétences disponibles, les budgets et les équipements sont souvent dispersés. Mutualis centralise les besoins, les propositions et les validations dans un espace traçable.

Le périmètre de recherche est organisé autour de quatre mécanismes :

- profils physiques et moraux avec contrôle de vérification ;
- catalogue de projets et apports de mutualisation ;
- matrice d'affectation RH avec blocage des charges supérieures à 100 % ;
- registre financier append-only, verrouillé en transaction et chaîné par SHA-256.

## Stack technique

- PHP 8.2+ et Laravel 12 ;
- Livewire 4 et Volt pour les interfaces réactives ;
- Tailwind CSS et Vite ;
- PostgreSQL en environnement cible ;
- SQLite en mémoire pour les tests automatisés ;
- stockage Laravel pour les pièces jointes et les contrats PDF.

## Installation locale

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Pour traiter les notifications en file d'attente dans un environnement de démonstration :

```bash
php artisan queue:work
```

Les variables d'environnement sont obligatoires. Ne jamais commiter `.env` ni partager une clé de base de données, une clé d'application ou un secret de paiement.

## Parcours de démonstration

1. Créer ou utiliser un profil externe et compléter sa fiche métier.
2. Publier un projet avec un besoin financier et des compétences recherchées.
3. Faire valider le projet depuis le comité de pilotage.
4. Soumettre un apport financier ou une compétence.
5. Valider l'apport depuis le back-office correspondant au métier.
6. Signer le contrat et effectuer le paiement simulé.
7. Vérifier la progression du projet et la nouvelle écriture financière.
8. Affecter un collaborateur sur plusieurs projets et montrer le blocage au-delà de 100 %.
9. Ouvrir le registre financier et lancer le contrôle d'intégrité SHA-256.

Les comptes de démonstration créés par `UserSeeder` utilisent le mot de passe local `123456`. Ils sont réservés au développement et doivent être remplacés ou supprimés avant toute mise en ligne.

## Organisation métier

Les règles importantes ne sont pas seulement appliquées dans les vues :

- `HumanResourceAllocationService` contrôle les chevauchements de périodes et les charges RH ;
- `FinancialPoolService` exécute les transferts avec verrouillage pessimiste et signe chaque écriture ;
- les Policies protègent les projets, contributions, contrats, messages et réservations ;
- les rôles sont centralisés dans `App\Enums\UserRole` ;
- les notifications informent les acteurs après une validation ou un changement de statut.

## Limites assumées du prototype

La passerelle de paiement actuelle est simulée afin de démontrer le cycle métier sans dépendre d'un opérateur externe. Les notifications sont configurables par mail et base de données. Le rafraîchissement de certaines conversations utilise le polling Livewire ; une évolution vers Laravel Reverb/WebSockets est prévue pour un temps réel réseau complet.

Ces limites doivent être présentées comme des choix de prototype et non comme des fonctionnalités de production déjà intégrées.

## Vérification qualité

```bash
vendor/bin/pint --test
php artisan test
```

Le projet comprend des tests d'authentification, de rôles, de catalogue, de contributions, de contrats, de paiements, de messagerie, de FAQ, de réservations, de charge RH, de profil et d'intégrité financière.
