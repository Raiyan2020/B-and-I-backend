<?php

namespace App\Traits;

use App\Enums\DeviceType;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait SendsFirebaseNotifications
{
     // Firebase Admin Dashboard Setup
    protected function sendWebNotifications(array $tokens, array $notification, array $data = []): array
    {
        return $this->sendFirebaseNotifications(DeviceType::Web, $tokens, $notification, $data);
    }

    protected function sendAndroidNotifications(array $tokens, array $notification, array $data = []): array
    {
        return $this->sendFirebaseNotifications(DeviceType::Android, $tokens, $notification, $data);
    }

    protected function sendIosNotifications(array $tokens, array $notification, array $data = []): array
    {
        return $this->sendFirebaseNotifications(DeviceType::Ios, $tokens, $notification, $data);
    }

    protected function sendFirebaseNotifications(DeviceType $deviceType, array $tokens, array $notification, array $data = []): array
    {
        $tokens = array_values(array_filter(array_unique($tokens)));

        if ($tokens === []) {
            return [
                'sent' => 0,
                'failed' => 0,
                'responses' => [],
            ];
        }

        if ($this->hasLegacyServerKey()) {
            return $this->sendLegacyFirebaseNotifications($deviceType, $tokens, $notification, $data);
        }

        return $this->sendHttpV1FirebaseNotifications($deviceType, $tokens, $notification, $data);
    }

    private function sendLegacyFirebaseNotifications(DeviceType $deviceType, array $tokens, array $notification, array $data): array
    {
        $response = $this->firebaseHttpClient()->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key='.config('services.firebase.legacy_server_key'),
                'Content-Type' => 'application/json',
            ],
            'json' => $this->buildLegacyPayload($deviceType, $tokens, $notification, $data),
        ]);

        $decoded = json_decode((string) $response->getBody(), true) ?? [];
        $failureCount = (int) ($decoded['failure'] ?? 0);

        return [
            'sent' => count($tokens) - $failureCount,
            'failed' => $failureCount,
            'responses' => $decoded['results'] ?? [],
        ];
    }

    private function sendHttpV1FirebaseNotifications(DeviceType $deviceType, array $tokens, array $notification, array $data): array
    {
        $projectId = config('services.firebase.project_id');

        if (blank($projectId)) {
            throw new \RuntimeException('Missing Firebase project id.');
        }

        $responses = [];
        $sent = 0;
        $failed = 0;
        $accessToken = $this->firebaseAccessToken();

        foreach ($tokens as $token) {
            try {
                $response = $this->firebaseHttpClient()->post(
                    sprintf('https://fcm.googleapis.com/v1/projects/%s/messages:send', $projectId),
                    [
                        'headers' => [
                            'Authorization' => 'Bearer '.$accessToken,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'message' => $this->buildHttpV1Message($deviceType, $token, $notification, $data),
                        ],
                    ]
                );

                $responses[] = json_decode((string) $response->getBody(), true) ?? [];
                $sent++;
            } catch (\Throwable $exception) {
                report($exception);
                $responses[] = ['error' => $exception->getMessage(), 'token' => $token];
                $failed++;
            }
        }

        return compact('sent', 'failed', 'responses');
    }

    private function buildLegacyPayload(DeviceType $deviceType, array $tokens, array $notification, array $data): array
    {
        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $notification['title'],
                'body' => $notification['body'],
            ],
            'data' => $this->normalizeDataPayload($data),
            'priority' => 'high',
            'content_available' => true,
        ];

        if ($deviceType === DeviceType::Web && filled($notification['click_action'] ?? null)) {
            $payload['notification']['click_action'] = $notification['click_action'];
        }

        return $payload;
    }

    private function buildHttpV1Message(DeviceType $deviceType, string $token, array $notification, array $data): array
    {
        $message = [
            'token' => $token,
            'notification' => [
                'title' => $notification['title'],
                'body' => $notification['body'],
            ],
            'data' => $this->normalizeDataPayload($data),
        ];

        if ($deviceType === DeviceType::Web) {
            $webpushNotification = array_filter([
                'title' => $notification['title'],
                'body'  => $notification['body'],
                'icon'  => $notification['icon'] ?? null,
            ]);

            $webpush = ['notification' => $webpushNotification];

            // Only include fcm_options when there is actually a link to send
            if (filled($notification['click_action'] ?? null)) {
                $webpush['fcm_options'] = ['link' => $notification['click_action']];
            }

            $message['webpush'] = $webpush;
        }

        if ($deviceType === DeviceType::Android) {
            $message['android'] = [
                'priority' => 'high',
                'notification' => array_filter([
                    'clickAction' => $notification['click_action'] ?? null,
                    'sound' => 'default',
                ]),
            ];
        }

        if ($deviceType === DeviceType::Ios) {
            $message['apns'] = [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                    ],
                ],
            ];
        }

        return $message;
    }

    private function normalizeDataPayload(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalized[(string) $key] = is_scalar($value) || $value === null
                ? (string) $value
                : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $normalized;
    }

    private function firebaseHttpClient(): Client
    {
        return new Client([
            'timeout' => (float) config('services.firebase.timeout', 10),
        ]);
    }

    private function hasLegacyServerKey(): bool
    {
        return filled(config('services.firebase.legacy_server_key'));
    }

    private function firebaseAccessToken(): string
    {
        $credentials = $this->firebaseCredentials();
        $cacheKey = 'firebase_access_token:'.md5(($credentials['client_email'] ?? '').'|'.($credentials['project_id'] ?? ''));

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($credentials) {
            $jwt = $this->buildServiceAccountJwt($credentials);
            $response = $this->firebaseHttpClient()->post($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            $decoded = json_decode((string) $response->getBody(), true);

            if (! isset($decoded['access_token'])) {
                throw new \RuntimeException('Unable to fetch Firebase access token.');
            }

            return $decoded['access_token'];
        });
    }

    private function buildServiceAccountJwt(array $credentials): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = now()->timestamp;
        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'sub' => $credentials['client_email'],
            'aud' => $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $unsignedToken = $header.'.'.$claims;
        $signature = '';

        openssl_sign($unsignedToken, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);

        return $unsignedToken.'.'.$this->base64UrlEncode($signature);
    }

    private function firebaseCredentials(): array
    {
        $json = config('services.firebase.credentials_json');
        if (filled($json)) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $path = config('services.firebase.credentials_path');
        if (blank($path)) {
            throw new \RuntimeException('Firebase credentials path is not configured.');
        }

        $resolvedPath = Str::startsWith($path, ['/', '\\']) || preg_match('/^[A-Za-z]:\\\\/', $path)
            ? $path
            : base_path($path);

        if (! is_file($resolvedPath)) {
            throw new \RuntimeException('Firebase credentials file was not found.');
        }

        $decoded = json_decode((string) file_get_contents($resolvedPath), true);

        if (! is_array($decoded)) {
            throw new \RuntimeException('Invalid Firebase credentials json.');
        }

        return $decoded;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
