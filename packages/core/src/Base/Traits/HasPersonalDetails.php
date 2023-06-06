<?php

namespace Lunar\Base\Traits;

trait HasPersonalDetails
{
    /**
     * Return the full name of the customer.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                "{$this->title} {$this->last_name} {$this->first_name} "
            )
        );
    }
}
