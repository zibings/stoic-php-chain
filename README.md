# Stoic Chain System
An execution system inspired by the chain-of-responsibility design pattern.

## Concept
Similar to the chain-of-responsibility design pattern, chains are groups of
processing objects, or nodes, which receive data in order as it is passed
to the chain.

The entire system revolves around a simple - but effective - `ChainHelper`
class.  This class facilitates the linking of processing nodes together,
all derived from the `NodeBase` abstract class, and sending along data
to the nodes in the form of a class deriving from the `DispatchBase`
abstract class.

## Full Example
First, a fully working (albeit simple) example of working with the chain
system:

```php
<?php

	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;
	use Stoic\Chain\ChainHelper;

	class IncrementDispatch extends DispatchBase {
		public function initialize($input) {
			$this->makeValid();

			return;
		}

		public function increment($number) {
			return ++$number;
		}
	}

	class IncrementNode extends NodeBase {
		public function __construct() {
			$this->setKey('incrementNode');
			$this->setVersion('1.0.0');

			return;
		}

		public function process($sender, DispatchBase &$dispatch) {
			if (!($dispatch instanceof IncrementDispatch)) {
				return;
			}

			$dispatch->setResult($dispatch->increment(1));

			return;
		}
	}

	$chain = new ChainHelper();
	$chain->linkNode(new IncrementNode());

	$dispatch = new IncrementDispatch();
	$dispatch->initialize(null);

	$success = $chain->traverse($dispatch);
	$results = $dispatch->getResults();

	print_r($results);
```

### Creating a Dispatch
