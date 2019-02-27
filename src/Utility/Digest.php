<?php declare(strict_types=1);

namespace DigestAuthServer\Utility;

use Slim\Http\Response;
use DigestAuthServer\Models\AuthenticationRequest;
use DigestAuthServer\Models\DigestUser;

class Digest
{

    private static $digest = null;
    private static $realm = "This is my realm deal with it";

    public static function getDigest() : ?string
    {
        if (self::$digest !== null) {
            return self::$digest;
        }
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            self::$digest = $_SERVER['PHP_AUTH_DIGEST'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'digest')===0) {
                self::$digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            }
        }
        return self::$digest;
    }

    //Unpack information from $digest that we then use to check the authentication
    private static function parse(): ?array
    {
        // protect against missing data
        $needed_parts = array('username'=>1, 'realm'=>1, 'nonce'=>1, 'opaque'=>1, 'uri'=>1, 'response'=>1);
        $data = array();

        preg_match_all('@(\w+)=(?:(?:")([^"]+)"|([^\s,$]+))@', self::$digest, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[2] ? $m[2] : $m[3];
            unset($needed_parts[$m[1]]);
        }

        return empty($needed_parts) ? $data : null;
    }

    public static function valid(): bool
    {
        $digestDetails = self::parse();
        if (!$digestDetails) {
            return false;
        }
        //Check Nonce Exists
        $authenticationrequest = AuthenticationRequest::where('nonce', $digestDetails['nonce'])->first();
        if (!$authenticationrequest) {
            return false;
        }
        //Mark Nonce as used
        $authenticationrequest->used = true;
        $authenticationrequest->save();
        //Check Opaque is the same
        if ($authenticationrequest->opaque !== $digestDetails['opaque']) {
            return false;
        }
        //Check User Exists
        $digestuser = DigestUser::where('name', $digestDetails['username'])->first();
        if (!$digestuser) {
            return false;
        }
        //Check Response Hash Matches
        $A1 = md5(sprintf(
            '%s:%s:%s',
            $digestuser->name,
            self::$realm,
            $digestuser->password
        ));

        $A2 = md5(sprintf(
            '%s:%s',
            $_SERVER['REQUEST_METHOD'],
            $digestDetails['uri']
        ));

        $calculated_response = md5(sprintf(
            '%s:%s:%s',
            $A1,
            $authenticationrequest->nonce,
            $A2
        ));

        if ($calculated_response !== $digestDetails['response']) {
            return false;
        }
        return true;
    }

    public static function setDigestDetails(Response $response): Response
    {
        $authenticationrequest = new AuthenticationRequest();

        $authenticationrequest->nonce = bin2hex(random_bytes(16));
        $authenticationrequest->opaque = bin2hex(random_bytes(16));

        $authenticationrequest->save();

        $authenticationHeader = sprintf(
            'Digest realm="%s", nonce="%s", opaque="%s"',
            self::$realm,
            $authenticationrequest->nonce,
            $authenticationrequest->opaque
        );
        //'HTTP/1.0 401 Unauthorized'
        $response = $response->withStatus(401)->withHeader('WWW-Authenticate', $authenticationHeader);

        return $response;
    }
}
