#!/usr/bin/env bash

if [ -z "$1" ] ; then
    cat <<EOL

    Usage
    Examples:

EOL
    echo -e "\t$0 hotelpro.sqlite jsonfiles/ /path/to/php"
    exit
fi;

scriptDIR="$( readlink -f "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )" )"

    if [ -d "$2" ]  && [ -x "$3" ]; then
        echo "Importing hotels and destinations from $2.. please wait..";
        sleep 1
        echo -n "Importing hotels.."
        "$3" -d memory_limit=258M "$scriptDIR/import-json.php" hotels "$1" "$2"/hotels-*.json
        echo -n "Importing destinations.."
        "$3" -d memory_limit=258M "$scriptDIR/import-json.php" destinations "$1" "$2"/destinations-*.json
        echo "All done. $1 is ready for use."
        exit
    fi