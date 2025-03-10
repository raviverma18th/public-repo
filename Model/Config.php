<?php
/**
 * Eighteentech_VirtualFoot Configuration Model
 * 
 * @category  Eighteentech
 * @package   Eighteentech_VirtualFoot
 * @author    Eighteentech
 */

namespace Eighteentech\VirtualFoot\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const XML_PATH_VIRTUALFOOT_GENERAL = 'virtualfoot/general/';
    const XML_PATH_VIRTUALFOOT_AUTHENTICATIONS = 'virtualfoot/authentications/';
    const XML_PATH_VIRTUALFOOT_PRODUCTS = 'virtualfoot/products/';
    const XML_PATH_VIRTUALFOOT_USERS = 'virtualfoot/users/';

    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get General Config Value
     *
     * @param string $field
     * @return string|null
     */
    public function getGeneralConfig($field)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_VIRTUALFOOT_GENERAL . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Authentications Config Value
     *
     * @param string $field
     * @return string|null
     */
    public function getAuthenticationsConfig($field)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_VIRTUALFOOT_AUTHENTICATIONS . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Products Config Value
     *
     * @param string $field
     * @return string|null
     */
    public function getProductsConfig($field)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_VIRTUALFOOT_PRODUCTS . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Users Config Value
     *
     * @param string $field
     * @return string|null
     */
    public function getUsersConfig($field)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_VIRTUALFOOT_USERS . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}