function toggleFields() {
  var typeSelect = document.getElementById("typeAvion");
  var fieldCommercial = document.getElementById("fieldCommercial");
  var fieldFret = document.getElementById("fieldFret");
  var fieldMilitaire = document.getElementById("fieldMilitaire");

  fieldCommercial.style.display = "none";
  fieldFret.style.display = "none";
  fieldMilitaire.style.display = "none";

  if (typeSelect.value === "commercial") {
    fieldCommercial.style.display = "block";
  } else if (typeSelect.value === "fret") {
    fieldFret.style.display = "block";
  } else if (typeSelect.value === "militaire") {
    fieldMilitaire.style.display = "block";
  }
}

function togglePisteTaxiway() {
  var locationSelect = document.getElementById("locationSelect");
  var pisteTaxiwayContainer = document.getElementById("pisteTaxiwayContainer");

  if (locationSelect.value === "Piste" || locationSelect.value === "Taxiway") {
    pisteTaxiwayContainer.style.display = "block";
  } else {
    pisteTaxiwayContainer.style.display = "none";
  }
}

function updateAllowedDestinations() {
  var avionSelect = document.getElementById("avionSelect");
  var nouvelleLocationSelect = document.getElementById("nouvelleLocation");
  var selectedOption = avionSelect.options[avionSelect.selectedIndex];
  var currentLocation = selectedOption.getAttribute("data-location");

  var allowedDestinations = {
    Hangar: ["Taxiway"],
    Taxiway: ["Piste"],
    Piste: ["En vol"],
    "En vol": ["En approche"],
    "En approche": [], // Pas de déplacement autorisé
  };

  // Effacer les options précédentes
  nouvelleLocationSelect.innerHTML =
    '<option value="" disabled selected>-- Choisissez un emplacement --</option>';

  if (allowedDestinations[currentLocation]) {
    allowedDestinations[currentLocation].forEach(function (destination) {
      var option = document.createElement("option");
      option.value = destination;
      option.textContent = destination;
      nouvelleLocationSelect.appendChild(option);
    });
  }

  // Gérer l'affichage du champ Piste/Taxiway
  document.getElementById("pisteTaxiwayContainer").style.display =
    currentLocation === "Taxiway" || currentLocation === "Piste"
      ? "block"
      : "none";
}
