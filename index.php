<?php
include 'avion.php';
include 'session_manager.php';

// Ajouter un avion
// Ajouter un avion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $id = uniqid();
    $modele = $_POST['modele'];
    $vitesse = $_POST['vitesse'];
    $moteurs = $_POST['moteurs'];
    $capacite = $_POST['capacite'];
    $location = $_POST['location'];
    $type = $_POST['type'];

    // Récupérer le numéro de piste/taxiway uniquement si nécessaire
    $positionDetaillee = ($_POST['location'] === "Piste" || $_POST['location'] === "Taxiway") ? $_POST['positionDetaillee'] : null;

    if ($type == "commercial") {
        $compagnie = $_POST['compagnie'] ?? "";
        $avion = new AvionCommercial($id, $modele, $vitesse, $moteurs, $capacite, $location, $compagnie, $positionDetaillee);
    } elseif ($type == "fret") {
        $chargeMax = $_POST['chargeMax'] ?? 0;
        $avion = new AvionFret($id, $modele, $vitesse, $moteurs, $capacite, $location, $chargeMax, $positionDetaillee);
    } else {
        $munitions = $_POST['munitions'] ?? 0;
        $avion = new AvionMilitaire($id, $modele, $vitesse, $moteurs, $capacite, $location, $munitions, $positionDetaillee);
    }

    ajouterAvion($avion);
}


// Déplacement d'un avion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deplacer'])) {
    $id = $_POST['id'];
    $nouvelleLocation = $_POST['nouvelleLocation'];
    $positionDetaillee = $_POST['positionDetaillee'] ?? null; // Récupérer la valeur ou NULL si vide

    deplacerAvion($id, $nouvelleLocation, $positionDetaillee);
}


// Suppression d'un avion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['supprimer'])) {
    $id = $_POST['id'];
    supprimerAvion($id);
}


// Affichage des avions
?>
<!DOCTYPE html>
<html>

<head>
    <title>Gestion des avions</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h2>Liste des avions</h2>
    <?php
    $locations = ['Hangar', 'Taxiway', 'Piste', 'En vol', 'En approche'];
    foreach ($locations as $location) :
    ?>
        <h3><?= $location ?></h3>
        <ul>
            <?php
            $found = false;
            foreach ($_SESSION['avions'] as $avion) :
                if ($avion->location === $location) :
                    $found = true;
            ?>
                    <li>
                        <strong><?= $avion->modele ?></strong> -
                        <?= $avion->vitesse ?> km/h, <?= $avion->moteurs ?> moteurs,

                        <?php if ($avion instanceof AvionCommercial) : ?>
                            Places: <?= $avion->capacite ?> passagers
                        <?php elseif ($avion instanceof AvionFret) : ?>
                            Stockage: <?= $avion->chargeMax ?> tonnes
                        <?php elseif ($avion instanceof AvionMilitaire) : ?>
                            Munitions: <?= $avion->munitions ?>
                        <?php endif; ?>

                        (Type: <?= get_class($avion) ?>)

                        <?php if (!empty($avion->positionDetaillee)) : ?>
                            (Détail de la piste : <?= htmlspecialchars($avion->positionDetaillee) ?>)
                        <?php endif; ?>
                    </li>
            <?php
                endif;
            endforeach;

            if (!$found) {
                echo "<li>Aucun avion</li>";
            }
            ?>
        </ul>
    <?php endforeach; ?>


    <h2>Ajouter un Avion</h2>
    <form method="POST">
        <label>Modèle :</label>
        <input type="text" name="modele" required><br>

        <label>Vitesse :</label>
        <input type="number" name="vitesse" required><br>

        <label>Moteurs :</label>
        <input type="number" name="moteurs" required><br>

        <label>Capacité :</label>
        <input type="number" name="capacite" required><br>

        <label>Emplacement :</label>
        <select name="location" id="locationSelect" onchange="togglePisteTaxiway()" required>
            <option value="Hangar">Hangar</option>
            <option value="Taxiway">Taxiway</option>
            <option value="Piste">Piste</option>
            <option value="En vol">En vol</option>
            <option value="En approche">En approche</option>
        </select><br>

        <!-- Champ pour choisir la piste/taxiway (visible seulement si nécessaire) -->
        <div id="pisteTaxiwayContainer" style="display: none;">
            <label>Numéro de Piste / Taxiway :</label>
            <input type="text" name="positionDetaillee" placeholder="Ex: Piste 2, Taxiway B"><br>
        </div>

        <label>Type :</label>
        <select name="type" id="typeAvion" onchange="toggleFields()" required>
            <option value="commercial">Commercial</option>
            <option value="fret">Fret</option>
            <option value="militaire">Militaire</option>
        </select><br>

        <!-- Champ spécifique pour Commercial (Nombre de places) -->
        <div id="fieldCommercial">
            <label>Nombre de places :</label>
            <input type="number" name="compagnie"><br>
        </div>

        <!-- Champ spécifique pour Fret (Capacité de stockage) -->
        <div id="fieldFret" style="display: none;">
            <label>Capacité de stockage (tonnes) :</label>
            <input type="number" name="chargeMax"><br>
        </div>

        <!-- Champ spécifique pour Militaire (Munitions) -->
        <div id="fieldMilitaire" style="display: none;">
            <label>Nombre de munitions :</label>
            <input type="number" name="munitions"><br>
        </div>

        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Déplacer un Avion</h2>
    <form method="POST">
        <label>Avion à déplacer :</label>
        <select name="id" required>
            <option value="" disabled selected>-- Sélectionnez un avion --</option>
            <?php foreach ($_SESSION['avions'] as $avion) : ?>
                <option value="<?= $avion->id ?>">
                    <?= $avion->modele ?> (<?= $avion->location ?>)
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Nouvelle Position :</label>
        <select name="nouvelleLocation" id="nouvelleLocation" required onchange="togglePisteTaxiway()">
            <option value="Hangar">Hangar</option>
            <option value="Taxiway">Taxiway</option>
            <option value="Piste">Piste</option>
            <option value="En vol">En vol</option>
            <option value="En approche">En approche</option>
        </select><br>

        <div id="pisteTaxiwayContainer" style="display: none;">
            <label>Numéro de Piste / Taxiway :</label>
            <input type="text" name="positionDetaillee" placeholder="Ex: Piste 2, Taxiway B"><br>
        </div>

        <button type="submit" name="deplacer">Déplacer</button>
    </form>

    <h2>Supprimer un Avion</h2>
    <form method="POST">
        <label>Avion à supprimer :</label>
        <select name="id" required>
            <option value="" disabled selected>-- Sélectionnez un avion --</option>
            <?php foreach ($_SESSION['avions'] as $avion) : ?>
                <option value="<?= $avion->id ?>">
                    <?= $avion->modele ?> (<?= $avion->location ?>)
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" name="supprimer" style="background-color: red; color: white;">Supprimer</button>
    </form>

    <script src="/script.js"></script>
</body>

</html>