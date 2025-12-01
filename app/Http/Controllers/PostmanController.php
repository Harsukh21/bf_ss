<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostmanController extends Controller
{
    public function index()
    {
        return view('postman.index');
    }

    public function run(Request $request)
    {
        $data = $request->validate([
            'url' => 'required|url',
            'method' => 'required|string|in:GET,POST,PUT,PATCH,DELETE',
            'payload' => 'nullable|string',
            'headers' => 'nullable|string',
        ]);

        $headers = $this->parseHeaders($data['headers'] ?? '');
        [$payload, $payloadIsJson] = $this->parsePayload($data['payload'] ?? '');

        $method = strtoupper($data['method']);
        $start = microtime(true);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->send($method, $data['url'], $this->buildRequestOptions($method, $payload, $payloadIsJson, $headers));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => null,
                'error' => $e->getMessage(),
            ], 500);
        }

        $durationMs = round((microtime(true) - $start) * 1000, 2);
        $body = $response->body();
        $prettyBody = $this->prettyPrintBody($body);

        return response()->json([
            'status' => $response->status(),
            'duration_ms' => $durationMs,
            'headers' => $response->headers(),
            'body' => $body,
            'body_pretty' => $prettyBody,
        ]);
    }

    private function parseHeaders(?string $headersInput): array
    {
        if (empty(trim((string) $headersInput))) {
            return [];
        }

        $headers = [];
        $decoded = json_decode($headersInput, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            foreach ($decoded as $key => $value) {
                if (is_scalar($value)) {
                    $headers[$key] = (string) $value;
                }
            }
            return $headers;
        }

        $lines = preg_split('/\r\n|\r|\n/', $headersInput);
        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $key = trim($key);

            if ($key === '') {
                continue;
            }

            $headers[$key] = trim($value);
        }

        return $headers;
    }

    private function parsePayload(?string $payloadInput): array
    {
        if ($payloadInput === null || trim($payloadInput) === '') {
            return [null, false];
        }

        $decoded = json_decode($payloadInput, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return [$decoded, true];
        }

        return [$payloadInput, false];
    }

    private function buildRequestOptions(string $method, $payload, bool $payloadIsJson, array $headers): array
    {
        $options = [];

        if ($payload === null) {
            return $options;
        }

        if ($method === 'GET') {
            if (is_array($payload)) {
                $options['query'] = $payload;
            } else {
                parse_str((string) $payload, $query);
                if (!empty($query)) {
                    $options['query'] = $query;
                }
            }

            return $options;
        }

        if ($payloadIsJson || is_array($payload)) {
            $options['json'] = $payload;
            return $options;
        }

        $options['body'] = (string) $payload;
        $hasContentType = false;

        foreach ($headers as $key => $value) {
            if (strcasecmp($key, 'Content-Type') === 0) {
                $hasContentType = true;
                break;
            }
        }

        if (! $hasContentType) {
            $options['headers']['Content-Type'] = 'text/plain';
        }

        return $options;
    }

    private function prettyPrintBody(string $body): string
    {
        $decoded = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return $body;
    }
}
