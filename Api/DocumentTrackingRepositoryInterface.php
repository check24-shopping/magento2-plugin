<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\DocumentTrackingInterface;

interface DocumentTrackingRepositoryInterface
{
    public function save(DocumentTrackingInterface $documentTracking);

    public function load($id): ?DocumentTrackingInterface;

    public function delete(DocumentTrackingInterface $documentTracking);

    public function findByDocumentId(int $documentId): ?DocumentTrackingInterface;
}
