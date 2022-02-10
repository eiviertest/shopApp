namespace App\MethodFactory;

use InvalidArgumentException;

interface ReportFactoryInterface
{
    /**
     * @param $type
     * @return ReportInterface
     * @throws InvalidArgumentException
     */
    public function create($type): ReportInterface;
}