<?php
/**
 * GetRecommendations GraphQL Resolver
 *
 * @category  Eighteentech
 * @package   Eighteentech_VirtualFoot
 * @author    Eighteentech
 */

namespace Eighteentech\VirtualFoot\GraphQl\Model\Resolver;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ResolverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;
use Eighteentech\VirtualFoot\Model\Config;

class GetRecommendations implements ResolverInterface
{
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Curl $curl
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        Curl $curl,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->curl = $curl;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Resolve GraphQL query
     *
     * @param Field $field
     * @param mixed $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $recommendationUrl = $this->config->getProductsConfig('recommendation_url');
        $xApiKey = $this->config->getProductsConfig('x_api_key');
        $autogeneratedToken = $this->config->getAuthenticationsConfig('autogenerated_token');

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'x-api-key' => $xApiKey,
            'Authorization' => 'Bearer ' . $autogeneratedToken
        ];

        $payload = http_build_query([
            'non_oneasics_id' => $args['non_oneasics_id'],
            'region' => $args['region'],
            'country' => $args['country'],
            'site' => $args['site'],
            'language' => $args['language'],
            'style' => $args['style'],
            'color' => $args['color'],
            'per_page' => $args['per_page'],
            'page' => $args['page']
        ]);

        $this->curl->setHeaders($headers);
        $this->curl->get($recommendationUrl . '?' . $payload);

        $response = json_decode($this->curl->getBody(), true);

        if (isset($response['data']['items'])) {
            return $response['data']['items'];
        } else {
            $this->logger->error('Failed to fetch recommendations.');
            return ['status' => 'error', 'message' => 'Failed to fetch recommendations.'];
        }
    }
}