#!/usr/bin/env bash

if [ -z "$1" ] ; then
    cat <<EOL

    1. Create a hotels.csv file
    2. Pass the filename to this script
    3. This script will product a sqlite db for use by script.

    Example:

EOL

    echo -e "\t$0 hotelpro.sqlite hotels.csv|destinations.csv hotels|destinations"
    exit
fi;

# create schema


    if [ -f "$1" ] && [ $# -lt 3 ] ; then
        echo "$1 exists. Aborting."
        exit
    fi;

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

    echo "Created schema";
fi


if [[ $# -gt 2 ]] ; then

     sqlite3 "$1" <<EOL
.mode csv $3
.import $2 $3
select count(*) from $3 ;
EOL

    echo "Created $1 . Done."
fi;
