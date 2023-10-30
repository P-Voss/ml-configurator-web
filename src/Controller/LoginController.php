<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/{_locale<en|de>}/', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, UserRepository $userRepository, $sampleUserFile): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $availableSampleUsers = [];
        $sampleUsers = $userRepository->findBy(['isDemoUser' => true]);
        foreach ($sampleUsers as $sampleUser) {
            if (!$sampleUser->getLastActionDatetime()) {
                try {
                    $availableSampleUsers[$sampleUser->getEmail()] = $this->findPassword($sampleUser->getEmail(), json_decode(file_get_contents($sampleUserFile)));
                } catch (\Exception $exception) {
                    continue;
                }
                continue;
            }
            if ($sampleUser->hoursSinceLastActivity() > 2) {
                try {
                    $availableSampleUsers[$sampleUser->getEmail()] = $this->findPassword($sampleUser->getEmail(), json_decode(file_get_contents($sampleUserFile)));
                } catch (\Exception $exception) {
                    continue;
                }
            }
        }

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'sampleUsers' => $availableSampleUsers,
        ]);
    }

    /**
     * @param string $username
     * @param array $sampleUserCredentials
     * @return string
     * @throws \Exception
     */
    private function findPassword(string $username, array $sampleUserCredentials = []): string {
        foreach ($sampleUserCredentials as $credential) {
            if ($credential->name === $username) {
                return $credential->password;
            }
        }
        throw new \Exception('credentials do not exist');
    }

    #[Route('/{_locale<en|de>}/logout', name: 'app_logout')]
    public function logout(): never
    {
        throw new \Exception("error in security.yaml");
    }


}
