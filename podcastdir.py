#!/usr/bin/python

import os
import sys
from datetime import datetime
import PyRSS2Gen
import socket
import eyeD3

path = '.'
serverDirectory = "podcasts"

hostname = socket.gethostname()
baseUrl = "http://{0}/{1}/".format(hostname, serverDirectory)

def main():

    rss = PyRSS2Gen.RSS2(
	title = "Assorted Podcasts",
	link = baseUrl,
	description = "Just a bunch of MP3s in a directory somewhere.",
	lastBuildDate = datetime.now(),
	items = [] )

    for file in os.listdir(path):

	if not eyeD3.isMp3File(file):
	    continue;

	rss.items.append(buildRssItem(file))

    rss.write_xml(sys.stdout)

def buildRssItem(file):

    print file

    audioFile = eyeD3.Mp3AudioFile(file);
    tag = audioFile.getTag()

    item = PyRSS2Gen.RSSItem (
	title = "{0} - {1}".format(tag.getAlbum(), tag.getTitle()),
	link = baseUrl + file,
	guid = PyRSS2Gen.Guid(file),
	pubDate = datetime.fromtimestamp(os.path.getmtime(file)),
	enclosure = buildEnclosure(file) )
    
    return item;

def buildEnclosure(file):

    filePath = os.path.join(path, file)
    url = baseUrl + file
    size = os.path.getsize(filePath)

    return PyRSS2Gen.Enclosure(url, size, "audio/mpeg") 
    
    


if __name__ == "__main__":
    main()
