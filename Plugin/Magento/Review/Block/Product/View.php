<?php
declare(strict_types=1);

namespace Gaiterjones\ProductReviews\Plugin\Magento\Review\Block\Product;
use Gaiterjones\ProductReviews\Helper\Data as Helper;

class View
{
    /**
     * Review collection
     *
     * @var ReviewCollection
     */
    protected $_reviewsCollection;
    /**
     * Registry
     *
     * @var coreRegistry
     */
    protected $_coreRegistry;
    /**
     * Store Manager
     *
     * @var StoreManager
     */
    protected $_storeManager;
    /**
     * helper
     *
     * @var Helper
     */
    private $_helper;

    /**
     * Review resource model
     *
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $_reviewsColFactory;

    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Helper $helper
    ) {
        $this->_reviewsColFactory = $collectionFactory;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_helper=$helper;
    }

    // plugin around \Magento\Review\Block\Product\View::ReviewsCollection
    // returns review collection to parent including associated (grouped)
    // and child (configurable) product reviews
    //
    public function aroundgetReviewsCollection(
        \Magento\Review\Block\Product\View $subject,
        \Closure $proceed
    ) {

        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
                $this->getStoreId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProductIds()
            )->setDateOrder();
        }
        return $this->_reviewsCollection;

    }

    /**
     * Get list of child/associated product ids
     *
     * @return  string (comma delimited list for grouped / configurable)
     */
    public function getProductIds()
    {
        return $this->_helper->getProductIds($this->_coreRegistry->registry('product'));
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
