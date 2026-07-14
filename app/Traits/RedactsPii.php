<?php

namespace App\Traits;

use App\Helpers\PrivacyHelper;

trait RedactsPii
{
    /**
     * Return a privacy-safe copy of this model for the given viewer.
     */
    public function withRedactedPii(?object $user): static
    {
        return PrivacyHelper::filterPII($this, $user);
    }

    /**
     * Check if the viewer can see unmasked PII for this record.
     */
    public function viewerHasPiiClearance(?object $user): bool
    {
        return PrivacyHelper::canViewUnmaskedPii($user, $this);
    }
}
