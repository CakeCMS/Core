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

namespace Core\Notify;

use Cake\ORM\Entity;
use Cake\Mailer\Email as CakeEmail;

/**
 * Class Email
 *
 * @package Core\Notify
 */
class Email
{

    const DEFAULT_MSG_TPL = 'Core.message';

    /**
     * Entity object.
     *
     * @var Entity
     */
    protected $_data;

    /**
     * Message template.
     *
     * @var string
     */
    protected $_tpl;

    /**
     * Server name from env().
     *
     * @var string
     */
    protected $_serverName;

    /**
     * @var string
     */
    protected $_fromName = 'CMS';

    /**
     * @var string
     */
    protected $_fromEmail = 'noreply@mail.cms';

    /**
     * @var string
     */
    protected $_format = 'html';

    /**
     * Mail constructor.
     *
     * @param Entity|array $data
     * @param string $tpl
     */
    public function __construct($data = [], $tpl = self::DEFAULT_MSG_TPL)
    {
        if (!($data instanceof Entity)) {
            $data = new Entity((array) $data);
        }

        $this->_tpl  = $tpl;
        $this->_data = $data;
        $this->_initialize();
    }

    /**
     * Send message method.
     *
     * @param string $subject
     * @param string $content
     * @param string|array $to
     * @param string $fromName
     * @param string $fromEmail
     * @return array
     */
    public function send($subject, $content, $to, $fromName = '', $fromEmail = '')
    {
        $mail      = new CakeEmail();
        $fromName  = ($fromName !== '') ? $fromName : $this->_fromName;
        $fromEmail = ($fromEmail !== '') ? $fromEmail : $this->_fromEmail;

        return $mail
            ->setTemplate($this->_tpl)
            ->setEmailFormat($this->_format)
            ->setFrom($fromEmail, $fromName)
            ->setTo($to)
            ->setSubject($subject)
            ->setViewVars(['page_title' => $subject])
            ->send($content);
    }

    /**
     * Constructor hook method.
     *
     * @return void
     */
    protected function _initialize()
    {
        $this->_serverName = preg_replace('#^www\.#', '', mb_strtolower(env('SERVER_NAME')));
    }
}
