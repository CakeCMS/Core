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
use Core\View\Helper\DocumentHelper;

/**
 * Class DocumentHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @property \Core\View\Helper\DocumentHelper $Document
 */
class DocumentHelperTest extends HelperTestCase
{

    protected $_name = 'Document';
    protected $_plugin = 'Core';

    public function testClassName()
    {
        $this->assertInstanceOf('Core\View\Helper\DocumentHelper', $this->Document);
    }

    public function testProperties()
    {
        $this->Document->initialize([]);

        $this->assertSame('ltr', $this->Document->dir);
        $this->assertSame('utf-8', $this->Document->charset);
        $this->assertSame('ru_RU', $this->Document->locale);
        $this->assertSame(PHP_EOL, $this->Document->eol);
        $this->assertSame('    ', $this->Document->tab);

        Configure::write('debug', 0);

        $doc = new DocumentHelper($this->View);
        $this->assertEmpty($doc->eol);
        $this->assertEmpty($doc->tab);

        Configure::write('debug', 1);
    }
    
    public function testLang()
    {
        $this->assertSame('ru', $this->Document->lang());
        $this->assertSame('ru', $this->Document->lang(false));

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

        $expected = implode($this->Document->eol, $expected) . $this->Document->eol;

        $this->assertSame($expected, $this->Document->type());
    }

    public function testMeta()
    {
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
        ];

        $output   = $this->Document->meta($expected);
        $expected = implode($this->Document->eol, $expected) . $this->Document->eol;

        $this->assertSame($expected, $output);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title on block</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
        ];

        $this->assertNull($this->Document->meta($expected, 'meta'));

        $expected = implode($this->Document->eol, $expected) . $this->Document->eol;
        $this->assertSame($expected, $this->View->fetch('meta'));

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $expected = [
            '<meta charset="utf-8"/>',
            '<title>Page title on block</title>',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
            '<meta http-equiv="Content-Language" content="en">',
        ];

        $expected = implode($this->Document->eol, $expected) . $this->Document->eol;

        $this->Document->meta(['<meta http-equiv="Content-Language" content="en">'], 'meta');

        $this->assertSame($expected, $this->View->fetch('meta'));
    }
}