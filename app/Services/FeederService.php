<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class FeederService
{
    protected $url;
    protected $user;
    protected $pass;

    public function __construct()
    {
        $this->url = config('feeder.url');
        $this->user = config('feeder.user');
        $this->pass = config('feeder.pass');
    }

    /**
     * Get Token from Feeder or Cache
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = 'feeder_token_' . md5($this->user);

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addHours(2), function () {
            $response = Http::post($this->url, [
                'act' => 'GetToken',
                'username' => $this->user,
                'password' => $this->pass
            ]);

            $result = $response->json();

            if (isset($result['error_code']) && $result['error_code'] != 0) {
                throw new Exception("Feeder Auth Error: " . $result['error_desc']);
            }

            return $result['data']['token'] ?? null;
        });
    }

    /**
     * Call Feeder Web Service with Auto-Retry for Token Expiration
     */
    public function proxy($act, $filter = '', $offset = 0, $limit = 0, $order = '')
    {
        $token = $this->getToken();

        $response = Http::post($this->url, [
            'act' => $act,
            'token' => $token,
            'filter' => $filter,
            'offset' => $offset,
            'limit' => $limit,
            'order' => $order
        ]);

        $result = $response->json();

        // Check for token expiration error (Usually error_code 100 or similar for Session Expired)
        // Adjust these error codes based on Neo Feeder documentation
        $expiredCodes = [100, 101, 102];

        if (isset($result['error_code']) && in_array($result['error_code'], $expiredCodes)) {
            // Token expired, refresh and retry once
            $token = $this->getToken(true);

            $response = Http::post($this->url, [
                'act' => $act,
                'token' => $token,
                'filter' => $filter,
                'offset' => $offset,
                'limit' => $limit,
                'order' => $order
            ]);

            $result = $response->json();
        }

        return $result;
    }

    /**
     * Insert/Update data to Feeder
     */
    public function post($act, $record)
    {
        $token = $this->getToken();

        $response = Http::post($this->url, [
            'act' => $act,
            'token' => $token,
            'record' => $record
        ]);

        return $response->json();
    }

    /**
     * Delete data from Feeder
     */
    public function delete($act, $key)
    {
        $token = $this->getToken();

        $response = Http::post($this->url, [
            'act' => $act,
            'token' => $token,
            'key' => $key
        ]);

        return $response->json();
    }
}
