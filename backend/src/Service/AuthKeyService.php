<?php

namespace App\Service;
use App\Entity\User;
//use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\InvalidTokenException;
use Exception as GlobalException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;


class AuthKeyService
{
    /**
     * Encodes a JWK token.
     * @param User $user The user to encode in the JWK token.
     * @return string The encoded JWK token.
     */
    private $params;
    private $jwtManager;
    private $doctrine;

    public function __construct(JWTTokenManagerInterface $jwtManager, ParameterBagInterface $params, ManagerRegistry $doctrine){
        $this->params = $params;
        $this->jwtManager = $jwtManager;
        $this->doctrine = $doctrine;
    }
    public  function encodeJWT(User $user): string
    {
        $token = $this->jwtManager->create($user);
        return $token;
    }

    /**
     * Decodes a JWK token.
     *
     * @param string $jwk The JWK token to decode.
     * @return array The decoded JWK token.
     * @throws Exception
     */
    public function decodeJWT(string $jwt): ?User
    {
        try {
            $token = new JWTUserToken();
            $token->setRawToken($jwt);
            $decoded = $this->jwtManager->decode($token);
            // check if the token is expired
            if ($decoded['exp'] < time()) {
                throw new BadRequestHttpException("Token expired");
            }
            // fetch user from database
            $user = $this->doctrine->getRepository(User::class)->findByUsernameASC($decoded['username'])[0];
            return $user;
        } catch (GlobalException $e) {
            throw new BadRequestHttpException("Invalid token");


        }

        return null;
    }



   
}