# Planung Postleitzahlen und Orte finden

## Programm-Plan

Der User gibt in das Eingabefeld Buchstaben oder Zahlen ein, je nachdem ob er nach einem Ort oder einer PLZ sucht.

Bei jedem Zeichen wird im Ausgabebereich bereits eine Tabelle ausgegeben, welche zu den eingegebenen Zeichen passt. Mit jedem weiteren Zeichen wird die Tabelle weiter gefiltert. Die Tabelle besteht aus 3 Spalten (Ort, PLZ, Bundesland). Die Tabelle benötigt keine Kopfzeile.

Die Zeichenkette wird von JS entgegengenommen und an den Webserver weitergeleitet.

Das PHP-Script nimmt die Eingabe entgegen und wird die Suche in einer CSV-Datei mit den Daten durchführen. Dann liefert das PHP-Script die gefilterten Zeilen der CSV-Datei und sendet sie an des JS zurück.

Das JS gibt die gefilterte Tabelle in den Ausgabebereich aus.

Bei leerem Eingabefeld bleibt der Ausgabebereich leer.

## Zu klärende Fragen

Q: Wo kommen die Daten (CSV-Datei) her?

A: Statistisches Bundesamt

---

Q: Welche Fehleingaben sind vom User zu erwarten und wie werden diese abgefangen bzw. behandelt?

A:

- Groß- bzw. Kleinschreibung: Suche nicht case-sensitiv umsetzen
- Sonderzeichen, falsche Rechtschreibung, Buchstabendreher
- führende oder nachfolgende Leerzeichen

Alle diese Fehler führen in der Regel zu keinem Suchergebnis. Daher:

- Ausgeben einer Meldung
- Ausgabebereich bleibt leer

Sollten sicherheitsrelevante böswillige Eingaben erfolgen sind diese durch die nativen JS- bzw. PHP-Funktionen zu behandeln.

---

Q: Welches Skript (JS oder PHP) ist für die HTML-Tabellenstruktur verantwortlich?

A: Javascript, weil weniger Datenverkehr und dadurch bessere Performance.

---

Q: In welchem Format soll das Ergebnis der Filterung vom PHP-Script geliefert werden?

A: JSON, weil ein Objekt geliefert wird und die Verarbeitung komfortabler ist. XML bereitet unter Umständen Probleme und Text ist umständlich weiter zu verarbeiten.

## Pseudo-Code

### JavaScript

- DOM schützen
- XHR-Objekt erzeugen
- Variablen / Kostanten für das Eingabefeld und den Ausgabe-Bereich initialisieren
- Eventlistener für die Eingaben (Betätigung einer Taste) registrieren mit Referenz auf eine Callback-Funktion
- Callback-Funktion:
  - kompletter Inhalt (Wert) des Eingabefeldes in einer Variable speichern
  - Inhalt validieren
  - Anfrage vorbereiten (Verbind zum Server öffnen)
    - Sendemethode festlegen (GET)
    - Pfad zur PHP-Datei liefern und die zu sendenden Daten übergeben
  - Callback-Funktion für die Entgennahme der Server-Antwort auf das Event der Server-Antwort registrieren
  - Anfrage senden
- Callback-Funtion der Server-Antwort:
  - Prüfen ob die Antwort des Server-Skriptes vollständig eingetroffen ist
    - wenn nein: weiter warten
    - wenn ja: nächster Schritt
  - Prüfen, ob das Server-Script eine Fehlermeldung liefert
    - wenn Fehler: keine Ausgabe, Meldung ausgeben
    - sonst: nächster Schritt (JSON-Objekt erzeugen)
  - Ausgabe-Variable anlegen und mit dem öffneneden Tabellen-Tag initialisieren
  - Schleife über das JSON-Objekt iterieren
    - HTML-Tags für die Zeile erzeugen
    - für jede Eigenschaft des JSON-Objektes eine HTML-Zelle mit dem Wert dieser Eigenschaft als Inhalt anlegen
    - Zeilenstruktur an die Ausgabe-Variable anhängen
  - schließende Tabellen-Tag anhängen
  - Ausgabe-Variable in den Ausgabe-Bereich ausgeben

### PHP-Script

- Prüfe, ob Infos geliefert wurden
  - wenn nicht: Meldung ausgeben, Abbruch
  - sonst: nächster Schritt
- Infos aufräumen und validieren (White-Spaces für und hinter der Zeichenkette entfernen)
- Prüfe ob die Zeichenkette nach dem Aufräumen leer ist
  - Ja: Meldung und Abbruch
  - Nein: Inhalt der JS-Lieferung in eine Variable ablegen
- CSV-Datei zum Lesen öffnen
- Prüfen, ob die Öffnung erfolgreich war
  - Nein: Meldung und Abbruch
  - Ja: nächster Schritt
- Ausgabe-Array initialisieren (Variable)
- Schleife, in welcher die CSV-Datei zeilenweise eingelesen wird
  - ersten Durchlauf ignorieren (Überschriftszeile)
  - Prüfen, ob die ersten Zeichen der gelieferten Zeichenkette (Zeile) mit den ersten Zeichen der Spalte Ort **ODER** der Spalte PLZ übereinstimmen
    - Ja: die Spalten Ort, PLZ, Bundesland als inneres Array an das Ausgabe-Array anhängen
    - Nein: Meldung und nächster Zeilendurchlauf
- Das zweidimensionale Ausgabe-ARray in einen JSON-String konvertieren
  - Struktur Ausgabe-Arrays
  
  ```
  Array (
    [0] => Array ( 'ort' => 'Aachen', 'plz' => '12345', 'bundesland' => 'Nordrhein-Westfahlen' ),
    [1] => Array ( 'ort' => '...', 'plz' => '...', 'bundesland' => '...' ),
    ...
  )
  ```

- aus dem erzeugten Ausgabe-Array eine JSON-Zeichenkette geneirieren und an das XHR-Objekt zurückliefern
- CSV-Datei schließen

## Übersicht der Bezeichner für Variablen, Konstanten und Funktionen etc.

### JS-Datei

| Bezeichner | Bemerkung |
|-------|-------|
| `const objXHR` | `XMLHttpRequest`-Objekt |
| `let elemInput` | das Eingabefeld |
| `let strInput` | das Eingabefeld |
| `let elemOutput` | das Ausgabe-Div |
| `let strOutput` | die HTML-Tabelle |
| `const strUri` | Pfad der anzufordernden PHP-Datei + URI-Query-String |
| `const objJSON` | das vom JSON-Objekt mit den gefilterten Daten |
| `let intRow` | Schleifen-Variable für die JSON-Objekt-Schleife |
| `fnCallPhp()` | Callback-Funktion zum Event "Taste gedrückt" |
| `fnUpdatePage()` | Callback-Funktion zum Event "Antwort vollständig" |

### PHP-Datei

| Bezeichner | Bemerkung |
|-------|-------|
| `$_GET` | Superglobales assoziatives Array mit übergebener Zeichenkette im Key `q` |
| `$strQ` | Wert des Schlüssels `q` im `$_GET`-Array |
| `$strCsv` | Pfad zur CSV-Datei |
| `$fp` | Dateizeiger auf die CSV-Datei |
| `$arrRow` | Schleifenvariable für das Zeilen-Array |
| `$arrOutput` | Array mit den gefilterten Daten aus der CSV-Datei |

## Ordner-Struktur

- `index.html`
- `includes`
  - `plzort.php`
  - `zuordnung_plz_ort.csv`
- `css`
  - `style.css`
- `js`
  - `plzort.js`
