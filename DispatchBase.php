<?php

	namespace Stoic\Chain;

	abstract class DispatchBase {
		protected $_isConsumable = false;
		protected $_isStateful = false;
		protected $_isConsumed = false;
		private $_results = array();
		protected $_isValid = false;
		private $_calledDateTime;


		public function __toString() {
			return static::class . "{ \"calledDateTime\": \"" . $this->_calledDateTime->format("Y-m-d G:i:s") . "\", " .
				"\"isConsumable\": \"{$this->_isConsumable}\", " .
				"\"isStateful\": \"{$this->_isStateful}\", " .
				"\"isConsumed\": \"{$this->_isConsumed}\" }";
		}
		
		public function consume() {
			if ($this->_isConsumable && !$this->_isConsumed) {
				$this->_isConsumed = true;

				return true;
			}

			return false;
		}

		public function getCalledDateTime() {
			return $this->_calledDateTime;
		}

		public function getResults() {
			if (count($this->_results) < 1) {
				return null;
			}

			return $this->_results;
		}

		abstract public function initialize($input);

		public function isConsumable() {
			return $this->_isConsumable;
		}

		public function isConsumed() {
			return $this->_isConsumed;
		}

		public function isStateful() {
			return $this->_isStateful;
		}

		public function isValid() {
			return $this->_isValid;
		}

		public function makeConsumable() {
			$this->_isConsumable = true;

			return $this;
		}

		public function makeStateful() {
			$this->_isStateful = true;

			return $this;
		}

		protected function makeValid() {
			$this->_calledDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
			$this->_isValid = true;

			return $this;
		}

		public function numResults() {
			return count($this->_results);
		}

		public function setResult($result) {
			if (!$this->_isStateful) {
				$this->_results = array($result);
			} else {
				$this->_results[] = $result;
			}

			return $this;
		}
	}
