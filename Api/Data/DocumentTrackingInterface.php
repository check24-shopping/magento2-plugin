<?php

namespace Check24\OrderImport\Api\Data;

interface DocumentTrackingInterface
{
    const TABLE_NAME = 'check24_document_tracking';
    const FIELD_ID = 'id';
    const FIELD_DOCUMENT_ID = 'document_id';

    public function setId($id): self;

    public function getId();

    public function setDocumentId(int $documentId): self;

    public function getDocumentId();
}
