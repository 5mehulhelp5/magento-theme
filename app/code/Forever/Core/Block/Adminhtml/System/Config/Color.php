<?php
namespace Forever\Core\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends Field
{
    /**
     * @param object $element
     *
     * render color picker when click on element
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');
        $html .= '<script type="text/javascript">
            require(["jquery"], function($) {
                $(document).ready(function(e) {
                    $("#' . $element->getHtmlId() . '").css("background-color", "#' . $value . '");
                    $("#' . $element->getHtmlId() . '").colpick({
                        layout: "hex",
                        submit: 0,
                        colorScheme: "dark",
                        color: "#' . $value . '",
                        onChange: function(hsb, hex, rgb, el, bySetColor) {
                            $(el).css("background-color", "#" + hex);
                            if (!bySetColor) $(el).val("#" + hex);
                        }
                    }).keyup(function() {
                        $(this).colpickSetColor(this.value);
                    });
                });
            });
            </script>';
        return $html;
    }
}
