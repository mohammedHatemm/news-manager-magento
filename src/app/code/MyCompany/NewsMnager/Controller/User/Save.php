<?php

namespace MyCompany\NewsManager\Controller\User;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use MyCompany\NewsManager\Model\NewsFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
  protected $customerSession;
  protected $newsFactory;

  public function __construct(
    Context $context,
    Session $customerSession,
    NewsFactory $newsFactory
  ) {
    parent::__construct($context);
    $this->customerSession = $customerSession;
    $this->newsFactory = $newsFactory;
  }

  public function execute()
  {
    $resultRedirect = $this->resultRedirectFactory->create();
    if (!$this->customerSession->isLoggedIn()) {
      return $resultRedirect->setPath('customer/account/login');
    }
    $data = $this->getRequest()->getPostValue();
    $customerId = $this->customerSession->getCustomerId();

    if ($data) {
      $id = $this->getRequest()->getParam('id');
      $model = $this->newsFactory->create();

      if ($id) {
        $model->load($id);
        // التحقق من أن المستخدم هو صاحب المقال
        if (!$model->getId() || !$model->canEdit($customerId)) {
          $this->messageManager->addErrorMessage(__('You are not authorized to save this article.'));
          return $resultRedirect->setPath('*/*/');
        }
      } else {
        // مقال جديد - تعيين معرف المستخدم واسمه
        $data['customer_id'] = $customerId;
        $data['author_name'] = $this->customerSession->getCustomer()->getName();
      }

      $model->setData($data);
      try {
        $model->save();

        if (isset($data['category_ids'])) {
          $categoryIds = is_array($data['category_ids']) ? $data['category_ids'] : [];
          $model->saveCategoryRelation($categoryIds);
        }

        $this->messageManager->addSuccessMessage(__('Your article has been saved.'));
        return $resultRedirect->setPath('*/*/');
      } catch (LocalizedException $e) {
        $this->messageManager->addErrorMessage($e->getMessage());
      } catch (\Exception $e) {
        $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving your article.'));
      }
      return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
    return $resultRedirect->setPath('*/*/');
  }
}
