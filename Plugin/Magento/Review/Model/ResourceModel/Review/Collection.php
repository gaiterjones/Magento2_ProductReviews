<?php
declare(strict_types=1);

namespace Gaiterjones\ProductReviews\Plugin\Magento\Review\Model\ResourceModel\Review;

class Collection
{
    public function aroundAddEntityFilter(
        \Magento\Review\Model\ResourceModel\Review\Collection $subject,
        \Closure $proceed,
        $entity,
        $pkValue
    ) {

        $reviewEntityTable = $subject->getTable('review_entity');
        if (is_numeric($entity)) {
            $subject->addFilter('entity', $subject->getConnection()->quoteInto('main_table.entity_id=?', $entity), 'string');
        } elseif (is_string($entity)) {
            $subject->getSelect()->join(
                $reviewEntityTable,
                'main_table.entity_id=' . $reviewEntityTable . '.entity_id',
                ['entity_code']
            );

            $subject->addFilter(
                'entity',
                $subject->getConnection()->quoteInto($reviewEntityTable . '.entity_code=?', $entity),
                'string'
            );
        }

        $subject->addFilter(
            'entity_pk_value',
            //$subject->getConnection()->quoteInto('main_table.entity_pk_value=?', $pkValue),
            $subject->getConnection()->quoteInto('main_table.entity_pk_value in (?)', explode(',',$pkValue)),
            'string'
        );

        return $subject;

    }
}
