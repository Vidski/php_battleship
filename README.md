# php_server

**TODOS**

Nach einem Game werden die versenkten Schiffe nicht neu gesetzt.



**Naming Conventions**

| Classes | Methods | Properties | Functions | Variables | Interfaces |
| --- | --- | --- | --- | --- | --- |
| PascalCase | camelCase | camelCase | lower_case | camelCase | iPascalCase |

**GAMEINFOS:**

Wir wollen in dem Projekt das Spiel „Schiffe versenken“ realisieren.
Dazu wird der Server so konfiguriert, dass er die Möglichkeit bietet mehrere Spiele zeitgleich zu starten.
Auf dem Server werden Lobbys erstellt, in denen 2 Spieler gegeneinander Spielen und einen Chatbereich habe.
Am Anfang besteht nur die Möglichkeit einen privaten Raum zu erstellen.
Der Benutzer bekommt dann eine PIN über die ein anderen Benutzer dem Raum beitreten kann.
Später sollen eventuell noch öffentliche Räume dazukommen, in denen man gegen zufällige Gegner spielen kann.

Die Regeln 
    - Das Feld ist 10x10 Kästchen groß.
    - Jeder hat 10 Schiffe
        - ein Schlachtschiff (5 Kästchen)
        - zwei Kreuzer (je 4 Kästchen)
        - drei Zerstörer (je 3 Kästchen)
        - vier U-Boote (je 2 Kästchen)
        - Die Schiffe dürfen **nicht** aneinanderstoßen
        - Die Schiffe dürfen **nicht** über Eck gebaut sein oder Ausbuchtungen besitzen
        - Die Schiffe dürfen **nicht** diagonal aufgestellt werden
        - Die Schiffe dürfen am Rand liegen
    - Ein Schiff gilt als versenkt, wenn alle Felder des Schiffes getroffen wurden. 
    - Wer beginnt wird Zufällig ausgewählt.
    - Wenn ein Treffer gelandet wurde, ist der Spieler nochmal am Zug, ansonsten wechselt der Spieler.
    - Ein Spiel ist beendet, wenn ein Spieler keine Schiffe mehr auf dem Feld hat.

