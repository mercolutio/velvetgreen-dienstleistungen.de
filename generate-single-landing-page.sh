#!/bin/bash
#
# Modi:
#   --list                  Gibt alle fehlenden Service+Stadt-Kombinationen als JSON aus
#   --create SERVICE SLUG   Generiert eine spezifische Seite
#   (kein Argument)         Legacy: erste fehlende Seite generieren
#

DIR="$(cd "$(dirname "$0")" && pwd)"

declare -a SERVICES=("entruempelung" "gewerbe" "haushaltsaufloesung" "kellerentruempelung" "messiewohnung")
declare -a TEMPLATES=("entruempelung-braunschweig.html" "gewerbe-braunschweig.html" "haushaltsaufloesung-braunschweig.html" "kellerentruempelung-braunschweig.html" "messiewohnung-braunschweig.html")

# Städte: slug|name|lat|lon|beschreibung|stadtteile
declare -a CITIES=(
    "salzgitter|Salzgitter|52.15|10.33|Salzgitter und allen Stadtteilen|Bad,Lebenstedt,Thiede,Gebhardshagen,Salder,Ringelheim"
    "wolfenbuettel|Wolfenbüttel|52.1642|10.5372|Wolfenbüttel und Umgebung|Stadtgebiet,Linden,Fümmelse,Ahlum,Leinde"
    "hildesheim|Hildesheim|52.1561|9.9500|Hildesheim und allen Stadtteilen|Innenstadt,Nordstadt,Weststadt,Südstadt,Oststadt,Drispenstedt,Ochtersum"
    "goslar|Goslar|51.9059|10.4289|Goslar und Umgebung|Altstadt,Jürgenohl,Kramerswinkel,Georgenberg,Jerstedt,Hahnenklee"
    "peine|Peine|52.3203|10.2345|Peine und Umgebung|Kernstadt,Stederdorf,Vöhrum,Woltorf,Dungelbeck"
    "gifhorn|Gifhorn|52.4889|10.5506|Gifhorn und Umgebung|Kernstadt,Gamsen,Kästorf,Neubokel,Wilsche"
    "wolfsburg|Wolfsburg|52.4226|10.7865|Wolfsburg und allen Stadtteilen|Innenstadt,Westhagen,Detmerode,Vorsfelde,Fallersleben,Reislingen,Kreuzheide"
    "helmstedt|Helmstedt|52.2277|11.0103|Helmstedt und Umgebung|Kernstadt,Barmke,Büddenstedt,Emmerstedt"
    "bad-harzburg|Bad Harzburg|51.8797|10.5597|Bad Harzburg und Umgebung|Kernstadt,Bündheim,Göttingerode,Harlingerode,Schlewecke"
)

service_label() {
    case "$1" in
        entruempelung)       echo "Entrümpelung" ;;
        gewerbe)             echo "Gewerbeentrümpelung" ;;
        haushaltsaufloesung) echo "Haushaltsauflösung" ;;
        kellerentruempelung) echo "Kellerentrümpelung" ;;
        messiewohnung)       echo "Messiewohnung" ;;
        *)                   echo "$1" ;;
    esac
}

do_generate() {
    local SERVICE="$1"
    local TARGET_SLUG="$2"

    for i in "${!SERVICES[@]}"; do
        [ "${SERVICES[$i]}" = "$SERVICE" ] || continue
        local TEMPLATE="${TEMPLATES[$i]}"
        local TEMPLATE_PATH="$DIR/$TEMPLATE"

        if [ ! -f "$TEMPLATE_PATH" ]; then
            echo "ERROR:Template nicht gefunden: $TEMPLATE_PATH"
            exit 1
        fi

        for CITY_DATA in "${CITIES[@]}"; do
            IFS='|' read -r SLUG NAME LAT LON BESCHREIBUNG STADTTEILE <<< "$CITY_DATA"
            [ "$SLUG" = "$TARGET_SLUG" ] || continue

            local FILENAME="${SERVICE}-${SLUG}.html"
            local FILEPATH="$DIR/$FILENAME"

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
        done

        echo "ERROR:Stadt '$TARGET_SLUG' nicht gefunden"
        exit 1
    done

    echo "ERROR:Service '$SERVICE' nicht gefunden"
    exit 1
}

# --list: alle fehlenden Kombinationen als JSON
if [ "$1" = "--list" ]; then
    echo "["
    first=1
    for i in "${!SERVICES[@]}"; do
        SERVICE="${SERVICES[$i]}"
        [ -f "$DIR/${TEMPLATES[$i]}" ] || continue
        for CITY_DATA in "${CITIES[@]}"; do
            IFS='|' read -r SLUG NAME LAT LON BESCHREIBUNG STADTTEILE <<< "$CITY_DATA"
            FILENAME="${SERVICE}-${SLUG}.html"
            [ -f "$DIR/$FILENAME" ] && continue
            [ $first -eq 0 ] && echo "  ,"
            printf '  {"service": "%s", "service_label": "%s", "city_slug": "%s", "city_name": "%s", "filename": "%s"}\n' \
                "$SERVICE" "$(service_label $SERVICE)" "$SLUG" "$NAME" "$FILENAME"
            first=0
        done
    done
    echo "]"
    exit 0
fi

# --create SERVICE CITY_SLUG: spezifische Seite generieren
if [ "$1" = "--create" ]; then
    if [ -z "$2" ] || [ -z "$3" ]; then
        echo "Usage: $0 --create SERVICE CITY_SLUG"
        exit 1
    fi
    SERVICE="$2"
    CITY_SLUG="$3"
    FILENAME="${SERVICE}-${CITY_SLUG}.html"
    if [ -f "$DIR/$FILENAME" ]; then
        echo "ERROR:Datei existiert bereits: $FILENAME"
        exit 1
    fi
    do_generate "$SERVICE" "$CITY_SLUG"
    exit 0
fi

# Legacy-Modus: erste fehlende Seite generieren
for i in "${!SERVICES[@]}"; do
    SERVICE="${SERVICES[$i]}"
    TEMPLATE="${TEMPLATES[$i]}"
    TEMPLATE_PATH="$DIR/$TEMPLATE"
    [ -f "$TEMPLATE_PATH" ] || continue

    for CITY_DATA in "${CITIES[@]}"; do
        IFS='|' read -r SLUG NAME LAT LON BESCHREIBUNG STADTTEILE <<< "$CITY_DATA"
        FILENAME="${SERVICE}-${SLUG}.html"
        FILEPATH="$DIR/$FILENAME"
        [ -f "$FILEPATH" ] && continue

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
    done
done

echo "DONE"
exit 0
