#!/bin/bash

# test std.php with different php versions

# build it first
/usr/bin/php build.php

# built-in php (5.4) on Mavericks
/usr/bin/php test.php

# php 5.5, installed via AMPPS
/Applications/AMPPS/php-5.5/bin/php test.php

# php 5.6, installed via php-osx.liip.ch
/usr/local/php5/bin/php test.php
