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

JBZoo.widget('JBZoo.FieldToggle', {
    'token' : ''
}, {

    /**
     * Toggle field.
     *
     * @param e
     * @param $this
     */
    'click .jsToggle' : function (e, $this) {
        var element = $(this);
        var url     = element.children('.ck-link').data('url');
        var $icon   = $this.$('.ck-icon', element);

        $icon.removeClass('fa-circle').addClass('fa-spin fa-spinner un-orange');

        $.ajax({
            'url'       : url,
            'type'      : 'post',
            'dateType'  : 'json',

            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $this.getOption('token'));
            },

            success : function (data) {
                data = $.parseJSON(data);
                element.html(data.output)
            }
        });
    }
});
