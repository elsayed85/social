<?php

namespace App\Services;

use App\Exceptions\Api\Postman\InvalidClientException;

class ProxyRequest
{
    public function grantPasswordToken(string $email, string $password, $scope = '')
    {
        $params = [
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
        ];

        return $this->makePostRequest($params, $scope);
    }

    public function refreshAccessToken($scope = '')
    {
        $refreshToken = request()->cookie('refresh_token');

        abort_unless($refreshToken, 403, 'Your refresh token is expired.');

        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        return $this->makePostRequest($params, $scope);
    }

    protected function makePostRequest(array $params, $scope = '')
    {
        $params = array_merge([
            'client_id' => config('services.passport.password_client_id'),
            'client_secret' => config('services.passport.password_client_secret'),
            'scope' => $scope,
        ], $params);

        $proxy = \Request::create('oauth/token', 'post', $params);
        $resp = json_decode(app()->handle($proxy)->getContent());

        if (isset($resp->error)) {
            throw new InvalidClientException($resp);
        }

        $this->setHttpOnlyCookie($resp->refresh_token);

        return $resp;
    }

    protected function setHttpOnlyCookie(string $refreshToken)
    {
        cookie()->queue(
            'refresh_token',
            $refreshToken,
            14400, // 10 days
            null,
            null,
            false,
            true // httponly
        );
    }
}
