<?php

namespace Neoflow\CMS\Controller\Api;

use Exception;
use Neoflow\Framework\Core\AbstractController;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;

class UserController extends AbstractController
{
    /**
     * Logout action.
     *
     * @return JsonResponse
     */
    public function logoutAction(): JsonResponse
    {
        if ($this->service('auth')->logout()) {
            return new JsonResponse([
                'status' => true,
                'message' => translate('Successfully logged out'),
            ]);
        }

        return new JsonResponse([
            'status' => false,
            'message' => translate('Logout failed'),
        ]);
    }

    /**
     * Authentication and authorization action.
     *
     * @return JsonResponse
     */
    public function authAction(): JsonResponse
    {
        // Get post data
        $email = $this->request()->getPost('email');
        $password = $this->request()->getPost('password');

        // Authenticate and authorize user
        try {
            if ($this->service('auth')->login($email, $password)) {
                return new JsonResponse([
                    'status' => true,
                    'message' => translate('Successfully logged in'),
                ]);
            }
        } catch (Exception $ex) {
            return new JsonResponse([
                'status' => false,
                'message' => $ex->getMessage(),
            ]);
        }

        return $this->redirectToRoute('backend_auth_login');
    }
}
