#!/bin/sh

echo "Ensuing required DIRs exist..."
#change /app to /host_folder if you're deploying in. Right now it's assuming mounting
if [ ! -d "/app/logs" ]; then
  mkdir "/app/logs"
fi
touch "/app/logs/entrypoint.log";
if [ ! -d "/app/resources" ]; then
  mkdir "/app/resources"
fi
if [ ! -d "/app/resources/downloads" ]; then
  mkdir "/app/resources/downloads"
fi
if [ ! -d "/app/resources/output" ]; then
  mkdir "/app/resources/output"
fi
if [ ! -d "/app/tmp" ]; then
  mkdir "/app/tmp"
fi

cd /app

#vendor files
if [ ! -d "/app/vendor" ]; then
  php ../composer.phar install
fi

#rm2pdf
if [ ! -d "/app/rm2pdf" ]; then
  git clone https://github.com/rorycl/rm2pdf.git
  cd /app/rm2pdf
  go build
  cd ..
fi

#setup to run
crontab -r
crontab /app/dockerfiles/crontab.txt
echo "first run..."
php run.php
echo "tailing to stay alive..."
tail -f logs/entrypoint.log


