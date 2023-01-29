<?php

//pdf.php

namespace app\class;
require_once 'dompdf/autoload.inc.php';

use class\dompdf\src\Dompdf;

class Pdf extends Dompdf
{
    public function __construct()
    {
        parent::__construct();
    }
}


?>