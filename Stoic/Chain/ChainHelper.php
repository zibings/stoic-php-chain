<?php

	namespace Stoic\Chain;

	class ChainHelper {
		protected $_nodes = array();
		protected $_isEvent = false;


		public function __construct($isEvent = false) {
			$this->_isEvent = $isEvent;

			return;
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

		public function isEvent() {
			return $this->_isEvent;
		}

		public function linkNode(NodeBase $node) {
			if (!$node->isValid()) {
				return $this;
			}

			if ($this->_isEvent) {
				$this->_nodes = array($node);
			} else {
				$this->_nodes[] = $node;
			}

			return $this;
		}

		public function traverse(DispatchBase &$dispatch, $sender = null) {
			if (count($this->_nodes) < 1) {
				return false;
			}

			if (!$dispatch->isValid()) {
				return false;
			}

			if ($dispatch->isConsumable() && $dispatch->isConsumed()) {
				return false;
			}

			if ($sender === null) {
				$sender = $this;
			}

			$isConsumable = $dispatch->isConsumable();

			if ($this->_isEvent) {
				$this->_nodes[0]->process($sender, $dispatch);
			} else {
				$len = count($this->_nodes);

				for ($i = 0; $i < $len; ++$i) {
					$this->_nodes[$i]->process($sender, $dispatch);

					if ($isConsumable && $dispatch->isConsumed()) {
						break;
					}
				}
			}

			return true;
		}
	}
