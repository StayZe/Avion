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
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

</head>

<body class="w-screen">
    <div class="flex flex-row w-full justify-center items-center py-4 px-8 border-b border-gray-200 shadow-md bg-black">
        <p class="text-white">====================</p>
        <img src="/img/logo.png" height="175" width="175" alt="">
        <p class="text-white">====================</p>

    </div>

    <div>
        <?php
        $locations = ['Hangar', 'Taxiway', 'Piste', 'En vol', 'En approche'];
        ?>

        <div class="px-8 py-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">État des Avions par Emplacement</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php foreach ($locations as $location) : ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gray-100 px-5 py-3 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <?= htmlspecialchars($location) ?>
                            </h3>
                        </div>

                        <ul class="divide-y divide-gray-200">
                            <?php
                            $found = false;
                            foreach ($_SESSION['avions'] as $avion) :
                                if ($avion->location === $location) :
                                    $found = true;
                            ?>
                                    <li class="px-5 py-4 hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-lg font-bold text-gray-900 truncate">
                                                        <?= htmlspecialchars($avion->modele) ?>
                                                    </h4>
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <?= htmlspecialchars(str_replace('Avion', '', get_class($avion))) ?>
                                                    </span>
                                                </div>

                                                <div class="mt-2 grid grid-cols-2 gap-2">
                                                    <div class="flex items-center space-x-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                                        </svg>
                                                        <span class="text-sm text-gray-600"><?= $avion->vitesse ?> km/h</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                                        </svg>
                                                        <span class="text-sm text-gray-600"><?= $avion->moteurs ?> moteurs</span>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <?php if ($avion instanceof AvionCommercial) : ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                                                <circle cx="4" cy="4" r="3" />
                                                            </svg>
                                                            <?= $avion->capacite ?> passagers
                                                        </span>
                                                    <?php elseif ($avion instanceof AvionFret) : ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                                <circle cx="4" cy="4" r="3" />
                                                            </svg>
                                                            <?= $avion->chargeMax ?> tonnes
                                                        </span>
                                                    <?php elseif ($avion instanceof AvionMilitaire) : ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                                <circle cx="4" cy="4" r="3" />
                                                            </svg>
                                                            <?= $avion->munitions ?> munitions
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if (!empty($avion->positionDetaillee)) : ?>
                                                    <div class="mt-2 text-sm text-gray-500 flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span class="italic"><?= htmlspecialchars($avion->positionDetaillee) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php
                                endif;
                            endforeach;

                            if (!$found) : ?>
                                <li class="px-5 py-4 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z" />
                                    </svg>
                                    Aucun avion à cet emplacement
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">État des Avions par Emplacement</h2>

        <div class="mx-auto">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="grid md:grid-cols-3 gap-8 p-8">
                    <!-- Ajouter un Avion -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4 border-b pb-4">
                            <img src="/icon/plus.svg" class="h-10 w-10 text-blue-600" alt="">
                            <h2 class="text-2xl font-bold text-gray-800">Ajouter un Avion</h2>
                        </div>

                        <form method="POST" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Modèle</label>
                                    <input type="text" name="modele" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="Ex: Boeing 747">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vitesse (km/h)</label>
                                    <input type="number" name="vitesse" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="800">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Moteurs</label>
                                    <input type="number" name="moteurs" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="2">
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement</label>
                                    <select name="location" id="locationSelect"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        onchange="togglePisteTaxiway()" required>
                                        <option value="Hangar">Hangar</option>
                                        <option value="Taxiway">Taxiway</option>
                                        <option value="Piste">Piste</option>
                                        <option value="En vol">En vol</option>
                                        <option value="En approche">En approche</option>
                                    </select>
                                </div>

                                <div id="pisteTaxiwayContainer" class="col-span-2 hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de Piste / Taxiway</label>
                                    <input type="text" name="positionDetaillee"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="Ex: Piste 2, Taxiway B">
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select name="type" id="typeAvion"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        onchange="toggleFields()" required>
                                        <option value="commercial">Commercial</option>
                                        <option value="fret">Fret</option>
                                        <option value="militaire">Militaire</option>
                                    </select>
                                </div>

                                <!-- Type-specific fields -->
                                <div id="fieldCommercial" class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de places</label>
                                    <input type="number" name="compagnie"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="300">
                                </div>

                                <div id="fieldFret" class="col-span-2 hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacité de stockage (tonnes)</label>
                                    <input type="number" name="chargeMax"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="50">
                                </div>

                                <div id="fieldMilitaire" class="col-span-2 hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de munitions</label>
                                    <input type="number" name="munitions"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="20">
                                </div>

                                <div class="col-span-2">
                                    <button type="submit" name="ajouter"
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Déplacer un Avion -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4 border-b pb-4">
                            <img src="/icon/arrow-right.svg" class="h-10 w-10 text-green-600" alt="">
                            <h2 class="text-2xl font-bold text-gray-800">Déplacer un Avion</h2>
                        </div>

                        <?php if (isset($_SESSION['error_message'])) : ?>
                            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                                <?= $_SESSION['error_message'] ?>
                                <?php unset($_SESSION['error_message']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Avion à déplacer</label>
                                    <select name="id" id="avionSelect" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        onchange="updateAllowedDestinations()">
                                        <option value="" disabled selected>-- Sélectionnez un avion --</option>
                                        <?php foreach ($_SESSION['avions'] as $avion) : ?>
                                            <option value="<?= $avion->id ?>" data-location="<?= $avion->location ?>">
                                                <?= $avion->modele ?> (<?= $avion->location ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle Position</label>
                                    <select name="nouvelleLocation" id="nouvelleLocation" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                        <option value="" disabled selected>-- Choisissez un emplacement --</option>
                                    </select>
                                </div>

                                <div id="pisteTaxiwayContainer" class="col-span-2 hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de Piste / Taxiway</label>
                                    <input type="text" name="positionDetaillee" id="positionDetaillee"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="Ex: Piste 2, Taxiway B">
                                </div>

                                <div class="col-span-2">
                                    <button type="submit" name="deplacer"
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200">
                                        Déplacer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Supprimer un Avion -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4 border-b pb-4">
                            <img src="/icon/poubelle.svg" class="h-10 w-10 text-red-600" alt="">
                            <h2 class="text-2xl font-bold text-gray-800">Supprimer un Avion</h2>
                        </div>

                        <form method="POST" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Avion à supprimer</label>
                                    <select name="id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                        <option value="" disabled selected>-- Sélectionnez un avion --</option>
                                        <?php foreach ($_SESSION['avions'] as $avion) : ?>
                                            <option value="<?= $avion->id ?>">
                                                <?= $avion->modele ?> (<?= $avion->location ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-span-2">
                                    <button type="submit" name="supprimer"
                                        class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto flex justify-center items-center bg-gray-50">
        <img src="/img/Animation.gif" alt="">
    </div>


    <div class="flex flex-col w-full py-6 bg-black">
        <div class="w-full flex flex-row justify-evenly text-white mb-6">
            <div class="flex flex-col gap-1.5">
                <h3 class="font-bold underline">Sommaire</h3>
                <a href="#">Mentions illégales</a>
                <a href="#">Politique sans confidentialité</a>
                <a href="#">Nous vendons vos données</a>
                <a href="https://i0.wp.com/www.boeufkarotte.fr/wp-content/uploads/2021/03/Banniere-blog-cookies.jpg?fit=728%2C410&ssl=1" target="_blank">Voici des cookies</a>
            </div>
            <div class="flex flex-col gap-1.5">
                <h3 class="font-bold underline">Pour compléter</h3>
                <a href="#">Ici je ne sais pas quoi mettre</a>
                <a href="#">Ici encore moins</a>
            </div>
        </div>
        <div class="border-b border-white/30 w-10/12 mx-auto"></div>
        <div class="flex flex-row mx-auto mt-6 gap-4">
            <img src="/icon/Twitter.svg" height="20" width="20" alt=""><img src="/icon/Facebook.svg" height="20" width="20" alt=""><img src="/icon/Instagram.svg" height="20" width="20" alt="">
        </div>
    </div>

    <script src="/script.js"></script>
</body>

</html>