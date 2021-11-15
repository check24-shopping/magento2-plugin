<?php

namespace Check24\OrderImport\Api;

use Check24\OrderImport\Api\Data\DocumentTrackingInterface;

interface DocumentTrackingRepositoryInterface
{
    public function save(DocumentTrackingInterface $documentTracking);

    public function load($id): ?DocumentTrackingInterface;

    public function delete(DocumentTrackingInterface $documentTracking);

    public function findByDocumentId(int $documentId): ?DocumentTrackingInterface;
}
