<?php
/**
 * Value object that tracks the result of a single import run.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\Import;

class ImportJob {

	private bool   $success       = false;
	private bool   $completed     = false;
	private string $error_message = '';
	private int    $count_success = 0;
	private int    $count_error   = 0;
	private float  $started_at;

	public function __construct() {
		$this->started_at = microtime( true );
	}

	public function complete(): void {
		$this->success   = true;
		$this->completed = true;
	}

	public function fail( string $message ): void {
		$this->success       = false;
		$this->completed     = true;
		$this->error_message = $message;
	}

	public function increment_success(): void {
		++$this->count_success;
	}

	public function increment_error(): void {
		++$this->count_error;
	}

	public function is_success(): bool {
		return $this->success;
	}

	public function is_completed(): bool {
		return $this->completed;
	}

	public function get_error_message(): string {
		return $this->error_message;
	}

	public function get_count_success(): int {
		return $this->count_success;
	}

	public function get_count_error(): int {
		return $this->count_error;
	}

	public function get_duration_ms(): int {
		return (int) round( ( microtime( true ) - $this->started_at ) * 1000 );
	}

	/** @return array<string, mixed> */
	public function to_array(): array {
		return [
			'success'       => $this->success,
			'count_success' => $this->count_success,
			'count_error'   => $this->count_error,
			'duration_ms'   => $this->get_duration_ms(),
			'message'       => $this->error_message,
		];
	}
}
