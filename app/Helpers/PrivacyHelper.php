<?php

namespace App\Helpers;

class PrivacyHelper
{
    /** @var list<string> */
    private const PII_NAME_FIELDS = [
        'first_name',
        'last_name',
        'middle_name',
        'requestor_first_name',
        'requestor_last_name',
        'requestor_middle_name',
    ];

    /** @var list<string> */
    private const PII_SCALAR_FIELDS = [
        'contact_number',
        'email',
        'complete_address',
        'dob',
        'registered_disability',
        'pwd',
    ];

    /**
     * Determine whether the viewer may see unmasked demographic data.
     */
    public static function canViewUnmaskedPii(?object $user, object $subject): bool
    {
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasPiiClearance') && $user->hasPiiClearance()) {
            return true;
        }

        if (isset($subject->email) && isset($user->email)) {
            return strtolower((string) $user->email) === strtolower((string) $subject->email);
        }

        return false;
    }

    /**
     * Obfuscate PII fields on a request object based on user clearance.
     */
    public static function filterPII(object $request, ?object $user): object
    {
        if (self::canViewUnmaskedPii($user, $request)) {
            return $request;
        }

        $cloned = clone $request;

        foreach (self::PII_NAME_FIELDS as $field) {
            if (isset($cloned->{$field}) && $cloned->{$field}) {
                $cloned->{$field} = self::maskName((string) $cloned->{$field});
            }
        }

        if (isset($cloned->contact_number) && $cloned->contact_number) {
            $cloned->contact_number = self::maskContact((string) $cloned->contact_number);
        }

        if (isset($cloned->email) && $cloned->email) {
            $cloned->email = self::maskEmail((string) $cloned->email);
        }

        if (isset($cloned->complete_address) && $cloned->complete_address) {
            $cloned->complete_address = self::REDACTED_LABEL;
        }

        if (isset($cloned->dob) && $cloned->dob) {
            $cloned->dob = null;
        }

        if (isset($cloned->registered_disability)) {
            $cloned->registered_disability = null;
        }

        if (isset($cloned->pwd)) {
            $cloned->pwd = null;
        }

        if (isset($cloned->custom_fields) && is_array($cloned->custom_fields)) {
            $cloned->custom_fields = self::maskCustomFields($cloned->custom_fields);
        }

        if (isset($cloned->display_name)) {
            $parts = explode(', ', (string) $cloned->display_name, 2);
            if (count($parts) === 2) {
                $cloned->display_name = self::maskName($parts[0]) . ', ' . self::maskName($parts[1]);
            } else {
                $cloned->display_name = self::maskName((string) $cloned->display_name);
            }
        }

        return $cloned;
    }

    /**
     * Mask a collection of records consistently.
     */
    public static function filterCollection(iterable $records, ?object $user): array
    {
        $result = [];
        foreach ($records as $record) {
            $result[] = self::filterPII($record, $user);
        }

        return $result;
    }

    public const REDACTED_LABEL = '[REDACTED FOR PRIVACY]';

    /**
     * Mask custom field answers that may contain PII.
     *
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    public static function maskCustomFields(array $fields): array
    {
        $masked = [];

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $masked[$key] = self::maskCustomFields($value);
                continue;
            }

            $normalizedKey = strtolower((string) $key);
            if (self::looksLikePiiField($normalizedKey)) {
                $masked[$key] = is_string($value) && str_contains($value, '@')
                    ? self::maskEmail($value)
                    : (is_string($value) && preg_match('/\d{6,}/', $value)
                        ? self::maskContact($value)
                        : (is_string($value) ? self::maskName($value) : self::REDACTED_LABEL));
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }

    private static function looksLikePiiField(string $key): bool
    {
        $needles = ['name', 'email', 'contact', 'phone', 'address', 'birth', 'dob', 'age', 'gender'];

        foreach ($needles as $needle) {
            if (str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function maskName(string $str): string
    {
        $str = trim($str);
        $len = mb_strlen($str);
        if ($len <= 1) {
            return '*';
        }
        if ($len === 2) {
            return mb_substr($str, 0, 1) . '*';
        }

        return mb_substr($str, 0, 1) . str_repeat('*', $len - 2) . mb_substr($str, -1);
    }

    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '*****';
        }

        $name = $parts[0];
        $domain = $parts[1];
        $len = mb_strlen($name);

        if ($len <= 2) {
            $maskedName = str_repeat('*', $len);
        } else {
            $maskedName = mb_substr($name, 0, 2) . str_repeat('*', $len - 2);
        }

        return $maskedName . '@' . $domain;
    }

    public static function maskContact(string $num): string
    {
        $num = trim($num);
        $len = strlen($num);
        if ($len <= 6) {
            return str_repeat('*', $len);
        }

        return substr($num, 0, 4) . str_repeat('*', $len - 6) . substr($num, -2);
    }
}
