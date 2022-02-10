namespace App\MethodFactory;

use InvalidArgumentException;

class ReportFactory implements ReportFactoryInterface
{
    private $app;
    private $aliases = [
        'dompdf' => 'dompdf.wrapper',
        'snappy' => 'snappy.pdf.wrapper',
    ];
    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
     * @param $type
     * @return ReportInterface
     * @throws InvalidArgumentException
     */
    public function create($type) : ReportInterface
    {
        $reportClass = __NAMESPACE__.'\\'.ucfirst($type).'Report';
        if (!array_key_exists($type, $this->aliases)) {
            throw new InvalidArgumentException("Report {$type} does not exist");
        }
        if (!class_exists($reportClass)) {
            throw new InvalidArgumentException("Class {$reportClass} does not exist");
        }
        return new $reportClass($this->app->make($this->aliases[$type]));
    }
}