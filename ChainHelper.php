<?php

	namespace Stoic\Chain;

	class ChainHelper {
		protected $_nodes = array();
		protected $_isEvent = false;
		protected $_doDebug = false;
		protected $_logger = null;


		public function __construct($isEvent = false, $doDebug = false) {
			$this->_isEvent = $isEvent;

			return;
		}

		public function toggleDebug($doDebug) {
			$this->_doDebug = ($doDebug) ? true : false;

			return $this;
		}

		public function getNodeList() {
			$ret = array();

			foreach (array_values($this->_nodes) as $node) {
				$ret[] = array(
					'key' => $node->getKey(),
					'version' => $node->getVersion()
				);
			}

			return $ret;
		}

		public function hookLogger($callback) {
			if ($callback === null || !is_callable($callback)) {
				return false;
			}

			$this->_logger = $callback;

			return true;
		}

		public function isEvent() {
			return $this->_isEvent;
		}

		public function linkNode(NodeBase $node) {
			if (!$node->isValid()) {
				if ($this->_doDebug) {
					$this->log("Attempted to add invalid node: " . $node);
				}

				return $this;
			}

			if ($this->_isEvent) {
				if ($this->_doDebug) {
					$this->log("Setting event node: " . $node);
				}

				$this->_nodes = array($node);
			} else {
				if ($this->_doDebug) {
					$this->log("Linking new node: " . $node);
				}

				$this->_nodes[] = $node;
			}

			return $this;
		}

		public function traverse(DispatchBase &$dispatch, $sender = null) {
			if (count($this->_nodes) < 1) {
				if ($this->_doDebug) {
					$this->log("Attempted to traverse chain with no nodes");
				}

				return false;
			}

			if (!$dispatch->isValid()) {
				if ($this->_doDebug) {
					$this->log("Attempted to traverse chain with invalid dispatch: " . $dispatch);
				}

				return false;
			}

			if ($dispatch->isConsumable() && $dispatch->isConsumed()) {
				if ($this->_doDebug) {
					$this->log("Attempted to traverse chain with consumed dispatch: " . $dispatch);
				}

				return false;
			}

			if ($sender === null) {
				$sender = $this;
			}

			$isConsumable = $dispatch->isConsumable();

			if ($this->_isEvent) {
				if ($this->_doDebug) {
					$this->log("Sending dispatch (" . $dispatch . ") to event node: " . $this->_nodes[0]);
				}

				$this->_nodes[0]->process($sender, $dispatch);
			} else {
				$len = count($this->_nodes);

				for ($i = 0; $i < $len; ++$i) {
					if ($this->_doDebug) {
						$this->log("Sending dispatch (" . $dispatch . ") to node: " . $this->_nodes[$i]);
					}

					$this->_nodes[$i]->process($sender, $dispatch);

					if ($isConsumable && $dispatch->isConsumed()) {
						if ($this->_doDebug) {
							$this->log("Dispatch (" . $dispatch . ") consumed by node: " . $this->_nodes[$i]);
						}

						break;
					}
				}
			}

			return true;
		}

		protected function log($message) {
			if ($this->_logger !== null) {
				$this->_logger($message);
			}

			return;
		}
	}
