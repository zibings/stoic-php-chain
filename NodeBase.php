<?php

	namespace Stoic\Chain;

	abstract class NodeBase {
		protected $_key = null;
		protected $_version = null;


		public function getKey() {
			return $this->_key;
		}

		public function getVersion() {
			return $this->_version;
		}

		public function isValid() {
			return !empty($this->_key) && !empty($this->_version);
		}

		abstract public function process($sender, DispatchBase &$dispatch);

		public function setKey($key) {
			$this->_key = $key;

			return $this;
		}

		public function setVersion($version) {
			$this->_version = $version;

			return $this;
		}
	}
