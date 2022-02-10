namespace App\MethodFactory;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;

class DompdfReport implements ReportInterface
{
    private $view = 'pdf.order';
    /**
     * @var PDF
     */
    private $pdf;
    /**
     * DompdfReport constructor.
     * @param PDF $pdf
     */
    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
    }
    /**
     * @param $data
     * @return ReportInterface
     */
    public function fromRequest($data) : ReportInterface
    {
        $this->pdf->loadView($this->view, ['order' => $data]);
        return $this;
    }
    /**
     * @param $filename
     * @return Response
     */
    public function download($filename): Response
    {
        return $this->pdf->download($filename);
    }
}