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
 * @var       $this \Core\View\AppView
 * @var       $clearUrl string|array
 */

use JBZoo\Utils\Str;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Network\Exception\NotFoundException;

if (empty($model)) {
    throw new NotFoundException(__d('core', 'Not found model'));
}

$filterBtn  = [];
if (!empty($formFields)) : ?>
    <div class="un-filter un-placeholder">
        <?php
        echo $this->Form->create($model, [
            'class' => 'un-form-inline form-inline',
        ]);

        foreach ($formFields as $field => $fieldOptions) {
            if (is_numeric($field) && is_string($fieldOptions)) {
                $field = $fieldOptions;
                $fieldOptions = [];
            }

            $label   = Inflector::humanize(Inflector::underscore($field));
            $options = ['required' => false];

            if (!isset($options['label'])) {
                $options['label'] = __d(Str::low($this->plugin), $label);
            }

            if ($field == 'id') {
                $options['type'] = 'text';
            }

            if (count($fieldOptions) >= 1) {
                $options = Hash::merge($options, $fieldOptions);
            }

            echo $this->Form->input($field, $options);
        }

        $filterBtn[] = $this->Form->button(__d('core', 'Search'), [
            'div'    => false,
            'icon'   => 'search',
            'button' => 'success',
        ]);

        $btnClasses = [
            'cms-input',
            'form-group',
            'cms-filter-action',
        ];

        if (!empty($this->request->query)) {
            $filterBtn[] = $this->Html->link(__d('core', 'Clear'), $clearUrl, [
                'button' => 'info',
            ]);
            $btnClasses[] = 'btn-group';
        }

        echo $this->Html->div(implode(' ', $btnClasses), implode('', $filterBtn));

        echo $this->Form->end();
        ?>
    </div>
<?php endif;
