<?php
declare(strict_types=1);

namespace Gaiterjones\ProductReviews\Plugin\Magento\Review\Model;
use Gaiterjones\ProductReviews\Helper\Data as Helper;
use Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory as SummaryCollectionFactory;
use Magento\Framework\Model\AbstractModel;

class ReviewSummary
{
    /**
     * @var SummaryCollectionFactory
     */
    private $summaryCollectionFactory;
    /**
     * helper
     *
     * @var Helper
     */
    private $_helper;

    /**
     * @param SummaryCollectionFactory $sumColFactory
     */
    public function __construct(
        SummaryCollectionFactory $sumColFactory,
        Helper $helper
    ) {
        $this->summaryCollectionFactory = $sumColFactory;
        $this->_helper=$helper;
    }

    public function aroundappendSummaryDataToObject(
        \Magento\Review\Model\ReviewSummary $subject,
        \Closure $proceed,
        AbstractModel $object,
        int $storeId,
        int $entityType = 1
    ) {

        $ids=$this->getProductIds($object);

        $summaryData = $this->summaryCollectionFactory->create()
            ->addEntityFilter(explode(',',$ids), $entityType)
            ->addStoreFilter($storeId);

        $reviewCount=0;
        $ratings=array();
        $ratingsTotal=0;
        $ratingSummary=0;

        foreach ($summaryData as $summary)
        {
            $reviewCount=$reviewCount+$summary->getData('reviews_count');
            $ratings[]=$summary->getData('rating_summary');
        }

        if ($reviewCount>0)
        {
            $ratingsTotal = array_sum($ratings);
            $ratingSummary = $ratingsTotal/count($ratings);
        }

        $object->addData(
            [
                'reviews_count' => $reviewCount,
                'rating_summary' => $ratingSummary
            ]
        );

    }

    public function getProductIds($product)
    {
        return $this->_helper->getProductIds($product);
    }
}
