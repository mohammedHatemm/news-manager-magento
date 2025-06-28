<?php

namespace MyCompany\NewsManager\Model;

use Magento\Framework\Model\AbstractModel;

use Magento\Framework\DataObject\IdentityInterface;


class Category extends AbstractModel implements IdentityInterface
{
  const CACHE_TGE = "news_category";
  const STATUS_ENABLED = 1;
  const status_DISABLED = 0;

  protected $_chacheTag = "news_category";
  protected $_eventPrefix = "news_category";

  protected function _construct()
  {
    $this->_init(\MyCompany\MyManger\Model\ResourceModel\Categpry::class);
  }

  public function getIdentities()
  {
    return [self::CACHE_TGE . '_' . $this->getId()];
  }


  public function getNewsCount()
  {
    $connsction = $this->getResource()->getConnection();
    $select = $connection->select()->from($this->get_resources()->getTabl('news_article_category'), 'COUNT(*)')
      ->where('category_id=?' . $this->getId());


    return $connection->fetchone($select);
  }
}
