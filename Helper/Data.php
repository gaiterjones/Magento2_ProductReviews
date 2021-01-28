<?php

declare(strict_types=1);

namespace Gaiterjones\ProductReviews\Helper;

/**
 * Helper functions for other classes
 */
class Data
{

    /**
     * Get list of child/associated product ids
     *
     * @return  string (comma delimited list for grouped / configurable)
     */
     public function getProductIds($product)
     {
         if ($product instanceof \Magento\Catalog\Model\Product)
         {
             $productType=$product->getTypeId();

             // Get Configurable product child IDs
             //
             if ($productType == 'configurable')
             {
                 $children=$product->getTypeInstance()->getUsedProductCollection($product)->getData();
                 if ($children && isset($children[0]['entity_id']))
                 {
                     $productIds[]=$product->getId();

                     foreach ($children as $child)
                     {
                         $productIds[]=$child['entity_id'];
                     }

                     return (implode(',',$productIds));
                 }

             }

             // Get Grouped product associated product IDs
             //
             if ($productType == 'grouped')
             {
                 $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);

                 $productIds[]=$product->getId();

                 foreach ($associatedProducts as $child)
                 {
                     $productIds[]=$child->getId();
                 }

                 return (implode(',',$productIds));

             }

             return $product->getId();

         }

         return null;
     }
}
