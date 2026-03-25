#!/bin/bash
#
# Landing Page Generator – reiner Template-Renderer.
#
# Modi:
#   --list-done
#       Listet alle bereits generierten Landing Pages (eine pro Zeile).
#       Format: SERVICE|SLUG  (z.B. messiewohnung|goslar)
#
#   --create SERVICE SLUG NAME LAT LON BESCHREIBUNG STADTTEILE
#       Generiert eine neue Seite aus dem Service-Template.
#       SERVICE      : entruempelung | gewerbe | haushaltsaufloesung | kellerentruempelung | messiewohnung
#       SLUG         : URL-slug des Ortes, nur Kleinbuchstaben + Bindestriche (z.B. bockenem, bad-gandersheim)
#       NAME         : Anzeigename (z.B. "Bockenem", "Bad Gandersheim")
#       LAT / LON    : GPS-Koordinaten als Dezimalzahl
#       BESCHREIBUNG : Freitext für Sätze wie "in Bockenem und Umgebung"
#       STADTTEILE   : Kommagetrennte Ortsteile (z.B. "Kernstadt,Mahlum,Hary,Bönnien")
#
# Ausgabe bei --create:
#   CREATED:<filename>
#   SERVICE:<service>
#   CITY:<name>
#

DIR="$(cd "$(dirname "$0")" && pwd)"

SERVICES="entruempelung gewerbe haushaltsaufloesung kellerentruempelung messiewohnung"

template_for() {
    case "$1" in
        entruempelung)       echo "entruempelung-braunschweig.html" ;;
        gewerbe)             echo "gewerbe-braunschweig.html" ;;
        haushaltsaufloesung) echo "haushaltsaufloesung-braunschweig.html" ;;
        kellerentruempelung) echo "kellerentruempelung-braunschweig.html" ;;
        messiewohnung)       echo "messiewohnung-braunschweig.html" ;;
        *) echo ""; ;;
    esac
}

# --list-done: alle vorhandenen Haupt-Landing-Pages ausgeben
if [ "$1" = "--list-done" ]; then
    for SERVICE in $SERVICES; do
        for f in "$DIR/${SERVICE}"-*.html; do
            [ -f "$f" ] || continue
            BASENAME="$(basename "$f" .html)"
            # Braunschweig-Template und Braunschweig-Unterseiten überspringen
            [[ "$BASENAME" == *"-braunschweig" ]]  && continue
            [[ "$BASENAME" == *"-braunschweig-"* ]] && continue
            # Slug = alles nach dem ersten Bindestrich-Segment (dem Service-Namen)
            SLUG="${BASENAME#${SERVICE}-}"
            # Unterseiten (service-city-district) überspringen
            # Zähle Bindestriche: service hat keinen, city kann einen haben (bad-harzburg)
            # District-Seiten haben immer mindestens 2 Segmente nach dem Service
            REMAINING_PARTS=$(echo "$SLUG" | tr '-' '\n' | wc -l)
            # bad-harzburg = 2 Teile → OK; bockenem = 1 Teil → OK
            # bad-harzburg-kernstadt = 3 Teile → überspringen
            [ "$REMAINING_PARTS" -gt 2 ] && continue
            echo "${SERVICE}|${SLUG}"
        done
    done
    exit 0
fi

# --create SERVICE SLUG NAME LAT LON BESCHREIBUNG STADTTEILE
if [ "$1" = "--create" ]; then
    SERVICE="$2"
    SLUG="$3"
    NAME="$4"
    LAT="$5"
    LON="$6"
    BESCHREIBUNG="$7"
    STADTTEILE="$8"

    if [ -z "$SERVICE" ] || [ -z "$SLUG" ] || [ -z "$NAME" ] || [ -z "$LAT" ] || [ -z "$LON" ]; then
        echo "Usage: $0 --create SERVICE SLUG NAME LAT LON BESCHREIBUNG STADTTEILE"
        echo "Services: $SERVICES"
        exit 1
    fi

    TEMPLATE="$(template_for "$SERVICE")"
    if [ -z "$TEMPLATE" ]; then
        echo "ERROR:Unbekannter Service '$SERVICE'. Verfügbar: $SERVICES"
        exit 1
    fi

    TEMPLATE_PATH="$DIR/$TEMPLATE"
    if [ ! -f "$TEMPLATE_PATH" ]; then
        echo "ERROR:Template nicht gefunden: $TEMPLATE_PATH"
        exit 1
    fi

    FILENAME="${SERVICE}-${SLUG}.html"
    FILEPATH="$DIR/$FILENAME"

    if [ -f "$FILEPATH" ]; then
        echo "ERROR:Datei existiert bereits: $FILENAME"
        exit 1
    fi

    [ -z "$BESCHREIBUNG" ] && BESCHREIBUNG="${NAME} und Umgebung"

    sed \
        -e "s|${SERVICE}-braunschweig\.html|${SERVICE}-${SLUG}.html|g" \
        -e "s|in Braunschweig und allen Stadtteilen|in ${BESCHREIBUNG}|g" \
        -e "s|in Braunschweig und Umgebung|in ${BESCHREIBUNG}|g" \
        -e "s|nach Braunschweig|nach ${NAME}|g" \
        -e "s|von Braunschweig|von ${NAME}|g" \
        -e "s|Braunschweig für|${NAME} für|g" \
        -e "s|in Braunschweig|in ${NAME}|g" \
        -e "s|Braunschweig|${NAME}|g" \
        -e "s|braunschweig|${SLUG}|g" \
        -e "s|52\.2689|${LAT}|g" \
        -e "s|10\.5268|${LON}|g" \
        "$TEMPLATE_PATH" > "$FILEPATH"

    echo "CREATED:${FILENAME}"
    echo "SERVICE:${SERVICE}"
    echo "CITY:${NAME}"
    exit 0
fi

echo "Usage:"
echo "  $0 --list-done"
echo "  $0 --create SERVICE SLUG NAME LAT LON BESCHREIBUNG STADTTEILE"
exit 1
