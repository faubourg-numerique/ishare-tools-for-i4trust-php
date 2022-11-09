<?php

namespace FaubourgNumerique\IShareToolsForI4Trust;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ramsey\Uuid\Uuid;

class IShareToolsForI4Trust
{
    static function generateIShareJWT(array $config)
    {
        $time = time();
        $uuid = Uuid::uuid4();

        $header = [
            "alg" => "RS256",
            "typ" => "JWT",
            "x5c" => $config["x5c"]
        ];

        $payload = [
            "iss" => $config["issuer"],
            "sub" => $config["subject"],
            "aud" => $config["audience"],
            "iat" => $time,
            "exp" => $time + 30,
            "jti" => $uuid->toString()
        ];

        return JWT::encode($payload, $config["privateKey"], "RS256", null, $header);
    }

    static function getAccessToken(array $config)
    {
        $headers = [
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $data = [
            "grant_type" => "client_credentials",
            "scope" => "iSHARE",
            "client_id" => $config["clientId"],
            "client_assertion_type" => "urn:ietf:params:oauth:client-assertion-type:jwt-bearer",
            "client_assertion" => $config["iShareJWT"]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $config["arTokenURL"]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);

        return $response["access_token"];
    }

    static function getDelegationToken(array $config)
    {
        $headers = [
            "Authorization: Bearer {$config["accessToken"]}",
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $config["arDelegationURL"]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($config["delegationRequest"]));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);

        return $response["delegation_token"];
    }

    static function createPolicy(array $config)
    {
        $headers = [
            "Authorization: Bearer {$config["accessToken"]}",
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $config["arPolicyURL"]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($config["delegationEvidence"]));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
    }

    static function decodeJWT(string $encodedJWT, string $certificate)
    {
        return JWT::decode($encodedJWT, new Key($certificate, "RS256"));
    }
}
