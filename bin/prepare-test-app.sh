#!/usr/bin/env sh

#
# CakeCMS Core
#
# This file is part of the of the simple cms based on CakePHP 3.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @package    Core
# @license    MIT
# @copyright  MIT License http://www.opensource.org/licenses/mit-license.php
# @link       https://github.com/CakeCMS/Core
#

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Clone CakeCMS Application \033[0m"
git clone --depth=50 --branch=master https://github.com/CakeCMS/App.git application

cd ./application && bin/app-jquery-table.sh && cd ../
echo ""

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Install application npm \033[0m"
cd ./application && npm install && cd ../
echo ""

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Install application bower \033[0m"
cd ./application && bower install && cd ../
echo ""

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Application gulp update \033[0m"
cd ./application && gulp update && cd ../
echo ""

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Prepare app css libs \033[0m"
mkdir -p ./tests/App/webroot/css/libs
cp ./application/webroot/css/libs/* ./tests/App/webroot/css/libs

echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Prepare app css libs \033[0m"
mkdir -p ./tests/App/webroot/js/libs
cp ./application/webroot/js/libs/* ./tests/App/webroot/js/libs
