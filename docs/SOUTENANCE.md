# Préparation de soutenance — Mutualis App

Ce document sert de fil conducteur personnel. Il ne remplace pas la compréhension du projet : chaque écran présenté doit être expliqué avec ses règles métier et ses choix techniques.

## Présentation en dix minutes

### 1. Problème — 1 minute

Les porteurs ont des projets mais ne disposent pas toujours des ressources nécessaires. Les compétences, les financements et les équipements restent dispersés dans des échanges peu traçables.

### 2. Réponse proposée — 1 minute

Mutualis centralise la publication des projets, l'expression des besoins, la proposition d'apports et leur validation par les responsables concernés.

### 3. Démonstration utilisateur — 3 minutes

- ouvrir le catalogue ;
- afficher un projet et ses deux indicateurs de progression ;
- ouvrir une session externe ;
- compléter la fiche métier ;
- proposer un apport de compétence ou financier ;
- montrer la notification de suivi.

### 4. Démonstration interne — 3 minutes

- ouvrir la validation RH ou Finance ;
- valider un apport ;
- afficher le contrat et le paiement simulé ;
- ouvrir la matrice de charge ;
- créer une affectation à 80 %, puis tenter une affectation concurrente à 30 % ;
- montrer le refus automatique à 110 %.

### 5. Démonstration de confiance — 2 minutes

- ouvrir le pool financier ;
- présenter le transfert entre deux projets ;
- lancer la vérification du registre ;
- expliquer que chaque hash dépend de l'écriture précédente et de l'identifiant courant ;
- rappeler que la modification directe d'une écriture est détectée.

## Réponses courtes aux questions probables

### Pourquoi Laravel et Livewire ?

Laravel fournit l'authentification, l'ORM, les migrations, les policies et les transactions. Livewire permet de conserver une architecture serveur claire tout en donnant une interface réactive, adaptée à un prototype académique qui ne nécessite pas une SPA complète.

### Comment empêchez-vous une surcharge RH ?

Les affectations sont liées à une personne et à une période. Avant insertion, les lignes qui se chevauchent sont verrouillées et leurs pourcentages sont additionnés. Une somme supérieure à 100 % déclenche une erreur de validation et aucune ligne n'est créée.

### Comment protégez-vous le registre financier ?

Les écritures sont ajoutées dans une transaction. Le projet source est verrouillé avant le contrôle du solde. Chaque écriture conserve le hash précédent et son propre hash SHA-256. Une modification casse donc la chaîne à partir de cette écriture.

### Le paiement est-il réellement connecté à Mobile Money ?

Dans cette version de recherche, la passerelle est simulée. Le contrat et le cycle de paiement sont réels dans l'application, mais l'appel opérateur est remplacé par une implémentation de démonstration. Une passerelle Orange Money ou Moov Money est une évolution de production.

### Quelles sont les limites ?

Le prototype doit encore être durci pour la production : passerelle de paiement réelle, WebSockets, supervision, sauvegardes, politique de conservation des données et déploiement avec secrets gérés.

## Livrables écrits à joindre au mémoire

- diagramme de cas d'utilisation mis à jour ;
- diagramme de classes incluant `ProjectUserAssignment` et `FinancialLedgerEntry` ;
- diagramme de séquence d'une contribution validée puis payée ;
- schéma relationnel et dictionnaire de données ;
- protocole de test avec résultats ;
- guide d'installation et manuel utilisateur ;
- section limites, sécurité, éthique et perspectives.
