namespace App\MethodFactory;

use Barryvdh\Snappy\PdfWrapper;
use Illuminate\Http\Response;

class SnappyReport implements ReportInterface
{
    private $view = 'pdf.order';
    /**
     * @var PdfWrapper
     */
    private $pdf;
    public function __construct(PdfWrapper $pdf)
    {
        $this->pdf = $pdf;
    }
    /**
     * @param $data
     * @return ReportInterface
     */
    public function fromRequest($data): ReportInterface
    {
        $this->pdf->loadView($this->view, ['order' =>$data]);
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