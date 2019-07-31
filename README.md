# Web-Streaming

This is the first test version of WSP (a very old one 2012), it works but it's not optimized and not coded very well, don use in production.

### Requirements
- apache
- mysql
- php
- ffmpeg

### Configuration

In /data/ folder there is the db dump, after importing you need to configure the credentials in:
- /include/config/config.ini.php
- /include/membersite_config.php

inside /include/config/config.ini.php you should configure also the ffmpeg path
