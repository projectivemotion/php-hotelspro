#!/usr/bin/env bash

if [ -z "$1" ] ; then
    cat <<EOL

    1. Create a hotels.csv and destinations.csv file
    2. Pass the filenames to this script
    3. This script will product a sqlite db for use by script.

    optionally put all json files in the same directory and call this script:

    Examples:

EOL

    echo -e "\t$0 hotelpro.sqlite destinations.csv destinations"
    echo -e "\t$0 hotelpro.sqlite hotels.csv hotels"
    echo -e "\t$0 hotelpro.sqlite jsonfiles/ /path/to/php"
    exit
fi;

scriptDIR="$( readlink -f "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )" )"
# create schema


if [ ! -f "$1" ] ; then
sqlite3 "$1" <<EOL
create table hotels (
    code TEXT PRIMARY KEY ASC,
    name TEXT,
    country TEXT,
    zipcode TEXT,
    address TEXT,
    destination TEXT,
    latitude TEXT,
    longitude TEXT,
    currencycode TEXT,
    stars INTEGER,
    hotel_type INTEGER);

create table destinations (
    code TEXT PRIMARY KEY ASC,
    country TEXT,
    parent TEXT,
    name TEXT,
    latitude TEXT, longitude TEXT
);
EOL

    echo "Created schema in $1";
fi;


    if [ -d "$2" ]  && [ -x "$3" ]; then
        echo "Importing hotels and destinations from $2.. please wait..";
        sleep 1
        echo -n "Importing hotels.."
        "$0" "$1" <("$3" -d memory_limit=258M "$scriptDIR/json-tocsv.php" hotel "$2"/hotels-*.json) hotels
        echo -n "Importing destinations.."
        "$0" "$1" <("$3" -d memory_limit=258M "$scriptDIR/json-tocsv.php" destination "$2"/destinations-*.json) destinations
        echo "All done. $1 is ready for use."
        exit
    fi

    if [ -f "$1" ] && [ $# -lt 3 ] ; then
        echo "$1 exists. Aborting."
        exit
    fi


if [[ $# -gt 2 ]] ; then

     sqlite3 "$1" <<EOL
.mode csv $3
.separator "|"
.import $2 $3
EOL

    echo "Imported " "$(echo "select count(*) from $3;" | sqlite3 "$1")" records into "$2"
fi;
