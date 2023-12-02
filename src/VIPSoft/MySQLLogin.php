<?php
/**
 * @copyright 2023 Anthon Pang
 * @license MIT
 */
namespace VIPSoft;

/**
 * Reader/decoder for obfuscated MySQL authentication credentials
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class MySQLLogin
{
    const LOGIN_KEY_LEN = 20;
    const MY_LOGIN_HEADER_LEN = 24;
    const MAX_CIPHER_STORE_LEN = 4;

    /**
     * @var array
     */
    private $credentials;

    /**
     * @var string
     */
    private $loginPathFile;

    /**
     * Constructor
     *
     * @param string $loginPathFile Obfuscated login path file (.mylogin.cnf)
     */
    public function __construct($loginPathFile)
    {
        $this->loginPathFile = $loginPathFile;
    }

    /**
     * Get decoded authentication credentials for login path
     *
     * @param string $loginPath
     *
     * @return array
     */
    public function get($loginPath = 'client')
    {
        if ($this->credentials === null) {
            $this->decodeLoginPathFile();
        }

        return is_array($this->credentials) && array_key_exists($loginPath, $this->credentials) ? $this->credentials[$loginPath] : [];
    }

    /**
     * Read and decode login path file
     */
    private function decodeLoginPathFile()
    {
        $raw = file_get_contents($this->loginPathFile);
        $fp = 4; // skip null bytes

        $b = substr($raw, $fp, self::LOGIN_KEY_LEN);
        $fp = self::MY_LOGIN_HEADER_LEN;

        // extract key
        $key = array_pad([], 16, 0);

        for ($i = 0; $i < self::LOGIN_KEY_LEN; $i++) {
            $key[$i % 16] ^= ord($b[$i]);
        }

        $key = pack('C*', $key[0], $key[1], $key[2], $key[3], $key[4], $key[5], $key[6], $key[7], $key[8], $key[9], $key[10], $key[11], $key[12], $key[13], $key[14], $key[15]);
        $loginPath = '';
        $credentials = [];

        while ($fp < strlen($raw)) {
            $b = substr($raw, $fp, self::MAX_CIPHER_STORE_LEN);
            $fp += self::MAX_CIPHER_STORE_LEN;
            $cipher_len = unpack('V', $b);

            $b = substr($raw, $fp, $cipher_len[1]);
            $fp += $cipher_len[1];
            $plain = trim(openssl_decrypt($b, 'aes-128-ecb', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING));

            if (preg_match('/^\[(.+?)\]/', $plain, $matches)) {
                $loginPath = $matches[1];
                $credentials[$loginPath] = [];
            } elseif (preg_match('/^(\w+) = "(.*?)"/', $plain, $matches)) {
                $credentials[$loginPath][$matches[1]] = $matches[2];
            }
        }

        $this->credentials = $credentials;
    }
}
