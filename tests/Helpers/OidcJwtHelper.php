<?php

namespace Tests\Helpers;

use phpseclib3\Crypt\RSA;

/**
 * A collection of functions to help with OIDC JWT testing.
 * By default, unless overridden, content is provided in a correct working state.
 */
class OidcJwtHelper
{
    public static function defaultIssuer(): string
    {
        return 'https://auth.example.com';
    }

    public static function defaultClientId(): string
    {
        return 'xxyyzz.aaa.bbccdd.123';
    }

    public static function defaultPayload(): array
    {
        return [
            'sub'                => 'abc1234def',
            'name'               => 'Barry Scott',
            'email'              => 'bscott@example.com',
            'ver'                => 1,
            'iss'                => static::defaultIssuer(),
            'aud'                => static::defaultClientId(),
            'iat'                => time(),
            'exp'                => time() + 720,
            'jti'                => 'ID.AaaBBBbbCCCcccDDddddddEEEeeeeee',
            'amr'                => ['pwd'],
            'idp'                => 'fghfghgfh546456dfgdfg',
            'preferred_username' => 'xXBazzaXx',
            'auth_time'          => time(),
            'at_hash'            => 'sT4jbsdSGy9w12pq3iNYDA',
        ];
    }

    public static function idToken($payloadOverrides = [], $headerOverrides = []): string
    {
        $payload = array_merge(static::defaultPayload(), $payloadOverrides);
        $header = array_merge([
            'kid' => 'xyz456',
            'alg' => 'RS256',
        ], $headerOverrides);

        $top = implode('.', [
            static::base64UrlEncode(json_encode($header)),
            static::base64UrlEncode(json_encode($payload)),
        ]);

        $privateKey = static::privateKeyInstance();
        $signature = $privateKey->sign($top);

        return $top . '.' . static::base64UrlEncode($signature);
    }

    public static function privateKeyInstance()
    {
        static $key;
        if (is_null($key)) {
            $key = RSA::loadPrivateKey(static::privatePemKey())->withPadding(RSA::SIGNATURE_PKCS1);
        }

        return $key;
    }

    public static function base64UrlEncode(string $decoded): string
    {
        return strtr(base64_encode($decoded), '+/', '-_');
    }

    public static function publicPemKey(): string
    {
        return ;
    }

    public static function privatePemKey(): string
    {
        return ;
    }

    public static function publicJwkKeyArray(): array
    {
        return [
            'kty' => 'RSA',
            'alg' => 'RS256',
            'kid' => '066e52af-8884-4926-801d-032a276f9f2a',
            'use' => 'sig',
            'e'   => 'AQAB',
            'n'   => 'qo1OmfNKec5S2zQC4SP9DrHuUR0VgCi6oqcGERz7zqO36hqk3A3R3aCgJkEjfnbnMuszRRKs45NbXoOp9pvmzXL16c93Obn7G8x8A3ao6yN5qKO5S5-CETqOZfKN_g75Xlz7VsC3igOhgsXnPx6iiM6sbYbk0U_XpFaT84LXKI8VTIPUo7gTeZN1pTET__i9FlzAOzX-xfWBKdOqlEzl-zihMHCZUUvQu99P-o0MDR0lMUT-vPJ6SJeRfnoHexwt6bZFiNnsZIEL03bX4QNkWvsLta1-jNUee-8IPVhzCO8bvM86NzLaKUJ4k6NZ5IVrmdCFpFsjCWByOrDG8wdw3w',
        ];
    }
}
