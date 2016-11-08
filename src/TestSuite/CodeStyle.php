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

namespace Core\TestSuite;

use Symfony\Component\Finder\Finder;
use JBZoo\PHPUnit\Codestyle as JBCodeStyle;

/**
 * Class CodeStyle
 *
 * @package Core\TestSuite
 */
class CodeStyle extends JBCodeStyle
{

    /**
     * Package name.
     *
     * @var string
     */
    protected $_packageName = ''; // Overload me!

    /**
     * Package vendor.
     *
     * @var string
     */
    protected $_packageVendor = 'CakeCMS';

    /**
     * Package link.
     *
     * @var string
     */
    protected $_packageLink = 'https://github.com/CakeCMS/_PACKAGE_';

    /**
     * Package copyright.
     *
     * @var string
     */
    protected $_packageCopyright = 'MIT License http://www.opensource.org/licenses/mit-license.php';

    /**
     * Package description.
     *
     * @var array
     */
    protected $_packageDesc = [
        'This file is part of the of the simple cms based on CakePHP 3.',
        'For the full copyright and license information, please view the LICENSE',
        'file that was distributed with this source code.'
    ];

    /**
     * Ignore list for.
     *
     * @var array
     */
    protected $_excludePaths = array(
        '.git',
        '.idea',
        'bin',
        'application',
        'bower_components',
        'build',
        'fonts',
        'fixtures',
        'logs',
        'node_modules',
        'resources',
        'vendor',
        'temp',
        'tmp',
        'webroot/css/cache'
    );

    /**
     * Try to find cyrilic symbols in the code.
     *
     * @return void
     */
    public function testCyrillic()
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in(PROJECT_ROOT)
            ->exclude($this->_excludePaths)
            ->exclude('tests')
            ->notPath(basename(__FILE__))
            ->notName('/\.md$/')
            ->notName('/empty/')
            ->notName('/\.min\.(js|css)$/')
            ->notName('/\.min\.(js|css)\.map$/');

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $content = \JBZoo\PHPUnit\openFile($file->getPathname());

            if (preg_match('#[А-Яа-яЁё]#ius', $content)) {
                \JBZoo\PHPUnit\fail('File contains cyrilic symbols: ' . $file); // Short message in terminal
            } else {
                \JBZoo\PHPUnit\success();
            }
        }
    }
}
