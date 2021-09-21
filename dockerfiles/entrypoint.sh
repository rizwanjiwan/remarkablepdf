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
chmod 777 -R /app/logs/
chmod 777 -R /app/tmp/
#end /app vs /host_folder

cd /app
#install vendor files
php ../composer.phar install
#rm2pdf
git clone https://github.com/rorycl/rm2pdf.git
cd /app/rm2pdf
go build
cd ..

echo "tailing to stay alive..."
tail -f logs/entrypoint.log


