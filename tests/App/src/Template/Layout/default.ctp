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

$this->Document->meta([
    '<title>' . $this->fetch('page_title') . '</title>',
    '<meta name="keywords" content="' . $this->fetch('meta_keywords') . '" />',
    '<meta name="description" content="' . $this->fetch('meta_description') . '" />',
    '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
    '<meta name="viewport" content="width=device-width, initial-scale=1">',
], 'meta');

echo $this->Document->type();
?>
<head>
    <?php
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('css_bottom');
    ?>
</head>
<body>
<?= $this->fetch('content') ?>
<?= $this->fetch('script') ?>
<?= $this->fetch('script_bottom') ?>
</body>
</html>
