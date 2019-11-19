<?php

namespace Frontender\Platform\Template\Filter;

use Slim\Container;

class Encryption extends \Twig_Extension {
	private $cipher;
	private $key;

	public function __construct(Container $container) {
		$this->key = $container->config->encryption_key;
		$this->cipher = 'AES-128-CBC';
	}

	public function getFilters()
	{
		return [
			new \Twig_Filter('encrypt', [$this, 'encrypt']),
			new \Twig_Filter('decrypt', [$this, 'decrypt'])
		];
	}

	public function encrypt($value, $serialize = true) {
		$iv = random_bytes(openssl_cipher_iv_length($this->cipher));

		// First we will encrypt the value using OpenSSL. After this is encrypted we
		// will proceed to calculating a MAC for the encrypted value so that this
		// value can be verified later as not having been changed by the users.
		$value = \openssl_encrypt(
			$serialize ? serialize($value) : $value,
			$this->cipher, $this->key, 0, $iv
		);

		if ($value === false) {
			throw new \Exception('Could not encrypt the data.');
		}

		// Once we get the encrypted value we'll go ahead and base64_encode the input
		// vector and create the MAC for the encrypted value so we can then verify
		// its authenticity. Then, we'll JSON the data into the "payload" array.
		$mac = $this->hash($iv = base64_encode($iv), $value);

		$json = json_encode(compact('iv', 'value', 'mac'));

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception('Could not encrypt the data.');
		}

		return base64_encode($json);
	}

	public function decrypt($payload, $unserialize = true) {
		$payload = $this->getJsonPayload($payload);

		$iv = base64_decode($payload['iv']);

		// Here we will decrypt the value. If we are able to successfully decrypt it
		// we will then unserialize it and return it out to the caller. If we are
		// unable to decrypt this value we will throw out an exception message.
		$decrypted = \openssl_decrypt(
			$payload['value'], $this->cipher, $this->key, 0, $iv
		);

		if ($decrypted === false) {
			throw new \Exception('Could not decrypt the data.');
		}

		return $unserialize ? unserialize($decrypted) : $decrypted;
	}

	protected function hash($iv, $value)
	{
		return hash_hmac('sha256', $iv.$value, $this->key);
	}

	protected function getJsonPayload($payload)
	{
		$payload = json_decode(base64_decode($payload), true);

		// If the payload is not valid JSON or does not have the proper keys set we will
		// assume it is invalid and bail out of the routine since we will not be able
		// to decrypt the given value. We'll also check the MAC for this encryption.
		if (! $this->validPayload($payload)) {
			throw new \Exception('The payload is invalid.');
		}

		if (! $this->validMac($payload)) {
			throw new \Exception('The MAC is invalid.');
		}

		return $payload;
	}

	protected function validPayload($payload)
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
	protected function validMac(array $payload)
	{
		$calculated = $this->calculateMac($payload, $bytes = random_bytes(16));

		return hash_equals(
			hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated
		);
	}

	/**
	 * Calculate the hash of the given payload.
	 *
	 * @param  array  $payload
	 * @param  string  $bytes
	 * @return string
	 */
	protected function calculateMac($payload, $bytes)
	{
		return hash_hmac(
			'sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true
		);
	}
}