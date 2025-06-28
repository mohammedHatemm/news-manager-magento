<?php

namespace MyCompany\NewsManager\Controller\Adminhtml\News;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MyCompany\NewsManager\Model\NewsFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
  const ADMIN_RESOURCE = 'MyCompany_NewsManager::news_edit';
  protected $newsFactory;

  public function __construct(
    Context $context,
    NewsFactory $newsFactory
  ) {
    parent::__construct($context);
    $this->newsFactory = $newsFactory;
  }

  public function execute()
  {
    $resultRedirect = $this->resultRedirectFactory->create();
    $data = $this->getRequest()->getPostValue();

    if ($data) {
      $id = $this->getRequest()->getParam('id');
      $model = $this->newsFactory->create();
      if ($id) {
        $model->load($id);
        if (!$model->getId()) {
          $this->messageManager->addErrorMessage(__('This news article no longer exists.'));
          return $resultRedirect->setPath('*/*/');
        }
      }

      $model->setData($data);

      try {
        $model->save();

        // حفظ علاقة التصنيفات
        if (isset($data['category_ids'])) {
          $categoryIds = is_array($data['category_ids']) ? $data['category_ids'] : [];
          $model->saveCategoryRelation($categoryIds);
        }

        $this->messageManager->addSuccessMessage(__('The news article has been saved.'));
        if ($this->getRequest()->getParam('back')) {
          return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
      } catch (LocalizedException $e) {
        $this->messageManager->addErrorMessage($e->getMessage());
      } catch (\Exception $e) {
        $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news article.'));
      }

      return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
    return $resultRedirect->setPath('*/*/');
  }
}
