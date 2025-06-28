<?php

namespace MyCompany\NewsManager\Model\ResourceModel\News;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
  protected $_idFieldName = 'article_id';
  protected $_eventPrefix = 'news_article_collection';
  protected $_eventObject = 'news_collection';

  protected function _construct()
  {
    $this->_init(
      \MyCompany\NewsManager\Model\News::class,
      \MyCompany\NewsManager\Model\ResourceModel\News::class
    );
  }

  /**
   * تصفية الأخبار حسب المستخدم
   */
  public function addCustomerFilter($customerId)
  {
    $this->addFieldToFilter('customer_id', $customerId);
    return $this;
  }

  /**
   * تصفية الأخبار حسب الحالة
   */
  public function addStatusFilter($status = 1)
  {
    $this->addFieldToFilter('status', $status);
    return $this;
  }

  /**
   * ربط الأخبار مع التصنيفات
   */
  public function joinCategories()
  {
    $this->getSelect()
      ->joinLeft(
        ['nac' => $this->getTable('news_article_category')],
        'main_table.article_id = nac.article_id',
        []
      )
      ->joinLeft(
        ['nc' => $this->getTable('news_category')],
        'nac.category_id = nc.category_id',
        ['category_names' => 'GROUP_CONCAT(nc.name SEPARATOR ", ")']
      )
      ->group('main_table.article_id');
    return $this;
  }
}
