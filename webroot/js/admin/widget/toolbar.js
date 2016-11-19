/**
 * CakeCMS Core
 *
 * This file is part of the of the simple cms based on CakePHP 3.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Core
 * @license    MIT
 * @copyright  MIT License http://www.opensource.org/licenses/mit-license.php
 * @link       https://github.com/CakeCMS/Core
 */

JBZoo.widget('JBZoo.Toolbar', {
    'formInput'     : '.jsFormAction',
    'deleteMessage' : 'Are you sure you want to delete?'
}, {

    /**
     * Process delete action.
     *
     * @param e
     * @param $this
     */
    'click .jsProcessDelete' : function (e, $this) {
        var element = $(this);
        var action  = element.data('action');

        if (action !== '' && confirm($this.options.deleteMessage)) {
            $this._submitForm($this, action);
        }
    },

    /**
     * Form save action.
     *
     * @param e
     * @param $this
     */
    'click .jsFormAdd' : function (e, $this) {
        var element = $(this);
        var action  = element.data('action');
        $this._submitForm($this, action);
    },

    /**
     * Submit form.
     *
     * @param $this
     * @param action
     * @private
     */
    _submitForm : function ($this, action) {
        $($this.options.formInput).val(action);
        $($this.options.formInput).closest('.jsForm').submit();
    }
});
