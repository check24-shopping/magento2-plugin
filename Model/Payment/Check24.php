<?php

namespace Check24Shopping\OrderImport\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;

class Check24 extends AbstractMethod
{

    protected $_code = 'check24';
    protected $_canUseInternal = true;

}
