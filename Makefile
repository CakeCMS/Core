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

.PHONY: build update test-all autoload test phpmd phpcpd phploc coveralls npm bower gulp

test-all:
	@echo -e "\033[0;33m>>> \033[0;30;46m Run all tests \033[0m"
	@make update autoload prepare-test-app test-unit phpmd phpcpd phploc

update:
	@echo -e "\033[0;33m>>> \033[0;30;46m Update project \033[0m"
	@composer update --no-interaction --optimize-autoloader
	@echo ""

autoload:
	@echo -e "\033[0;33m>>> \033[0;30;46m Composer autoload \033[0m"
	@composer dump-autoload --optimize --no-interaction
	@echo ""

test-unit:
	@echo -e "\033[0;33m>>> \033[0;30;46m Run unit-tests \033[0m"
	@php ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml.dist
	@echo ""

phpmd:
	@echo -e "\033[0;33m>>> \033[0;30;46m Check PHPmd \033[0m"
	@php ./vendor/phpmd/phpmd/src/bin/phpmd ./src text codesize, unusedcode, naming
	@echo ""

phpcpd:
	@echo -e "\033[0;33m>>> \033[0;30;46m Check Copy&Paste \033[0m"
	@php ./vendor/sebastian/phpcpd/phpcpd ./src --verbose
	@echo ""

phploc:
	@echo -e "\033[0;33m>>> \033[0;30;46m Show statistic \033[0m"
	@php ./vendor/phploc/phploc/phploc ./src --verbose
	@echo ""

coveralls:
	@echo -e "\033[0;33m>>> \033[0;30;46m Send coverage to coveralls.io \033[0m"
	@php ./vendor/satooshi/php-coveralls/bin/coveralls --verbose
	@echo ""

prepare-test-app:
	@echo -e "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Clone CakeCMS Application \033[0m"
	git clone --depth=50 --branch=master https://github.com/CakeCMS/App.git application

	cd ./application && bash bin/app-jquery-table.sh && cd ../
	@echo ""

	@echo -e "\033[0;33m>>> \033[0;30;46m Install application npm \033[0m"
	cd ./application && npm install && cd ../
	@echo ""

	@echo -e "\033[0;33m>>> \033[0;30;46m Install application bower \033[0m"
	cd ./application && bower install && cd ../
	@echo ""

	@echo -e "\033[0;33m>>> \033[0;30;46m Application gulp update \033[0m"
	cd ./application && gulp update && cd ../
	@echo ""

	@echo -e "\033[0;33m>>> \033[0;30;46m Prepare app css libs \033[0m"
	mkdir -p ./tests/App/webroot/css/libs
	cp ./application/webroot/css/libs/* ./tests/App/webroot/css/libs
	@echo ""

	@echo -e "\033[0;33m>>> \033[0;30;46m Prepare app css libs \033[0m"
	mkdir -p ./tests/App/webroot/js/libs
	cp ./application/webroot/js/libs/* ./tests/App/webroot/js/libs
