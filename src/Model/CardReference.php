<?php

namespace Omnipay\Stripe\Model;

class CardReference
{
	private $customerId = null;
	private $cardId = null;

	/**
	 * @param string $data JSON encoded string representing a card reference
	 */
	public function __construct($data = null)
	{
		if ($data) {
			$data = json_decode($data);
			if (isset($data->customerId)) {
				$this->customerId = $data->customerId;
			}
			if (isset($data->cardId)) {
				$this->cardId = $data->cardId;
			}
		}
	}

	public function __toString()
	{
		$data = array(
			'customerId' => $this->customerId,
			'cardId' => $this->cardId
		);
		return json_encode($data);
	}

	public function getCustomerId()
	{
		return $this->customerId;
	}

	public function setCustomerId($customerId)
	{
		$this->customerId = $customerId;
	}

	public function getCardId()
	{
		return $this->cardId;
	}

	public function setCardId($cardId)
	{
		$this->cardId = $cardId;
	}
}