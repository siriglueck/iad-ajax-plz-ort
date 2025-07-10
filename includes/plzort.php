<?php
// Unterdrücken der Ausgabe von PHP-Fehlermeldungen
error_reporting(0);
ini_set("display_error", false);

if (empty($_GET)) {
  echo json_encode(array(array("error" => "Es wurden keine Zeichen eingegeben!")));
  exit;
}

if (empty(trim($_GET['q']))) {
  echo json_encode(array(array("error" => "Es wurden keine Zeichen eingegeben!")));
  exit;
} else {
  $strQ = $_GET['q'];
}

$strCsv = 'zuordnung_plz_ort.csv';

// CSV-Datei zum lesen öffnen
$fp = fopen($strCsv, 'r');

// Prüfung, ob Öffnung erfolgreich
if (!$fp) {
  echo json_encode(array(array("error" => "Datei <b>" . $strCsv . "</b> nicht gefunden!")));
  exit;
}

$arrOutput = array();

$i = 0;
while ($arrRow = fgetcsv($fp)) {
  // ersten Durchlauf ignorieren
  if ($i === 0) {
    $i++;
    continue;
  }

  // Prüfe mit RegExp ob der Anfang der CSV-Zeichenkette für die Spalte Ort mit der Eingabe-Zeichenkette übereinstimmt
  if (preg_match("/^$strQ/i", $arrRow[1]) || preg_match("/^$strQ/i", $arrRow[2])) {
    // füge dem zweidimensionalen Array die gefundenen Spalten hinzu
    $arrOutput[] = array(
      'ort' => $arrRow[1],
      'plz' => $arrRow[2],
      'bundesland' => $arrRow[3]
    );
  }
}

// ist das Ausgabe-Array leer...
if( empty( $arrOutput ) ) {
  // ... liefere eine Fehlermeldung
  echo json_encode(array(array("error" => "Keine Übereinstimmung gefunden!")));
} else {
  // sonst: JSON-Struktur an JavaScript zurückliefern
  echo json_encode($arrOutput);
}

// CSV-Datei schließen
fclose($fp);