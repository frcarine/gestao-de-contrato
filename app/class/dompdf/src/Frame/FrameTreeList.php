<?php
namespace app\class\dompdf\src\Frame;

use app\class\dompdf\src\Frame\FrameTreeIterator;
use IteratorAggregate;
use app\class\dompdf\src\Frame;

/**
 * Pre-order IteratorAggregate
 *
 * @access private
 * @package dompdf
 */
class FrameTreeList implements IteratorAggregate
{
    /**
     * @var \app\class\dompdf\src\Frame
     */
    protected $_root;

    /**
     * @param \app\class\dompdf\src\Frame $root
     */
    public function __construct(Frame $root)
    {
        $this->_root = $root;
    }

    /**
     * @return FrameTreeIterator
     */
    public function getIterator()
    {
        return new FrameTreeIterator($this->_root);
    }
}
