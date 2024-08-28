<?php
    namespace App\Services;
    use Exception;

    class CommonCrypt
    {
        // constant
        const ALGORITHM_AES_128_CBC = 'AES-128-CBC';
        const ALGORITHM_AES_256_CBC = 'AES-256-CBC';

        /**
         * The Supported Cipher Algorithms.
         *
         * @var array
         */
        private array $supportedCipherAlgorithmList = [
            self::ALGORITHM_AES_128_CBC,
            self::ALGORITHM_AES_256_CBC
        ];

        /**
         * The encryption key.
         */
        protected string $key;

        /**
         * The algorithm used for encryption.
         */
        protected string $cipher;


        /**
         * Create a new encrypter instance.
         *
         * @param  string  $key
         * @param  string  $cipher
         * @return void
         *
         * @throws Exception
         */
        public function __construct(string $key, string $cipher = self::ALGORITHM_AES_256_CBC)
        {
            // Verify the key and cipher are set correctly
            $this->supported($key, $cipher);

            // setter
            $this->key = $key;
            $this->cipher = $cipher;
        }

        /**
         * set key
         *
         * @param  string  $key
         * @return self
         */
        public function setKey(string $key): self
        {
            $this->key = $key;
            return $this;
        }

        /**
         * set cipher
         *
         * @param  string  $cipher
         * @return self
         */
        public function setCipher(string $cipher): self
        {
            $this->cipher = $cipher;
            return $this;
        }

        /**
         * Encrypt the given value.
         *
         * @param mixed $value
         * @param bool  $serialize
         *
         * @return string|null
         *
         * @throws \Exception
         */
        public function encrypt(mixed $value, bool $serialize = true): ?string
        {
            // case value is null
            if (is_null($value)) {
                return null;
            }

            // generate of thr iv(Initial Vector)
            $iv = random_bytes(openssl_cipher_iv_length($this->cipher));

            // First we will encrypt the value using OpenSSL.
            // After this is encrypted we will proceed to calculating a MAC for the encrypted value so that this
            $value = \openssl_encrypt(
                $serialize ? serialize($value) : $value,
                $this->cipher,
                $this->key,
                0,
                $iv
            );

            if ($value === false) {
                throw new Exception('Could not encrypt the data.');
            }

            // Once we get the encrypted value we'll go ahead and base64_encode the input
            // vector and create the MAC for the encrypted value so we can then verify
            // its authenticity. Then, we'll JSON the data into the "payload" array.
            $mac = $this->hash($iv = base64_encode($iv), $value);

            $json = json_encode(compact('iv', 'value', 'mac'), JSON_UNESCAPED_SLASHES);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Could not encrypt the data.');
            }

            return base64_encode($json);
        }

        /**
         * Encrypt a string without serialization.
         *
         * @param  string  $value
         * @return string
         *
         * @throws Exception
         */
        public function encryptString(string $value): string
        {
            return $this->encrypt($value, false);
        }

        /**
         * Decrypt the given value.
         *
         * @param  mixed  $payload
         * @param  bool  $unserialize
         * @return mixed
         *
         * @throws Exception
         */
        public function decrypt($payload, bool $unserialize = true)
        {
            // case payload is null
            if (is_null($payload)) {
                return null;
            }

            // case payload is non string type
            if (gettype($payload) !== 'string') {
                throw new Exception('Could not decrypt the data. decrypt payload is string only.');
            }

            $payload = $this->getJsonPayload($payload);

            $iv = base64_decode($payload['iv']);

            // Here we will decrypt the value. If we are able to successfully decrypt it
            // we will then unserialize it and return it out to the caller. If we are
            // unable to decrypt this value we will throw out an exception message.
            $decrypted = \openssl_decrypt(
                $payload['value'],
                $this->cipher,
                $this->key,
                0,
                $iv
            );

            if ($decrypted === false) {
                throw new Exception('Could not decrypt the data.');
            }

            return $unserialize ? unserialize($decrypted) : $decrypted;
        }

        /**
         * Decrypt the given string without unserialization.
         *
         * @param  string  $payload
         * @return string
         *
         * @throws Exception
         */
        public function decryptString(string $payload): string
        {
            return $this->decrypt($payload, false);
        }

        /**
         * Create a MAC for the given value.
         * If you want to run this function by itself, set iv to.
         * ex)$iv = random_bytes(openssl_cipher_iv_length("AES-256-CBC"));
         *
         * @param  string  $iv
         * @param  mixed  $value
         * @return string
         */
        public function hash(string $iv, $value): string
        {
            if (empty($iv)) {
                // generate of thr iv(Initial Vector)
                $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
            }
            return hash_hmac('sha256', $iv . $value, $this->key);
        }

        /**
         * Determine if the given key and cipher are valid.
         *
         * @param  string  $key
         * @param  string  $cipher
         * @return void
         *
         * @throws Exception
         */
        protected function supported(string $key, string $cipher): void
        {
            // Comfirm that set cipher algorithm is supported.
            if (!in_array($cipher, $this->supportedCipherAlgorithmList)) {
                $message = 'The following cipher algorithms are supported : ' . implode(', ', $this->supportedCipherAlgorithmList);
                throw new Exception($message);
            }

            // Comfirm The key is not empty.
            if (empty($key)) {
                throw new Exception('The key is empty.');
            }
        }

        /**
         * Get the JSON array from the given payload.
         *
         * @param  string  $payload
         * @return array
         *
         * @throws Exception
         */
        protected function getJsonPayload(string $payload): array
        {
            $payload = json_decode(base64_decode($payload), true);

            // If the payload is not valid JSON or does not have the proper keys set we will
            // assume it is invalid and bail out of the routine since we will not be able
            // to decrypt the given value. We'll also check the MAC for this encryption.
            if (!$this->validPayload($payload)) {
                throw new Exception('The payload is invalid.');
            }

            if (!$this->validMac($payload)) {
                throw new Exception('The MAC is invalid.');
            }

            return $payload;
        }

        /**
         * Verify that the encryption payload is valid.
         *
         * @param  mixed  $payload
         * @return bool
         */
        protected function validPayload($payload): bool
        {
            return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
                strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
        }

        /**
         * Determine if the MAC for the given payload is valid.
         *
         * @param  array  $payload
         * @return bool
         */
        protected function validMac(array $payload): bool
        {
            $calculated = $this->calculateMac($payload, $bytes = random_bytes(16));

            return hash_equals(
                hash_hmac(
                    'sha256',
                    $payload['mac'],
                    $bytes,
                    true
                ),
                $calculated
            );
        }

        /**
         * Calculate the hash of the given payload.
         *
         * @param  array  $payload
         * @param  string  $bytes
         * @return string
         */
        protected function calculateMac(array $payload, string $bytes): string
        {
            return hash_hmac(
                'sha256',
                $this->hash(
                    $payload['iv'],
                    $payload['value']
                ),
                $bytes,
                true
            );
        }
    }
