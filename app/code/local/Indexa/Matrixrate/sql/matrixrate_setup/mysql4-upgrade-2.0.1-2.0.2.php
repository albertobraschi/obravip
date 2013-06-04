<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('shipping_matrixrate')} ADD COLUMN estimated_delivery_time INT DEFAULT 0;");

$installer->endSetup();