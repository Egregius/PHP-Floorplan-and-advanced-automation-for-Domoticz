<?php
/**
 * User preferences form
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

namespace PhpMyAdmin\Config\Forms\Page;

use PhpMyAdmin\Config\Forms\BaseForm;
use PhpMyAdmin\Config\Forms\User\MainForm;

/**
 * @package PhpMyAdmin\Config\Forms\Page
 */
class BrowseForm extends BaseForm
{
    /**
     * @return array
     */
    public static function getForms()
    {
        return [
            'Browse' => MainForm::getForms()['Browse'],
        ];
    }
}
