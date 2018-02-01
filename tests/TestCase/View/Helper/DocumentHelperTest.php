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

namespace Core\Test\TestCase\View\Helper;

use App\View\AppView;
use Cake\Core\Configure;
use Test\Cases\IntegrationTestCase;
use Core\View\Helper\DocumentHelper;

/**
 * Class DocumentHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\DocumentHelper _helper()
 */
class DocumentHelperTest extends HelperTestCase
{

    protected $_name = 'Document';
    protected $_plugin = 'Core';

    public function testAssets()
    {
        $this->_helper()->Assets
            ->bootstrap()
            ->fancyBox();

        $styles = $this->_helper()->assets('css');
        $this->assertHtml([
            ['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/libs/bootstrap.min.css']],
            ['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/libs/fancybox.min.css']],
        ], $styles);

        $scripts = $this->_helper()->assets('script');
        $this->assertHtml([
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/bootstrap.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/fancybox.min.js']], '/script',
        ], $scripts);
    }

    public function testClassName()
    {
        self::assertInstanceOf('Core\View\Helper\DocumentHelper', $this->_helper());
    }

    public function testHead()
    {
        $this->_helper()->Assets
            ->bootstrap()
            ->fancyBox();

        $this->_helper()->Html->css('styles.css', ['block' => 'css']);

        $this->assertHtml([
            ['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/libs/bootstrap.min.css']],
            ['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/libs/fancybox.min.css']],
            ['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/styles.css']],
        ], $this->_helper()->head());
    }

    public function testLang()
    {
        self::assertSame('ru', $this->_helper()->lang());
        self::assertSame('ru', $this->_helper()->lang(false));

        Configure::write('App.defaultLocale', 'en_GB');
        $doc = new DocumentHelper($this->View);

        self::assertSame('en', $doc->lang());
        self::assertSame('gb', $doc->lang(false));
    }

    public function testMeta()
    {
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
        ];

        $output   = $this->_helper()->meta($expected);
        $expected = implode($this->_helper()->eol, $expected) . $this->_helper()->eol;

        self::assertSame($expected, $output);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title on block</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
        ];

        self::assertNull($this->_helper()->meta($expected, 'meta'));

        $expected = implode($this->_helper()->eol, $expected) . $this->_helper()->eol;
        self::assertSame($expected, $this->View->fetch('meta'));

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title on block</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
            '<meta http-equiv="Content-Language" content="en">',
        ];

        $expected = implode($this->_helper()->eol, $expected) . $this->_helper()->eol;

        $this->_helper()->meta(['<meta http-equiv="Content-Language" content="en">'], 'meta');

        self::assertSame($expected, $this->View->fetch('meta'));
    }

    public function testProperties()
    {
        $this->_helper()->initialize([]);

        self::assertSame('ltr', $this->_helper()->dir);
        self::assertSame('utf-8', $this->_helper()->charset);
        self::assertSame('ru_RU', $this->_helper()->locale);
        self::assertSame(PHP_EOL, $this->_helper()->eol);
        self::assertSame('    ', $this->_helper()->tab);

        Configure::write('debug', 0);

        $doc = new DocumentHelper($this->View);
        self::assertEmpty($doc->eol);
        self::assertEmpty($doc->tab);

        Configure::write('debug', 1);
    }

    public function testType()
    {
        $expected = [
            '<!doctype html>',
            '<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7 ie6" lang="ru" dir="ltr"> <![endif]-->',
            '<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="ru" dir="ltr"> <![endif]-->',
            '<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="ru" dir="ltr"> <![endif]-->',
            '<!--[if gt IE 8]><!--><html class="no-js" xmlns="http://www.w3.org/1999/xhtml" lang="ru" dir="ltr" prefix="og: http://ogp.me/ns#" > <!--<![endif]-->'
        ];

        $expected = implode($this->_helper()->eol, $expected) . $this->_helper()->eol;

        self::assertSame($expected, $this->_helper()->type());
    }
}

/**
 * Class DocumentHelperTestIntegration
 *
 * @package Core\Test\TestCase\View\Helper
 */
class DocumentHelperTestIntegration extends IntegrationTestCase
{

    protected $_plugin = 'Test';

    public function testActionWidget()
    {
        $this->_url['controller'] = 'Metadata';
        $this->_url['action'] = 'form';

        $this->get($this->_url);
        $this->assertResponseOk();
        $this->assertResponseContains('test/js/widget/metadata-form.js');
    }

    public function testGetBodyClasses()
    {
        $this->_url['controller'] = 'Metadata';
        $this->_url['action'] = 'form';

        $this->get($this->_url);
        /** @var AppView $view */
        $view = $this->_controller->viewBuilder()->build();
        self::assertSame(
            'prefix-site theme- plugin-test view-metadata tmpl-form layout-default',
            $view->Document->getBodyClasses()
        );

        $passUrl = $this->_getUrl([
            'controller' => 'Metadata',
            'action' => 'form',
            10
        ]);

        $this->get($passUrl);
        /** @var AppView $view */
        $view = $this->_controller->viewBuilder()->build();
        self::assertSame(
            'prefix-site theme- plugin-test view-metadata tmpl-form layout-default item-id-10',
            $view->Document->getBodyClasses()
        );
    }

    public function testMetaDataFromController()
    {
        $this->_url['controller'] = 'Metadata';
        $this->_url['action'] = 'index';

        $this->get($this->_url);
        $this->assertResponseOk();
        $this->assertResponseContains('metadata index action');
        $this->assertResponseContains('<title>Test page title</title>');
        $this->assertResponseContains('<meta name="keywords" content="test, meta, key" />');
        $this->assertResponseContains('<meta name="description" content="test meta description" />');
    }

    public function testReloadMetaDataFromView()
    {
        $this->_url['controller'] = 'Metadata';
        $this->_url['action'] = 'form';

        $this->get($this->_url);
        $this->assertResponseOk();
        $this->assertResponseContains('metadata form action');
        $this->assertResponseContains('<title>Title from view</title>');
        $this->assertResponseContains('<meta name="keywords" content="key, from view" />');
        $this->assertResponseContains('<meta name="description" content="description from view" />');
    }
}
