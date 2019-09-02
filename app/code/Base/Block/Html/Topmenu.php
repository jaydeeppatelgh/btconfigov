<?php
namespace Borntechies\Base\Block\Html;
use Magento\Theme\Block\Html\Topmenu as BaseMenu;

/**
 * Class Topmenu
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Topmenu extends  BaseMenu
{
    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $html .= '<ul class="level' . $childLevel . ' submenu">';
        if ($childLevel == 0) {
            $html .= '<li class="menu-main-title"><h2>' . $child->getName() .'</h2></li>';
        }
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        return $html;
    }
}