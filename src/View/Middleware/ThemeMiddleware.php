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

namespace Core\View\Middleware;

use Core\Theme;
use Core\Plugin;
use Cake\Http\Runner;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

/**
 * Class ThemeMiddleware
 *
 * @package Core\View\Middleware
 */
class ThemeMiddleware
{

    /**
     * Serve assets if the path matches one.
     *
     * @param   ServerRequest $request
     * @param   Response $response
     * @param   Runner $next
     * @return  mixed
     */
    public function __invoke($request, $response, $next)
    {
        $theme    = Theme::setup($request->getParam('prefix'));
        $request  = $request->withParam('theme', $theme);
        $response = $next($request, $response);

        Plugin::loaded($theme);
        return $response;
    }
}
