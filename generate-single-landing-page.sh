#!/bin/bash
#
# Generiert die nächste fehlende Landing Page.
# Gibt "CREATED:<filename>" oder "DONE" aus.
#

DIR="$(cd "$(dirname "$0")" && pwd)"

# Service-Typen und ihre Templates
declare -a SERVICES=("entruempelung" "gewerbe" "haushaltsaufloesung" "kellerentruempelung" "messiewohnung")
declare -a TEMPLATES=("entruempelung-braunschweig.html" "gewerbe-braunschweig.html" "haushaltsaufloesung-braunschweig.html" "kellerentruempelung-braunschweig.html" "messiewohnung-braunschweig.html")

# Städte: slug|name|lat|lon|beschreibung
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

for i in "${!SERVICES[@]}"; do
    SERVICE="${SERVICES[$i]}"
    TEMPLATE="${TEMPLATES[$i]}"
    TEMPLATE_PATH="$DIR/$TEMPLATE"

    if [ ! -f "$TEMPLATE_PATH" ]; then
        continue
    fi

    for CITY_DATA in "${CITIES[@]}"; do
        IFS='|' read -r SLUG NAME LAT LON BESCHREIBUNG STADTTEILE <<< "$CITY_DATA"

        FILENAME="${SERVICE}-${SLUG}.html"
        FILEPATH="$DIR/$FILENAME"

        if [ -f "$FILEPATH" ]; then
            continue
        fi

        # Stadtteil-HTML generieren
        STADTTEIL_HTML=""
        IFS=',' read -ra PARTS <<< "$STADTTEILE"
        for ST in "${PARTS[@]}"; do
            STADTTEIL_HTML="${STADTTEIL_HTML}                <div class=\"area-item\"><i class=\"fas fa-map-marker-alt\"></i> ${ST}</div>\n"
        done

        # Template kopieren und Ersetzungen durchführen
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
