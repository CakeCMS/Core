#!/usr/bin/env sh

#
# CakeCMS Core
#
# This file is part of the of the simple cms based on CakePHP 3.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @package   Core
# @license   MIT
# @copyright MIT License http://www.opensource.org/licenses/mit-license.php
# @link      https://github.com/CakeCMS/Core
#

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Install application npm \033[0m"
cd ./vendor/cake-cms/app && npm install && cd ../../../
echo ""

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Install application bower \033[0m"
cd ./vendor/cake-cms/app && bower install && cd ../../../
echo ""

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Application gulp update \033[0m"
cd ./vendor/cake-cms/app && gulp update && cd ../../../
echo ""

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Prepare app css libs \033[0m"
mkdir -p ./tests/App/webroot/css/libs
cp ./vendor/cake-cms/app/webroot/css/libs/* ./tests/App/webroot/css/libs

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Prepare app css libs \033[0m"
mkdir -p ./tests/App/webroot/js/libs
cp ./vendor/cake-cms/app/webroot/js/libs/* ./tests/App/webroot/js/libs
