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

namespace Core\Test\TestCase\Notify;

use Core\Notify\Email;
use Test\Cases\TestCase;

/**
 * Class EmailTest
 *
 * @package Core\Test\TestCase\Notify
 */
class EmailTest extends TestCase
{

    /**
     * Setup test data.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $_SERVER['SERVER_NAME'] = 'localhost.cms';
        $_SERVER['SERVER_PORT'] = '80';
    }

    /**
     * Test send email.
     *
     * @return void
     */
    public function testSend()
    {
        $email  = new Email();
        $result = $email->send('My subject', 'My content', 'trat@mail.ru');

        self::assertSame(['headers', 'message'], array_keys($result));
        self::assertContains('My content', $result['message']);
        self::assertContains('From: CMS <noreply@mail.cms>', $result['headers']);
        self::assertContains('Date:', $result['headers']);
        self::assertContains('Message-ID:', $result['headers']);
        self::assertContains('Content-Type: text/html; charset=UTF-8', $result['headers']);

        $result = $email->send('My subject 2', 'My content 2', 'tester@mail.com', 'MyFromName', 'no-reply@test.com');
        self::assertSame(['headers', 'message'], array_keys($result));
        self::assertContains('My content 2', $result['message']);
        self::assertContains('From: MyFromName <no-reply@test.com>', $result['headers']);
        self::assertContains('Date:', $result['headers']);
        self::assertContains('Message-ID:', $result['headers']);
        self::assertContains('Content-Type: text/html; charset=UTF-8', $result['headers']);
    }
}
