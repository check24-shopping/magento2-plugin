<?php

namespace Check24\OrderImport\Api;

interface OrderManagementInterface
{

    public function getStoreIdByPartnerId($partnerId);

    public function buildStreetData($street, $remarks, $storeId);

}
