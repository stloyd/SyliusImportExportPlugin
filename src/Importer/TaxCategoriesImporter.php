<?php

declare(strict_types=1);

namespace FriendsOfSylius\SyliusImportExportPlugin\Importer;

use FriendsOfSylius\SyliusImportExportPlugin\Exception\ImporterException;
use Port\Reader;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class TaxCategoriesImporter extends AbstractImporter
{
    /** @var array */
    private $headerKeys = ['Code', 'Name', 'Description'];

    /**
     * {@inheritdoc}
     */
    protected function assertKeys(Reader $reader)
    {
        if (!method_exists($reader, 'getColumnHeaders')) {
            throw new ImporterException('Missing "getColumnHeaders" method on reader');
        }

        $missingHeaders = array_diff($this->headerKeys, $reader->getColumnHeaders());
        if (!empty($missingHeaders)) {
            throw new ImporterException('Missing expected headers: ' . implode(', ', $missingHeaders));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createOrUpdateObject(array $row): void
    {
        /** @var TaxCategoryInterface $taxCategory */
        $taxCategory = $this->repository->findOneBy(['code' => $row['Code']]);

        if (null === $taxCategory) {
            $taxCategory = $this->factory->createNew();
            $taxCategory->setCode($row['Code']);
            $this->objectManager->persist($taxCategory);
        }

        $taxCategory->setName($row['Name']);
        $taxCategory->setDescription($row['Description']);
    }
}
