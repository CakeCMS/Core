<?php
/**
 * CakeCMS Core
 *
 * This file is part of the of the simple cms based on CakePHP 3.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Core
 * @license   MIT
 * @copyright MIT License http://www.opensource.org/licenses/mit-license.php
 * @link      https://github.com/CakeCMS/Core".
 * @author    Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

namespace Test\Controller;

use Core\Controller\AppController;

/**
 * Class MetadataController
 *
 * @package Test\Controller
 */
class MetadataController extends AppController
{

    /**
     * Index action for test meta data from controller.
     *
     * @return void
     */
    public function index()
    {
        $this->_setMetaData();
    }

    /**
     * Form action for test reload meta data from view.
     *
     * @return void
     */
    public function form()
    {
        $this->_setMetaData();
    }

    /**
     * Set page title and meta data.
     *
     * @return void
     */
    protected function _setMetaData()
    {
        $this->set('page_title', 'Test page title');
        $this->set('meta_keywords', 'test, meta, key');
        $this->set('meta_description', 'test meta description');
    }
}
