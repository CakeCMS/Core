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

namespace Core\Test\TestCase\Toolbar;

use Core\Utility\Toolbar;
use Core\TestSuite\TestCase;
use Core\Toolbar\ToolbarHelper;

/**
 * Class ToolbarHelperTest
 *
 * @package Core\Test\TestCase\Toolbar
 */
class ToolbarHelperTest extends TestCase
{

    public function testAdd()
    {
        ToolbarHelper::setToolbar(__FUNCTION__);
        ToolbarHelper::add('Add link', '/add');

        $toolbar = Toolbar::getInstance(__FUNCTION__);

        $this->assertHtml([
            ['div' => ['id' => 'test-add-core-link', 'class' => 'item-wrapper tb-item-1 first last']],
                'a' => [
                    'href'  => '/add',
                    'class' => 'ck-link btn btn-green lighten-2',
                    'title' => 'Add link'
                ],
                    ['i' => ['class' => 'ck-icon fa fa-plus']], '/i',
                    'span' => ['class' => 'ck-link-title'],
                        'Add link',
                    '/span',
                '/a',
            '/div',
        ], $toolbar->render());
    }

    public function testCancel()
    {
        ToolbarHelper::setToolbar(__FUNCTION__);
        ToolbarHelper::cancel(null, '/index');

        $toolbar = Toolbar::getInstance(__FUNCTION__);

        $this->assertHtml([
            'div' => ['id' => 'test-cancel-core-link', 'class' => 'item-wrapper tb-item-1 first last'],
                'a' => ['href' => '/index', 'class' => 'ck-link btn btn-grey lighten-3', 'title' => 'Cancel'],
                    'i' => ['class' => 'ckTextRed ck-icon fa fa-close'], '/i',
                    'span' => ['class' => 'ck-link-title'],
                        'Cancel',
                    '/span',
                '/a',
            '/div'
        ], $toolbar->render());

        ToolbarHelper::setToolbar('cancelTestTitle');
        ToolbarHelper::cancel('Custom title', '/index');

        $toolbar = Toolbar::getInstance('cancelTestTitle');
        $this->assertHtml([
            'div' => ['id' => 'cancel-test-title-core-link', 'class' => 'item-wrapper tb-item-1 first last'],
                'a' => ['href' => '/index', 'class' => 'ck-link btn btn-grey lighten-3', 'title' => 'Custom title'],
                    'i' => ['class' => 'ckTextRed ck-icon fa fa-close'], '/i',
                    'span' => ['class' => 'ck-link-title'],
                        'Custom title',
                    '/span',
                '/a',
            '/div'
        ], $toolbar->render());
    }

    public function testDelete()
    {
        ToolbarHelper::setToolbar(__FUNCTION__);
        ToolbarHelper::delete();

        $toolbar = Toolbar::getInstance(__FUNCTION__);

        $this->assertHtml([
            ['div' => ['id' => 'test-delete-core-action', 'class' => 'item-wrapper tb-item-1 first last']],
                'button' => [
                    'class'       => 'jsProcessDelete ck-button waves-effect waves-light btn red lighten-2',
                    'data-action' => 'delete',
                    'type'        => 'submit',
                ],
                    ['i' => ['class' => 'ck-icon fa fa-trash']], '/i',
                    'Delete',
                '/button',
            '/div',
        ], $toolbar->render());
    }

    public function testLink()
    {
        ToolbarHelper::setToolbar(__FUNCTION__);
        ToolbarHelper::link('Link title', 'http://google.com');

        $toolbar = Toolbar::getInstance(__FUNCTION__);

        $this->assertHtml([
            ['div' => ['id' => 'test-link-core-link', 'class' => 'item-wrapper tb-item-1 first last']],
                'a' => [
                    'href'  => 'http://google.com',
                    'class' => 'ck-link btn btn-grey lighten-3',
                    'title' => 'Link title'
                ],
                    ['i' => ['class' => 'ck-icon fa fa-link']], '/i',
                    'span' => ['class' => 'ck-link-title'],
                        'Link title',
                    '/span',
                '/a',
            '/div',
        ], $toolbar->render());
    }

    public function testSave()
    {
        ToolbarHelper::setToolbar(__FUNCTION__);
        ToolbarHelper::save();

        $toolbar = Toolbar::getInstance(__FUNCTION__);

        $this->assertHtml([
            'div' => ['id' => 'test-save-core-action', 'class' => 'item-wrapper tb-item-1 first last'],
                'button' => [
                    'type'        => 'submit',
                    'data-action' => 'save',
                    'class'       => 'jsFormButton ck-button waves-effect waves-light btn grey lighten-3'
                ],
                    'i' => ['class' => 'ckTextGreen ck-icon fa fa-check'], '/i',
                    'Save',
                '/button',
            '/div'
        ], $toolbar->render());
    }

    public function testApply()
    {
        ToolbarHelper::setToolbar(__FUNCTION__);
        ToolbarHelper::apply();

        $toolbar = Toolbar::getInstance(__FUNCTION__);

        $this->assertHtml([
            'div' => ['id' => 'test-apply-core-action', 'class' => 'item-wrapper tb-item-1 first last'],
                'button' => [
                    'type'        => 'submit',
                    'data-action' => 'apply',
                    'class'       => 'jsFormButton ck-button waves-effect waves-light btn green lighten-2'
                ],
                    'i' => ['class' => 'ck-icon fa fa-check-square-o'], '/i',
                    'Apply',
                '/button',
            '/div'
        ], $toolbar->render());
    }
}
