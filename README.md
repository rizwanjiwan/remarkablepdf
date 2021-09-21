# remarkablepdf

This is a quick thing I hacked together to automate the process of backing up my reMarkable. Basically, it turns all your notes into PDFs. Relies entirely on https://github.com/rorycl/rm2pdf to do the conversion of files.

(only tested on MacOS)

## Installation

Install Docker. Pull this project down. Run build.sh to build the docker image.

Copy config.example to config and edit the file in a text editor to enter your reMarkable details. The details can be found in help-> copyright and licenses.

## Execution

Run the docker image/create a container with run.sh. Open a terminal and connect to the container's shell. 

Go to the /app folder and run "php run.php" with your reMarkable on and connected to wifi. It will dump the notes into a bunch of pdfs in the output folder that you set in the config file.

This was just for fun. So much more to do to make it actually generally useable but I may or may not do that work.
