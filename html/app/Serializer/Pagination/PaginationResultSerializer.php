<?php
namespace App\Serializer\Pagination;

use Knp\Component\Pager\Pagination\PaginationInterface;

class PaginationResultSerializer
{

    /**
     * @var PaginationInterface
     */
    protected $pagination;

    /**
     * @param PaginationInterface $pagination
     */
    public function __construct(?PaginationInterface $pagination = null)
    {
        $this->pagination = $pagination;
    }

    /**
     * @param PaginationInterface $pagination
     */
    public function setPagination(?PaginationInterface $pagination): void
    {
        $this->pagination = $pagination;
    }

    /**
     * @return array
     */
    public function toArray(?PaginationInterface $pagination = null)
    {
        if ($pagination) {
            $this->setPagination($pagination);
        }

        $paginationArray = [];
        foreach ((array) $this->pagination as $key => $value) {
            $k = str_replace(["\x00Knp\\Component\\Pager\\Pagination\\SlidingPagination\x00", "\x00*\x00"], ['',''], $key);
            $paginationArray[$k] = $value;
        }
        return $paginationArray;
    }

}