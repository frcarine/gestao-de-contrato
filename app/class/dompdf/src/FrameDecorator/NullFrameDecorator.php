<?php
/**
 * @package dompdf
 * @link    http://dompdf.github.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
namespace app\class\dompdf\src\FrameDecorator;

use app\class\dompdf\src\Dompdf;
use app\class\dompdf\src\Frame;
use app\class\dompdf\src\FrameDecorator\AbstractFrameDecorator;

/**
 * Dummy decorator
 *
 * @package dompdf
 */
class NullFrameDecorator extends AbstractFrameDecorator
{
    /**
     * NullFrameDecorator constructor.
     * @param Frame $frame
     * @param Dompdf $dompdf
     */
    function __construct(Frame $frame, Dompdf $dompdf)
    {
        parent::__construct($frame, $dompdf);
        $style = $this->_frame->get_style();
        $style->width = 0;
        $style->height = 0;
        $style->margin = 0;
        $style->padding = 0;
    }
}
