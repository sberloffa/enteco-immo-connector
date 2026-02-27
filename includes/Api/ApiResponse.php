<?php
/**
 * Uniform response wrapper for all provider API calls.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Api;

class ApiResponse {

	private bool $success;
	/** @var array<int, array<string, mixed>> */
	private array $data;
	private string $error_message;
	private int $status_code;

	/**
	 * @param array<int, array<string, mixed>> $data
	 */
	public function __construct(
		bool $success,
		array $data = [],
		string $error_message = '',
		int $status_code = 200
	) {
		$this->success       = $success;
		$this->data          = $data;
		$this->error_message = $error_message;
		$this->status_code   = $status_code;
	}

	/** @param array<int, array<string, mixed>> $data */
	public static function success( array $data ): self {
		return new self( true, $data );
	}

	public static function error( string $message, int $status_code = 0 ): self {
		return new self( false, [], $message, $status_code );
	}

	public function is_success(): bool {
		return $this->success;
	}

	/** @return array<int, array<string, mixed>> */
	public function get_data(): array {
		return $this->data;
	}

	public function get_error_message(): string {
		return $this->error_message;
	}

	public function get_status_code(): int {
		return $this->status_code;
	}
}
