# piwigo4blog
Plugin for [Piwigo](https://piwigo.org) self-hosted gallery: mass export images from gallery wrapped in html tags to embed to blog post.

This project is server API and plugin.
Client UI: https://github.com/sadr0b0t/piwigo4blog-react

[<img src="https://raw.githubusercontent.com/sadr0b0t/piwigo4blog-react/master/doc/screens/v0.1.x/piwigo4blog-share-02.png" width=800>](https://raw.githubusercontent.com/sadr0b0t/piwigo4blog-react/master/doc/screens/v0.1.x/piwigo4blog-share-02.png)

# Install plugin

Preffered way: from [plugin catalogue](https://piwigo.org/ext/extension_view.php?eid=891) with plugin management interface inside admin section.


Manually:
Unpack release archive to _plugins_ dir in Piwigo installation on server (for example: _/var/www/piwigo/plugins/piwigo4react_).

Inside Admin section go to Plugins > Piwigo4blog

# Build

This project contains ready to install PHP server code. You should only build client code from [piwigo4blog-react](https://github.com/sadr0b0t/piwigo4blog-react) project.

More info on plugin development for Piwigo: https://piwigo.org/doc/doku.php?id=dev:extensions:start

# API

Check out JSON data:

root category:
https://[your-piwigo-hosting.org]/plugins/piwigo4blog/api/category.php

with paging:
https://[your-piwigo-hosting.org]/plugins/piwigo4blog/api/category.php?img_lim=10&img_offset=1


some child category:

all images:
https://[your-piwigo-hosting.org]/plugins/piwigo4blog/api/category.php?id=15

with paging:
https://[your-piwigo-hosting.org]/plugins/piwigo4blog/api/category.php?id=15&img_lim=10&img_offset=1

