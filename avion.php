<?php
session_start();

// Classe Avion (Classe mère)
class Avion
{
    public $id;
    public $modele;
    public $vitesse;
    public $moteurs;
    public $capacite;
    public $location; // Hangar, Aéroport (Piste, Taxiway), En vol, En approche
    public $positionDetaillee; // Piste ou Taxiway si applicable

    public function __construct($id, $modele, $vitesse, $moteurs, $capacite, $location, $positionDetaillee = null)
    {
        $this->id = $id;
        $this->modele = $modele;
        $this->vitesse = $vitesse;
        $this->moteurs = $moteurs;
        $this->capacite = $capacite;
        $this->location = $location;
        $this->positionDetaillee = $positionDetaillee;
    }
}

// Classe AvionCommercial
class AvionCommercial extends Avion
{
    public $compagnie;

    public function __construct($id, $modele, $vitesse, $moteurs, $capacite, $location, $compagnie, $positionDetaillee = null)
    {
        parent::__construct($id, $modele, $vitesse, $moteurs, $capacite, $location, $positionDetaillee);
        $this->compagnie = $compagnie;
    }
}

// Classe AvionFret
class AvionFret extends Avion
{
    public $chargeMax;

    public function __construct($id, $modele, $vitesse, $moteurs, $capacite, $location, $chargeMax, $positionDetaillee = null)
    {
        parent::__construct($id, $modele, $vitesse, $moteurs, $capacite, $location, $positionDetaillee);
        $this->chargeMax = $chargeMax;
    }
}

// Classe AvionMilitaire
class AvionMilitaire extends Avion
{
    public $munitions;

    public function __construct($id, $modele, $vitesse, $moteurs, $capacite, $location, $munitions, $positionDetaillee = null)
    {
        parent::__construct($id, $modele, $vitesse, $moteurs, $capacite, $location, $positionDetaillee);
        $this->munitions = $munitions;
    }
}
