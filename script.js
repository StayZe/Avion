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
