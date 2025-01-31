<?php
session_start();

// Initialisation de la session si vide
if (!isset($_SESSION['avions'])) {
    $_SESSION['avions'] = [];
}

// Fonction pour ajouter un avion
function ajouterAvion($avion)
{
    $_SESSION['avions'][] = $avion;
}

// Fonction pour déplacer un avion
function deplacerAvion($id, $nouvelleLocation, $positionDetaillee = null) {
    foreach ($_SESSION['avions'] as &$avion) {
        if ($avion->id == $id) {
            $avion->location = $nouvelleLocation;
            
            // Si l'avion est sur une piste ou un taxiway, on enregistre le numéro
            if ($nouvelleLocation === "Piste" || $nouvelleLocation === "Taxiway") {
                $avion->positionDetaillee = $positionDetaillee;
            } else {
                $avion->positionDetaillee = null; // Effacer si l'avion n'est plus sur une piste/taxiway
            }
            return true;
        }
    }
    return false;
}



// Fonction pour supprimer un avion
function supprimerAvion($id)
{
    foreach ($_SESSION['avions'] as $key => $avion) {
        if ($avion->id == $id) {
            unset($_SESSION['avions'][$key]);
            $_SESSION['avions'] = array_values($_SESSION['avions']); // Réindexer le tableau
            return true;
        }
    }
    return false;
}
