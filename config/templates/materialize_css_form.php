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

return [
    'file' => implode(PHP_EOL, [
        '<div class="file-field input-field">',
            '<div class="btn">',
                '<span>{{title}}</span>',
                '<input type="file" name="{{name}}">',
            '</div>',
                '<div class="file-path-wrapper">',
                '<input class="file-path" type="text">',
            '</div>',
        '</div>'
    ]),
    'formGroup'           => "{{input}}\n{{label}}",
    'inputContainer'      => '<div class="input-field {{type}}{{required}}">{{before}}{{content}}{{after}}</div>',
    'nestingLabel'        => '{{hidden}}{{input}}<label{{attrs}}><span>{{text}}</span></label>',
    'inputContainerError' => '<div class="input-field {{type}}{{required}} error">{{before}}{{content}}{{after}}{{error}}</div>',
    'switcher'            => '<div class="switch">{{title}}<label>{{before}}{{input}}{{lever}}{{after}}</label></div>'
];
