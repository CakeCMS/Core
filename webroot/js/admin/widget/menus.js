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

JBZoo.widget('JBZoo.Menus', {}, {

    init: function ($this) {
        var active = $this.$('li.active');
        var parents = active.parents('.li-item');
        parents.each(function () {
            var link = $(this).find('.ck-link');
            if (link.attr('href') == '#') {
                link.click();
            }
        });
    }

});
