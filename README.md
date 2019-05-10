# php_server

**Naming Conventions**

| Classes | Methods | Properties | Functions | Variables | Interfaces |
| --- | --- | --- | --- | --- | --- |
| PascalCase | camelCase | camelCase | lower_case | camelCase | iPascalCase |

**PROCESS:**

**SERVER**->on_message_received->**GAME**->handler_action->**HANDLER**->action **RETURN** JSON

**TODO:**

- [ ] Lobby/Raum


**GAME_INFOS**

- Schiffe versenken
- Mehrere Spiele zeitgleich möglich
    - Ein Spieler kann einen privaten Raum erstellen und bekommt einen Pin, über diesen Pin kann ein anderer Spieler sich verbinden.
    - Wenn am Ende noch Zeit ist, werden öffentliche Räume erstellt um zufällige Gegner zu bekommen.
- Regeln
    - Das Feld ist 10x10  Kästchen groß.
    - Jeder hat 10 Schiffe
        - ein Schlachtschiff(5 Kästchen)
        - zwei Kreuzer (je 4 Kästchen)
        - drei Zerstörer (je 3 Kästchen)
        - vier U-Boore (je 2 Kästchen)
    - Die Schiffe dürfen nicht aneinander stoßen
    - Die Schiffe dürfen nicht über Eck gebaut sein oder Ausbuchtungen besitzen
    - Die Schiffe dürfen nicht diagonal aufgestellt werden
    - Die Schiffe dürfen auch am Rand liegen
    - Ein Schiff gilt als versenkt, wenn alle Felder des Schiffes getroffen wurden. 
    - Wer beginnt wird Zufällig ausgewählt.
    - Wenn ein Treffer gelandet wurde, ist der Spieler nochmal am Zug, ansonsten wechselt der Spieler.
    - Ein Spiel ist beendet, wenn ein Spieler keine Schiff mehr auf dem Feld hat.