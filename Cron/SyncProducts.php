<?php
/**
 * SyncProducts Cron Job
 * 
 * @category  Eighteentech
 * @package   Eighteentech_VirtualFoot
 * @author    Eighteentech
 */

namespace Eighteentech\VirtualFoot\Cron;

use Psr\Log\LoggerInterface;
use Eighteentech\VirtualFoot\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;

class SyncProducts
{
    protected $logger;
    protected $config;
    protected $productCollectionFactory;
    protected $curl;
    protected $storeManager;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Config $config
     * @param CollectionFactory $productCollectionFactory
     * @param Curl $curl
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        CollectionFactory $productCollectionFactory,
        Curl $curl,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute the cron job to sync products
     */
    public function execute()
    {
        $autogeneratedToken = $this->config->getAuthenticationsConfig('autogenerated_token');
        $productSyncUrl = $this->config->getProductsConfig('product_sync_url');
        $xApiKey = $this->config->getProductsConfig('x_api_key');

        $headers = [
            'Content-Type' => 'application/json',
            'x-api-key' => $xApiKey,
            'Authorization' => 'Bearer ' . $autogeneratedToken
        ];

        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('type_id', 'simple');

        $items = [];

        foreach ($products as $product) {
            $items[] = [
                "item_id" => $product->getSku(),
                "color_id" => explode('.', $product->getSku())[0],
                "external_id" => "AIN_" . $product->getSku(),
                "title" => $product->getName(),
                "description" => $product->getDescription(),
                "link" => $product->getProductUrl(),
                "image_link" => $product->getImageUrl(),
                "gender" => $product->getAttributeText('gender'),
                "size" => $product->getAttributeText('size'),
                "color" => $product->getAttributeText('color')
            ];
        }

        $payload = [
            'region' => $this->config->getGeneralConfig('region'),
            'country' => $this->config->getGeneralConfig('country'),
            'site' => $this->config->getGeneralConfig('site'),
            'language' => $this->config->getGeneralConfig('language'),
            'items' => $items
        ];

        $this->curl->setHeaders($headers);
        $this->curl->post($productSyncUrl, json_encode($payload));

        if ($this->curl->getStatus() == 200) {
            $this->logger->info('Products synchronized successfully.');
        } else {
            $this->logger->error('Failed to synchronize products.');
        }
    }
}