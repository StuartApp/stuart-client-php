<?php

namespace Stuart;

class Pricing
{
    /**
     * @var float
     */
    private $priceTaxIncluded;

    /**
     * @var float
     */
    private $priceTaxExcluded;

    /**
     * @return float
     */
    public function getPriceTaxIncluded()
    {
        return $this->priceTaxIncluded;
    }

    /**
     * @param float $priceTaxIncluded
     */
    public function setPriceTaxIncluded($priceTaxIncluded)
    {
        $this->priceTaxIncluded = $priceTaxIncluded;
    }

    /**
     * @return float
     */
    public function getPriceTaxExcluded()
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @param float $priceTaxExcluded
     */
    public function setPriceTaxExcluded($priceTaxExcluded)
    {
        $this->priceTaxExcluded = $priceTaxExcluded;
    }
}
