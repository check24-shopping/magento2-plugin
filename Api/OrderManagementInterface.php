<?php

namespace Check24Shopping\OrderImport\Api;

interface OrderManagementInterface
{

    public function getStoreIdByPartnerId($partnerId);

    public function buildStreetData($street, $remarks, $storeId);

}
