<?php

declare(strict_types=1);

namespace SpartanSound;

final class SpartanHttp
{
    public function __construct(private string $base, private string $cookieFile)
    {
    }

    /**
     * @param array<string, string>|null $post
     * @return array{status: int, headers: string, body: string}
     */
    public function request(string $method, string $path, ?array $post = null, ?string $cookie = null): array
    {
        $handle = curl_init($this->base . $path);
        if ($handle === false) {
            throw new RuntimeException('Could not start a request.');
        }
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_COOKIEFILE => $this->cookieFile,
            CURLOPT_COOKIEJAR => $this->cookieFile,
        ];
        if ($post !== null) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($post);
        }
        if ($cookie !== null) {
            $options[CURLOPT_COOKIE] = $cookie;
        }
        curl_setopt_array($handle, $options);
        $raw = curl_exec($handle);
        if (!is_string($raw)) {
            $error = curl_error($handle);
            throw new RuntimeException('HTTP request failed: ' . $error);
        }
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        return [
            'status' => $status,
            'headers' => substr($raw, 0, $headerSize),
            'body' => substr($raw, $headerSize),
        ];
    }
}
