
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/36226f74e0554de1806dd0dde27b3a2f)](https://app.codacy.com/gh/BouhlelMohamed/project7_opc?utm_source=github.com&utm_medium=referral&utm_content=BouhlelMohamed/project7_opc&utm_campaign=Badge_Grade)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/c08a4cacaa17486ab76cbf4fa494c918)](https://app.codacy.com/gh/BouhlelMohamed/project7_opc?utm_source=github.com&utm_medium=referral&utm_content=BouhlelMohamed/project7_opc&utm_campaign=Badge_Grade)

<h3>Bilemo</h3>

Lien vers la =><a href="http://p7.mohamed-bouhlel.com/doc">doc</a>

Packages : 

    Symfony 5.1.8
    NelmioApiDocBundle

Installation

1 - Cloner le projet

https://github.com/BouhlelMohamed/project7_opc

2 - Modifier la BDD dans bilemo/.env

DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name

3 - Installer composer 

 composer install

<h2>Contexte</h2>
BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo. Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface). Il s’agit donc de vente exclusivement en B2B (business to business).

Il va donc falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.
<hr>
<h2>Besoin client</h2>

Le premier client a enfin signé un contrat de partenariat avec BileMo ! C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et les éprouver tout de suite.

Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :

- consulter la liste des produits BileMo ;
- consulter les détails d’un produit BileMo ;
- consulter la liste des utilisateurs inscrits liés à un client sur le site web ; 
- consulter le détail d’un utilisateur inscrit lié à un client ; 
- ajouter un nouvel utilisateur lié à un client ; 
- supprimer un utilisateur ajouté par un client. Seuls les clients référencés peuvent accéder aux API. Les clients de l’API doivent être authentifiés via Oauth ou JWT.
