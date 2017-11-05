<?php

declare(strict_types=1);

namespace FriendsOfSylius\SyliusImportExportPlugin\Importer;

use FriendsOfSylius\SyliusImportExportPlugin\Exception\ImporterException;
use Port\Reader;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class PaymentMethodsImporter extends AbstractImporter
{
    /** @var PaymentMethodFactoryInterface */
    protected $factory;

    /** @var array */
    private $headerKeys = ['Code', 'Gateway', 'Name', 'Instructions'];

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
        $paymentMethod = $this->repository->findOneBy(['code' => $row['Code']]);

        if ($paymentMethod === null) {
            /** @var PaymentMethodInterface $paymentMethod */
            $paymentMethod = $this->factory->createWithGateway($row['Gateway']);
            $paymentMethod->setCode($row['Code']);
            $this->objectManager->persist($paymentMethod);
        }

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            throw new ImporterException('Gateway does not exist:' . $row['Gateway']);
        }

        $gatewayConfig->setGatewayName($row['Name']);
        $paymentMethod->setGatewayConfig($gatewayConfig);

        $paymentMethod->setName($row['Name']);
        $paymentMethod->setInstructions($row['Instructions']);
    }
}
