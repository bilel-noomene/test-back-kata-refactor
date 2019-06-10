CHANGELOG
=========

1 First commit
------------
- Etat initial du projet.


2 Using namespaces
------------------
- Composer a été configuré pour charger les classes du projets (sous les dossiers src et tests) 
suivant la spécification PSR 4. Composer génère un fichier `autoload.php` sous le dossier vendor
qui permet de charger ces classes ainsi que les classes vendor.

- Un fichier `bootstrap.php` a été ajouté à la racine du projet pour factoriser les opérations à 
exécuter à l'amorçage de l’application (accès web ou console). Pour notre cas, nous n’avons 
besoin qu’à charger le fichier vendor/autoload.php. Ce fichier doit être importé dans les scripts
qui s’exécutent en premier lors du lancement du projet (par exemple `example/example.php`). Pour 
les scripts de tests, PHP Unit charge par défaut le fichier `vendor/autoload.php`.


3 Simplify TemplateManager
--------------------------
- L’objectif de ce commit est de faciliter la compréhension du TemplateManager. Une nouvelle 
méthode `hasPlcaholder` a été ajoutée à cette classe et qui ne comporte qu’une seule ligne mais 
qui améliore la lisibilité du code. 

- Les deux variables `$destinationOfQuote` et `$destination` ont été fusionneés puisqu’elles
portent la même valeur. Cela a impliqué des changements au niveau du traitement de la variable 
`$destination` en dehors du bloc `if($quote)` (voir le commentaire dans le code).


4 Optimize TemplateManager
--------------------------
- Le code de la fonction `computeText` contient beaucoup des conditions `if`. Avant d’effectuer 
une substitution dans le template, on vérifie si le placeholder est utilisé. Essayer de faire un 
remplacement avec un placeholder non utilisé n’a aucun effet sur le template et peut avoir le 
même coût que chercher le placeholder dans le template. Nous pouvons donc effectuer le 
remplacement directement cette vérification.

- La variable `$_quoteFromRepository` est chargée à partir de l’id du `$quote` et peut contenir
des données différentes. Mais dans notre cas, seulement l’attribut `id` de 
`$_quoteFromRepository` est utilisé. Nous pouvons par conséquent utiliser $quote au lieu de 
`$_quoteFromRepository`.


5 Implement template extensions
-------------------------------
- L’objectif de ce commit est de rendre le système facilement extensible. Le design pattern 
**Strategy** est utilisé pour ces fins. TemplateManager utilise maintenant des TemplateExtensions
pour effectuer la substitution des placeholders avec les valeurs correspondantes.

- Un TemplateExtension doit implémenter l’interface TemplateExtensionInterface et par conséquent
les trois méthodes suivantes :
    - `isInvolved` : pour indiquer si l'extension doit être utilisée pour le traitement du template.
    - `getPlaceholders` : retourne un tableau contenant les placeholders gérés par l’extension.
    - `loadData` : retourne les données qui correspond aux placeholders gérés par l’extension.

- Deux TemplateExtensions ont été implémentés : `QuoteTemplateExtension` et `UserTemplateExtension`.
 
- Les extensions peuvent être passées au moment de la création du TemplateManager conformément
au design pattern **Strategy** mais pour ne pas compliquer l’exemple et vu les contraintes du 
projet, ces extensions sont instanciées dans le constructeur de `TemplateManager`.


 6 Add AbstractTemplateExtension and factorize code
 --------------------------------------------------
- Les extensions présentent de la logique dupliquée notamment pour les deux méthodes `isInvolved`
et `getPlaceholders`. `AbstractTemplateExtension` est implémentée pour factoriser cette logique.

- En plus, on a utilisé des constantes pour manipuler les tags (~ placeholders sans le préfixe).


7 Autoload template extensions
------------------------------
- Un système a été implémenté pour permettre un chargement automatiquement des TemplateExtensions
dans TemplateManager : charger les classes instantiables et qui implémente l’interface 
`TemplateExtensionInterface` dans le namespace `App\TemplateExtension`


8 Extract Quote formatting into a separate service
--------------------------------------------------
- Partant du principe **Single Responsibility**, le formatage du Quote en texte et en html a été
 extrait dans un service séparé.


9 Using php 7.1 typing and encapsulate entity attributes
--------------------------------------------------------
- Le typing de `PHP 7` a été utilisé pour typer les arguments des fonctions même avec les types 
scalaires et pour la déclaration de leurs types de retour. Précisément, `PHP >= 7.1` est utilisé
pour pouvoir déclarer des types de retour `void`.
 
 - Les attributs des entités sont encapsulés et sont protégés avec des fonctions `setters` typées.


Note
----
Pour supporter des nouvelles placeholders dans le template, il suffit d’ajouter une nouvelle classe
qui implémente l’interface `TemplateExtensionInterface` (ou étend la classe `AbstractTemplateExtension`)
avec le namespace `App\TemplateExtension`. Aucune autre modification n’est nécessaire. De même, pour 
désactiver une extension, il suffit que la méthode isInvolved retourne false. On peut également 
supprimer carrément la classe de l’extension sans aucun impact. Le système implémenté respecte 
visiblement le principe `Open/Close` (ouvert à l’extension, fermé à la modification)
