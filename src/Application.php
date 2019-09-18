<?php
/**
 * CakeCMS Core
 *
 * This file is part of the of the simple cms based on CakePHP 3.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @license     MIT
 * @copyright   MIT License http://www.opensource.org/licenses/mit-license.php
 * @link        https://github.com/CakeCMS/Core".
 * @author      Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

namespace Core;

use Core\Core\Plugin;
use Cake\Core\Configure;
use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Core\PluginInterface;
use Core\View\Middleware\ThemeMiddleware;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Error\Middleware\ErrorHandlerMiddleware;

/**
 * Class Application
 *
 * @package App
 */
class Application extends BaseApplication
{

    /**
     * Load all the application configuration and bootstrap logic.
     *
     * Override this method to add additional bootstrap logic for your application.
     *
     * @return  void
     */
    public function bootstrap()
    {
        parent::bootstrap();

        $this->addPlugin('Core', ['bootstrap' => true, 'routes' => true]);

        //  Load all plugins.
        $plugins = [
            'Search',
            'Config',
            'Community',
            'Migrations',
            'Extensions',
            Configure::read('Theme.site'),
            Configure::read('Theme.admin'),
        ];

        foreach ($plugins as $name) {
            if (Plugin::isLoaded($name)) {
                continue;
            }

            if ($path = Plugin::findPlugin($name)) {
                $this->addPlugin($name, Plugin::getConfigForLoad($path));
            }
        }
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param   MiddlewareQueue $middlewareQueue The middleware queue to setup.
     *
     * @return  MiddlewareQueue The updated middleware queue.
     */
    public function middleware($middlewareQueue)
    {
        $middlewareQueue

            /** Catch any exceptions in the lower layers, and make an error page/response */
            ->add(ErrorHandlerMiddleware::class)

            /** Handle plugin/theme assets like CakePHP normally does. */
            ->add(AssetMiddleware::class)

            /**
             * Add routing middleware.
             * Routes collection cache enabled by default, to disable route caching pass null as cacheConfig, example:
             * `new RoutingMiddleware($this)` you might want to disable this cache in case your routing
             * is extremely simple.
             */
            ->add(new RoutingMiddleware($this, '_cake_routes_'))

            /**
             * Add theme middleware.
             * Setup request params 'theme'. Param hold current theme name.
             */
            ->add(ThemeMiddleware::class);

        return $middlewareQueue;
    }

    /**
     * Add new plugin.
     *
     * @param   PluginInterface|string $name
     * @param   array $config
     *
     * @return  $this
     */
    public function addPlugin($name, array $config = [])
    {
        parent::addPlugin($name, $config);

        $plugin = (array) $name;

        foreach ($plugin as $_name) {
            if ((bool) Plugin::isLoaded($_name)) {
                Plugin::addManifestCallback($name);
                Cms::mergeConfig('App.paths.locales', Plugin::getLocalePath($name));
            }
        }

        return $this;
    }
}
