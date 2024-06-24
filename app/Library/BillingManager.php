<?php

namespace Acelle\Library;

use Exception;
use Acelle\Model\Setting;

class BillingManager
{
    protected $gateways = [];
    protected $returnUrl;

    public function __construct($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function register($type, $constructor)
    {
        if ($this->isGatewayRegistered($type)) {
            throw new Exception(sprintf('Payment gateway type "%s" is already registered', $type));
        }

        $this->gateways[$type] = $constructor;
    }

    public function getGateways(): array
    {
        $list = [];
        foreach ($this->gateways as $type => $constructor) {
            $list[] = $this->getGateway($type);
        }

        return $list;
    }

    public function getGateway(string $type)
    {
        if (!$this->isGatewayRegistered($type)) {
            throw new Exception(sprintf('Payment gateway type "%s" is not registered', $type));
        }

        $constructor = $this->gateways[$type];
        $gateway = $constructor();

        if (is_null($gateway)) {
            throw new Exception("Invalid constructor closure for payment service {$type}");
        }

        return $gateway;
    }

    public function isGatewayRegistered($type)
    {
        return array_key_exists($type, $this->gateways);
    }

    public function getEnabledPaymentGateways()
    {
        if (!Setting::get('gateways')) {
            return [];
        }

        $enabledTypes = json_decode(Setting::get('gateways'));
        $list = [];

        foreach ($enabledTypes as $t) {
            if ($this->isGatewayRegistered($t) && $this->getGateway($t)->isActive()) {
                $list[] = $this->getGateway($t);
            }
        }
        return $list;
    }

    public function isPaymentGatewayTypeEnabled($gw)
    {
        if (!Setting::get('gateways')) {
            return false;
        }

        $enabledTypes = json_decode(Setting::get('gateways'));
        if ($this->isGatewayRegistered($gw->getType()) && in_array($gw->getType(), $enabledTypes)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Enable payment gateway.
     *
     * @var void
     */
    public static function enablePaymentGateway($gatewayType)
    {
        $gateways = Setting::get('gateways') ? json_decode(Setting::get('gateways'), true) : [];
        $gateways = array_unique(array_merge($gateways, [$gatewayType]));
        Setting::set('gateways', json_encode($gateways));
    }

    /**
     * Disable payment gateway.
     *
     * @var void
     */
    public static function disablePaymentGateway($gatewayType)
    {
        $gateways = Setting::get('gateways') ? json_decode(Setting::get('gateways'), true) : [];
        $gateways = array_values(array_diff($gateways, [$gatewayType]));
        Setting::set('gateways', json_encode($gateways));
    }

    public function isGatewayEnabled($gateway): bool
    {
        foreach ($this->getEnabledPaymentGateways() as $gw) {
            if ($gw->getType() == $gateway->getType()) {
                return true;
            }
        }

        return false;
    }
}
