<?php declare(strict_types=1);

namespace JWTServer\Utility;

class Token
{

    private static $secret = 'thisisthesecretwhichshouldbewellprotected';

    public static function base64URLEncode(string $data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64URLDecode(string $data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    // FIXME: Allow algorithm and hashing function to be amended.
    public static function create(array $payload, string $algorithm = 'HS256') : string
    {
        $header = ['typ' => 'JWT', 'alg' => $algorithm];
        $segments = [];
        $segments[] = self::base64URLEncode(json_encode($header));
        $segments[] = self::base64URLEncode(json_encode($payload));

        $signing_input = implode('.', $segments);

        $signature = hash_hmac('sha256', $signing_input, Token::$secret, true);

        $segments[] = self::base64URLEncode($signature);

        return implode('.', $segments);
    }

    public static function decode(string $token) : array
    {
        list($header, $payload, $signature) = explode(".", $token);

        $header = json_decode(self::base64URLDecode($header), true);
        $payload = json_decode(self::base64URLDecode($payload), true);

        return [
          'header' => $header,
          'payload' => $payload,
        ];
    }

    /**
     * Check two signature hashes match. One signature is supplied by the token - we need to decode this
     * The other is newly generated from the token's header and payload. They
     * should match - if they don't someone has likely tampered with the token.
     */
    public static function validate(?string $token) : bool
    {
        if (!$token) {
            return false;
        }

        list($header, $payload, $signature) = explode(".", $token);

        $recreated_signature = hash_hmac('sha256', "$header.$payload", Token::$secret, true);

        return hash_equals(self::base64URLDecode($signature), $recreated_signature);
    }
}
