<?php
namespace Neoflow\Module\DateTimePicker;

use DateTime;
use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Model\ModuleModel;
use RuntimeException;
use function convert_php2js;

class Service extends AbstractService
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var ModuleModel
     */
    protected $module;

    /**
     * Constructor.
     *
     * @param ModuleModel $module
     */
    public function __construct(ModuleModel $module)
    {
        $this->module = $module;

        $this->options['format'] = $this->translator()->getDateFormat(' H:i');

        $this->engine()
            ->addStylesheetUrl($this->module->getUrl('static/jquery.datetimepicker.min.css'))
            ->addJavascriptUrl($this->module->getUrl('static/jquery.datetimepicker.full.min.js'))
            ->addJavascript('
                (function() {
                    $.datetimepicker.setLocale("' . $this->translator()->getActiveLanguageCode() . '");
                })();
            ');
    }

    /**
     * Initialize DateTimePicker
     *
     * @param string $id ID of form input
     * @param array  $options Custom options for DateTimePicker
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function init(string $id, array $options = []): bool
    {
        $mergedOptions = array_merge($this->options, $options);

        if (isset($mergedOptions['timepicker']) && $mergedOptions['timepicker'] === false) {
            $mergedOptions['format'] = $this->translator()->getDateFormat();
        }

        $this->engine()->addJavascript('
                    (function() {
                        $("#' . $id . '").datetimepicker({
                            ' . convert_php2js($mergedOptions, false) . '
                        });
                    })();
            ');

        return true;
    }

    /**
     * Convert given date and time value of DateTimePicker to timestamp
     * @param string $value Given date and time value of input field
     * @param string $format Non-default format for date and time value
     * @return int
     */
    public function toTimestamp(string $value, string $format = ''): int
    {
        return $this
                ->toDateTime($value, $format)
                ->getTimestamp();
    }

    /**
     * Convert given date (and time) value of DateTimePicker to DateTime object
     * @param string $value Given date and time value of input field
     * @param string $format Non-default format for date and time value
     * @return DateTime
     */
    protected function toDateTime(string $value, string $format = ''): DateTime
    {
        if (!$format) {
            $format = $this->options['format'];
        }

        return DateTime::createFromFormat($format, $value);
    }
}
