<?php

namespace Forever\AuthenticationPopUp\Controller\Ajax;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Ajax Login Controller for Magento 2.4.9 / PHP 8.4
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Login implements ActionInterface, HttpPostActionInterface
{
    /**
     * @param RequestInterface $request
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param JsonFactory $resultJsonFactory
     * @param RawFactory $resultRawFactory
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly Session $customerSession,
        private readonly AccountManagementInterface $customerAccountManagement,
        private readonly JsonFactory $resultJsonFactory,
        private readonly RawFactory $resultRawFactory,
        private readonly JsonSerializer $jsonSerializer
    ) {
    }

    /**
     * Login registered users and initiate a session.
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        $httpBadRequestCode = 400;
        $resultRaw = $this->resultRawFactory->create();

        try {
            $credentials = $this->jsonSerializer->unserialize($this->request->getContent());
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        if (!$credentials
            || $this->request->getMethod() !== 'POST'
            || !$this->request->isXmlHttpRequest()
        ) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors'  => false,
            'message' => __('Login successful.')
        ];

        try {
            $customer = $this->customerAccountManagement->authenticate(
                $credentials['username'],
                $credentials['password']
            );
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->regenerateId();
        } catch (EmailNotConfirmedException $e) {
            $response = [
                'errors'  => true,
                'message' => $e->getMessage()
            ];
        } catch (InvalidEmailOrPasswordException $e) {
            $response = [
                'errors'  => true,
                'message' => $e->getMessage()
            ];
        } catch (LocalizedException $e) {
            $response = [
                'errors'  => true,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $response = [
                'errors'  => true,
                'message' => __('Invalid login or password.')
            ];
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
