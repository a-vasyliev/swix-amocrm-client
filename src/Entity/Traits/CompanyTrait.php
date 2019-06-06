<?php

namespace Swix\AmoCrm\Entity\Traits;

use Webmozart\Assert\Assert;

trait CompanyTrait
{
    /** @var array|null */
    private $company;

    /**
     * @return array|null
     */
    public function getCompany(): ?array
    {
        return $this->company;
    }

    /**
     * @param array|null $company
     * @return self
     */
    public function setCompany(array $company = null): self
    {
        if (is_array($company) && !empty($company)) {
            Assert::keyExists($company, 'id');
        } else {
            $company = null;
        }

        $this->company = $company;

        return $this;
    }
}