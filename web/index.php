<?php
/**
 * Created by PhpStorm.
 * User: Samuel Besnier
 * Date: 14/06/2017
 * Time: 14:20
 */

// Démarrage de la session
session_start();

// Définition du dossier racine du projet
define('ROOT_PATH', dirname(__DIR__));

// Inclusion du fichier d'auto chargement de composer
require ROOT_PATH.'/vendor/autoload.php';

// Inclusion de dépendance du projet
require ROOT_PATH.'/src/framework/mvc.php';
require ROOT_PATH.'/src/config/config.php';
// Enregistrement des fonctions d'autochargement des classes
spl_autoload_register("autoloader");

// Instanciation de klogger
$logger = new \Katzgrau\KLogger\Logger(ROOT_PATH.'/logs');

// Récupération du contrôleur
if( isset($_GET['controller']) ) {
    $controllerName = $_GET['controller'];
}
else {
    $controllerName = 'accueil';
}

// Sécurisation l'accès à l'administration
session_regenerate_id(true);

$securedRoutes =
    [
        "home-admin" => "admin",
        "matiere" => "admin",
        "home-formateur" => "formateur",
        "home-stagiaire" => "stagiaire"
    ];

// Handling user with OOP
$user = getUser();

$role = $user->getRole();

// Si on tente d'accéder à une page sécurisée sans s'être identifié au
// au préalable alors la route est modifiée pour afficher le formulaire de login
if (array_key_exists($controllerName, $securedRoutes) && $role != $securedRoutes[$controllerName]) {
    $_SESSION['flash'] = "Vous n'avez pas les droits pour accéder à cette page";
    header(
        "location:/?controller=login"
    );
    exit();
}

// Définition du chemin du contrôleur
$controllerPath = ROOT_PATH.'/src/controllers/'.$controllerName.'.php';

// Test de l'existence du contrôleur
if(! file_exists($controllerPath)) {
    // Envoi vers le fichier erreur
    $controllerPath = ROOT_PATH.'/src/controllers/erreur.php';
}

$logger->info("Lancement de l'application");

// Éxecution du contrôleur
require $controllerPath;