# remarkablepdf

This is a quick thing I hacked together to automate the process of backing up my reMarkable. Basically, it turns all your notes into PDFs. Relies entirely on https://github.com/rorycl/rm2pdf to do the conversion of files.

(only tested on MacOS)

## Installation

Install Docker. Pull this project down. Run build.sh to build the docker image.

Copy config.example to config and edit the file in a text editor to enter your reMarkable details. The details can be found in help-> copyright and licenses.

## Execution

Run the docker image/create a container with run.sh. The container should boot up, run the pdf dump, and then exit.

You could setup a cron or something to automatically do this on a regular basis and then script whatever you want to pull the pdfs to some backup.

This was just for fun. So much more to do to make it actually generally useable but I may or may not do that work.
