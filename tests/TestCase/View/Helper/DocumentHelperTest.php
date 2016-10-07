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

namespace Core\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Core\Plugin;
use Core\View\Helper\DocumentHelper;
use Core\TestSuite\IntegrationTestCase;

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

    public function testClassName()
    {
        $this->assertInstanceOf('Core\View\Helper\DocumentHelper', $this->_helper());
    }

    public function testProperties()
    {
        $this->_helper()->initialize([]);

        $this->assertSame('ltr', $this->_helper()->dir);
        $this->assertSame('utf-8', $this->_helper()->charset);
        $this->assertSame('ru_RU', $this->_helper()->locale);
        $this->assertSame(PHP_EOL, $this->_helper()->eol);
        $this->assertSame('    ', $this->_helper()->tab);

        Configure::write('debug', 0);

        $doc = new DocumentHelper($this->View);
        $this->assertEmpty($doc->eol);
        $this->assertEmpty($doc->tab);

        Configure::write('debug', 1);
    }
    
    public function testLang()
    {
        $this->assertSame('ru', $this->_helper()->lang());
        $this->assertSame('ru', $this->_helper()->lang(false));

        Configure::write('App.defaultLocale', 'en_GB');
        $doc = new DocumentHelper($this->View);

        $this->assertSame('en', $doc->lang());
        $this->assertSame('gb', $doc->lang(false));
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

        $this->assertSame($expected, $this->_helper()->type());
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

        $this->assertSame($expected, $output);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title on block</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
        ];

        $this->assertNull($this->_helper()->meta($expected, 'meta'));

        $expected = implode($this->_helper()->eol, $expected) . $this->_helper()->eol;
        $this->assertSame($expected, $this->View->fetch('meta'));

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title on block</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
            '<meta http-equiv="Content-Language" content="en">',
        ];

        $expected = implode($this->_helper()->eol, $expected) . $this->_helper()->eol;

        $this->_helper()->meta(['<meta http-equiv="Content-Language" content="en">'], 'meta');

        $this->assertSame($expected, $this->View->fetch('meta'));
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

    public function testActionWidget()
    {
        $this->_url['controller'] = 'Metadata';
        $this->_url['action'] = 'form';

        $this->get($this->_url);
        $this->assertResponseOk();
        $this->assertResponseContains('test/js/widget/metadata-form.js');
    }
}
