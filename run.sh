#!/bin/sh
docker run --rm --mount src="$(pwd)",target=/app,type=bind -ti rizwanjiwan/remarkablepdf:1.0 /bin/bash