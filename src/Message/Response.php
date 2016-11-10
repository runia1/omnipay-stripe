<?php

/**
 * Stripe Response.
 */
namespace Omnipay\Stripe\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Stripe\Model\CardReference;

/**
 * Stripe Response.
 *
 * This is the response class for all Stripe requests.
 *
 * @see \Omnipay\Stripe\Gateway
 */
class Response extends AbstractResponse
{
    /**
     * Request id
     *
     * @var string URL
     */
    protected $requestId = null;
    
    /**
     * Is the transaction successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return !isset($this->data['error']);
    }

    /**
     * Get the charge reference from the response of FetchChargeRequest.
     * 
     * @deprecated 2.3.3:3.0.0 duplicate of \Omnipay\Stripe\Message\Response::getTransactionReference()
     * @see \Omnipay\Stripe\Message\Response::getTransactionReference()
     * @return array|null
     */
    public function getChargeReference()
    {
        if (isset($this->data['object']) && $this->data['object'] == 'charge') {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        if (isset($this->data['object']) && 'charge' === $this->data['object']) {
            return $this->data['id'];
        }
        if (isset($this->data['error']) && isset($this->data['error']['charge'])) {
            return $this->data['error']['charge'];
        }

        return null;
    }

    /**
     * Get the balance transaction reference.
     *
     * @return string|null
     */
    public function getBalanceTransactionReference()
    {
        if (isset($this->data['object']) && 'charge' === $this->data['object']) {
            return $this->data['balance_transaction'];
        }
        if (isset($this->data['object']) && 'balance_transaction' === $this->data['object']) {
            return $this->data['id'];
        }
        if (isset($this->data['error']) && isset($this->data['error']['charge'])) {
            return $this->data['error']['charge'];
        }

        return null;
    }

    /**
     * Get a customer reference, for createCustomer requests.
     *
     * @return string|null
     */
    public function getCustomerReference()
    {
        if (isset($this->data['object']) && 'customer' === $this->data['object']) {
            return $this->data['id'];
        }
        if (isset($this->data['object']) && 'card' === $this->data['object']) {
            if (!empty($this->data['customer'])) {
                return $this->data['customer'];
            }
        }

        return null;
    }

    /**
     * Get a card reference, for createCard or createCustomer requests.
     *
     * @return string|CardReference
     */
    public function getCardReference($serialize = true)
    {
	      //if the request was to create a new customer
        if (isset($this->data['object']) && 'customer' === $this->data['object']) {
            if (!empty($this->data['id']) && !empty($this->data['default_source'])) {
	            $cardReference = new CardReference();
	            $cardReference->setCustomerId($this->data['id']);
	            $cardReference->setCardId($this->data['default_source']);
	            if ($serialize) {
		            return (string)$cardReference; //this calls __toString() in the CardReference class.
	            }
            }
        }
        //if the request was to create a new card
        if (isset($this->data['object']) && 'card' === $this->data['object']) {
            if (!empty($this->data['id']) && !empty($this->data['customer'])) {
	            $cardReference = new CardReference();
	            $cardReference->setCustomerId($this->data['customer']);
	            $cardReference->setCardId($this->data['id']);
	            if ($serialize) {
		            return (string)$cardReference; //this calls __toString() in the CardReference class.
	            }
            }
        }
        //if the request was to create a new purchase
        if (isset($this->data['object']) && 'charge' === $this->data['object']) {
            if (!empty($this->data['source']) && !empty($this->data['source']['id']) && !empty($this->data['customer'])) {
	            $cardReference = new CardReference();
	            $cardReference->setCustomerId($this->data['customer']);
	            $cardReference->setCardId($this->data['source']['id']);
	            if ($serialize) {
		            return (string)$cardReference; //this calls __toString() in the CardReference class.
	            }
            }
        }

        return null;
    }

    /**
     * Get a token, for createCard requests.
     *
     * @return string|null
     */
    public function getToken()
    {
        if (isset($this->data['object']) && 'token' === $this->data['object']) {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * Get the card data from the response.
     *
     * @return array|null
     */
    public function getCard()
    {
        if (isset($this->data['card'])) {
            return $this->data['card'];
        }

        return null;
    }

    /**
     * Get the card data from the response of purchaseRequest.
     *
     * @return array|null
     */
    public function getSource()
    {
        if (isset($this->data['source']) && $this->data['source']['object'] == 'card') {
            return $this->data['source'];
        }

        return null;
    }

    /**
     * Get the subscription reference from the response of CreateSubscriptionRequest.
     *
     * @return array|null
     */
    public function getSubscriptionReference()
    {
        if (isset($this->data['object']) && $this->data['object'] == 'subscription') {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * Get the event reference from the response of FetchEventRequest.
     *
     * @return array|null
     */
    public function getEventReference()
    {
        if (isset($this->data['object']) && $this->data['object'] == 'event') {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * Get the invoice reference from the response of FetchInvoiceRequest.
     *
     * @return array|null
     */
    public function getInvoiceReference()
    {
        if (isset($this->data['object']) && $this->data['object'] == 'invoice') {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * Get the list object from a result
     *
     * @return array|null
     */
    public function getList()
    {
        if (isset($this->data['object']) && $this->data['object'] == 'list') {
            return $this->data['data'];
        }

        return null;
    }

    /**
     * Get the subscription plan from the response of CreateSubscriptionRequest.
     *
     * @return array|null
     */
    public function getPlan()
    {
        if (isset($this->data['plan'])) {
            return $this->data['plan'];
        } elseif (array_key_exists('object', $this->data) && $this->data['object'] == 'plan') {
            return $this->data;
        }

        return null;
    }

    /**
     * Get plan id
     *
     * @return string|null
     */
    public function getPlanId()
    {
        $plan = $this->getPlan();
        if ($plan && array_key_exists('id', $plan)) {
            return $plan['id'];
        }

        return null;
    }

    /**
     * Get invoice-item reference
     *
     * @return string|null
     */
    public function getInvoiceItemReference()
    {
        if (isset($this->data['object']) && $this->data['object'] == 'invoiceitem') {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()
    {
        if (!$this->isSuccessful()) {
            return $this->data['error']['message'];
        }

        return null;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getCode()
    {
        if (!$this->isSuccessful()) {
            return $this->data['error']['code'];
        }

        return null;
    }
    
    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set request id
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }
}
