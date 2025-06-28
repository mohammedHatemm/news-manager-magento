<?php

namespace MyCompany\NewsManager\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class News extends AbstractDb
{
  protected function _construct()
  {
    $this->_init('news_article', 'article_id');
  }
}
