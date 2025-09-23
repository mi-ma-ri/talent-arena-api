<?php

namespace App\Services;

use Carbon\Carbon;
use stdClass;

class CinpherService extends BaseService
{
    /**
     * Create a new class instance.
     */
    public const OPEN_SSL_METHOD = 'AES-256-CBC';

    /**
     * Create a new class instance.
     */
    public function getDecryptParam(string $resourse): stdClass
    {
        $result = new stdClass;

        $result->unique_salt = uniqid(rand());
        $result->ms_v = base64_encode(
            openssl_random_pseudo_bytes(
                openssl_cipher_iv_length(self::OPEN_SSL_METHOD)
            )
        );
        $result->ms = $this->encypt(
            $resourse,
            $result->unique_salt,
            $result->ms_v
        );
        $result->ms_hash = $this->hash($resourse);

        return $result;
    }

    /**
     * 暗号
     * 方法：第一引数の文字列を第二引数で暗号化したものを、さらに共通ソルトで暗号化したものを返します。
     * 失敗した場合はfalseを返します。
     */
    public function encypt($string, $unique_salt, $iv)
    {
        $encrypt_by_unique_salt = @openssl_encrypt(
            $string,
            self::OPEN_SSL_METHOD,
            $unique_salt,
            0,
            $iv
        );
        return @openssl_encrypt(
            $encrypt_by_unique_salt,
            self::OPEN_SSL_METHOD,
            config('app.common_salt'),
            0,
            $iv
        );
    }

    /**
     * 復号
     * 方法：第一引数の文字列を第二引数で復号したものを、さらに共通ソルトで復号したものを返します。
     * 失敗した場合はfalseを返します。
     */
    public function decrypt(string $encrepted, string $salt, string $iv): string
    {
        $decrypt_by_common_salt = @openssl_decrypt(
            $encrepted,
            self::OPEN_SSL_METHOD,
            config('app.common_salt'),
            0,
            $iv
        );
        return @openssl_decrypt(
            $decrypt_by_common_salt,
            self::OPEN_SSL_METHOD,
            $salt,
            0,
            $iv
        );
    }

    /**
     * パラメータの文字列+共通ソルトを素材にsha256でハッシュ化します
     */
    public function hash(string $str): string
    {
        return hash("SHA256", $str . config('app.common_salt'));
    }

    /**
     * 現在時刻+ランダム数値+共通ソルトを素材にmd5でHMAC方式ハッシュ化
     */
    public function hash_hmac(): string
    {
        $now = (new Carbon())->format('Y-m-d H:i:s');
        return hash_hmac(
            'md5',
            $now . mt_rand(1, 999) . config('app.common_salt'),
            false,
            false
        );
    }
}
