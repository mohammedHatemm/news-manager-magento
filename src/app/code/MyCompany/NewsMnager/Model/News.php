<?php

namespace MyCompany\NewsManager\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class News extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'news_article';
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_cacheTag = 'news_article';
    protected $_eventPrefix = 'news_article';

    protected function _construct()
    {
        $this->_init(\MyCompany\NewsManager\Model\ResourceModel\News::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * الحصول على معرفات التصنيفات المرتبطة بهذا الخبر
     */
    public function getCategoryIds()
    {
        if (!$this->getId()) {
            return [];
        }
        $connection = $this->getResource()->getConnection();
        $select = $connection->select()
            ->from($this->getResource()->getTable('news_article_category'), 'category_id')
            ->where('article_id = ?', $this->getId());
        return $connection->fetchCol($select);
    }

    /**
     * حفظ علاقة الخبر مع التصنيفات
     */
    public function saveCategoryRelation($categoryIds)
    {
        $connection = $this->getResource()->getConnection();
        $table = $this->getResource()->getTable('news_article_category');

        // حذف العلاقات القديمة
        $connection->delete($table, ['article_id = ?' => $this->getId()]);

        // إدراج العلاقات الجديدة
        if (!empty($categoryIds)) {
            $data = [];
            foreach ($categoryIds as $categoryId) {
                $data[] = [
                    'article_id' => $this->getId(),
                    'category_id' => $categoryId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }

    /**
     * التحقق من صلاحية المستخدم لتعديل هذا الخبر
     */
    public function canEdit($customerId, $isAdmin = false)
    {
        if ($isAdmin) {
            return true;
        }
        return $this->getCustomerId() == $customerId;
    }
}
