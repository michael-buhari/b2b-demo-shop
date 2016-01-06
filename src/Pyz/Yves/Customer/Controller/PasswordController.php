<?php

namespace Pyz\Yves\Customer\Controller;

use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Pyz\Yves\Customer\CustomerFactory;
use Pyz\Yves\Customer\Plugin\Provider\CustomerControllerProvider;
use Spryker\Client\Customer\CustomerClientInterface;
use Spryker\Shared\Customer\Code\Messages;
use Spryker\Yves\Application\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method CustomerFactory getFactory()
 * @method CustomerClientInterface getClient()
 */
class PasswordController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function forgottenPasswordAction(Request $request)
    {
        $form = $this
            ->buildForm($this->getFactory()->createFormForgottenPassword())
            ->handleRequest($request);

        if ($form->isValid()) {
            $customerResponseTransfer = $this->sendPasswordRecovery($form->getData());

            if ($customerResponseTransfer->getIsSuccess()) {
                $this->addSuccessMessage(Messages::CUSTOMER_PASSWORD_RECOVERY_MAIL_SENT);
            } else {
                foreach ($customerResponseTransfer->getErrors() as $errorTransfer) {
                    $this->addErrorMessage($errorTransfer->getMessage());
                }
            }
        }

        return $this->viewResponse([
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function restorePasswordAction(Request $request)
    {
        $form = $this
            ->buildForm($this->getFactory()->createFormRestorePassword($request->query->get('token')))
            ->handleRequest($request);

        if ($form->isValid()) {
            $customerResponseTransfer = $this->getClient()->restorePassword($form->getData());

            if ($customerResponseTransfer->getIsSuccess()) {
                $this->getClient()->logout();

                $this->addSuccessMessage(Messages::CUSTOMER_PASSWORD_CHANGED);

                return $this->redirectResponseInternal(CustomerControllerProvider::ROUTE_LOGIN);
            }

            foreach ($customerResponseTransfer->getErrors() as $errorTransfer) {
                $this->addErrorMessage($errorTransfer->getMessage());
            }
        }

        return $this->viewResponse([
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerResponseTransfer
     */
    protected function sendPasswordRecovery(CustomerTransfer $customerTransfer)
    {
        return $this->getClient()
            ->forgottenPassword($customerTransfer);
    }

}
