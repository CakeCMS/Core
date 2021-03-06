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

namespace Core\Controller\Admin;

/**
 * Class RootController
 *
 * @package Core\Controller\Admin
 */
class RootController extends AppController
{

    /**
     * Dashboard action.
     *
     * @return  void
     *
     * @throws  \Aura\Intl\Exception
     */
    public function dashboard()
    {
        $this->set('page_title', __d('core', 'Dashboard'));
    }
}
