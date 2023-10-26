<?php

namespace Farzai\Twitch;

use Exception;
use Farzai\Twitch\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Authorizer
{
    /**
     * Retrieve an access token from Twitch.
     *
     * @throws AuthenticationException
     */
    public static function retrieveAccessToken(): string
    {
        $accessTokenCacheKey = 'twitch_cache.access_token';

        $accessToken = Cache::get($accessTokenCacheKey, '');
        if ($accessToken) {
            return $accessToken;
        }

        try {
            $response = Http::withQueryParameters([
                'client_id' => config('twitch.credentials.client_id'),
                'client_secret' => config('twitch.credentials.client_secret'),
                'grant_type' => 'client_credentials',
            ])
                ->post('https://id.twitch.tv/oauth2/token')
                ->throw();

            if ($response->json('access_token') && $response->json('expires_in')) {
                Cache::put($accessTokenCacheKey, (string) $response->json('access_token'), (int) $response->json('expires_in') - 60);

                $accessToken = $response->json('access_token');
            }
        } catch (Exception) {
            throw new AuthenticationException('Access Token could not be retrieved from Twitch.');
        }

        return (string) $accessToken;
    }
}
