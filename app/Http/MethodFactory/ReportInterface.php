namespace App\MethodFactory;

use Illuminate\Http\Response;

interface ReportInterface
{
    /**
     * @param $data
     * @return ReportInterface
     */
    public function fromRequest($data): ReportInterface;
    /**
     * @param $filename
     * @return Response
     */
    public function download($filename): Response;
}