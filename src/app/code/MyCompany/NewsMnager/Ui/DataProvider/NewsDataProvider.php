<?php

namespace MyCompany\NewsManager\Ui\DataProvider;

use MyCompany\NewsManager\Model\ResourceModel\News\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;

class NewsDataProvider extends AbstractDataProvider
{
  protected $collection;
  protected $dataPersistor;
  protected $loadedData;

  public function __construct(
    $name,
    $primaryFieldName,
    $requestFieldName,
    CollectionFactory $collectionFactory,
    DataPersistorInterface $dataPersistor,
    array $meta = [],
    array $data = []
  ) {
    $this->collection = $collectionFactory->create();
    $this->dataPersistor = $dataPersistor;
    parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
  }

  public function getData()
  {
    if (isset($this->loadedData)) {
      return $this->loadedData;
    }
    $items = $this->collection->getItems();
    foreach ($items as $model) {
      $data = $model->getData();
      // تحميل معرفات التصنيفات للنموذج
      $data['category_ids'] = $model->getCategoryIds();
      $this->loadedData[$model->getId()] = $data;
    }

    // استعادة البيانات بعد فشل الحفظ
    $data = $this->dataPersistor->get('news_article');
    if (!empty($data)) {
      $model = $this->collection->getNewEmptyItem();
      $model->setData($data);
      $this->loadedData[$model->getId()] = $model->getData();
      $this->dataPersistor->clear('news_article');
    }

    return $this->loadedData;
  }
}
