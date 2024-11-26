<?php

namespace App\Lib\BankQR;

class Functions
{
    private static $banks;
    private static $json_banks;
    
    public static function addField(string $current_string, string $code, string $value): string
    {
        $new_value = $current_string;
        if (empty($new_value)) $new_value = '';

        $new_value = $new_value . $code . sprintf('%02d', strlen($value)) . $value;
        return $new_value;
    }

    public static function generateMerchantInfo(string $bank_id, string $account_no, bool $is_account): string
    {
        $merchant_info = '';
        $receiver_info = '';
        $service_code = Functions::getNapasServiceCode($is_account);
        $bin_code = '';
        try {
            $bin_code = Functions::getBIN($bank_id);
        } catch (InvalidBankIdException $e) {
            throw $e;
        }
        $receiver_info = Functions::addField($receiver_info, Field::CONSUMER_INFO_CONSUMER_BIN, $bin_code);
        $receiver_info = Functions::addField($receiver_info, Field::CONSUMER_INFO_CONSUMER_MERCHANT, $account_no);

        $merchant_info = Functions::addField($merchant_info, Field::CONSUMER_INFO_GUID, "A000000727");
        $merchant_info = Functions::addField($merchant_info, Field::CONSUMER_INFO_CONSUMER, $receiver_info);
        $merchant_info = Functions::addField($merchant_info, Field::CONSUMER_INFO_SERVICE_CODE, $service_code);

        return $merchant_info;
    }

    /**
     * Get bin code
     * @param string $bankId/bin/shortname
     * @return string
     * @throws InvalidBankIdException
     */
    private static function getBIN(string $bank_id): string
    {
        if (empty($bank_id)) {
            throw new InvalidBankIdException();
        }

        $bank_id = strtolower($bank_id);

        if (empty(self::$banks)) {
            $banks_data = self::loadDataBanks();
            self::$banks = array();
            foreach ($banks_data['data'] as $item) {
                self::$banks[strtolower($item['code'])] = strtolower($item['bin']);
                self::$banks[strtolower($item['bin'])] = strtolower($item['bin']);
                self::$banks[strtolower($item['short_name'])] = strtolower($item['bin']);
            }
        }

        if (isset(self::$banks[$bank_id])) {
            return self::$banks[$bank_id];
        } else {
            throw new InvalidBankIdException();
        }
    }

    public static function isValidAmount(int $amount): bool
    {
        $regExpPattern = '/^\d{1,}\.?\d{0,2}$/';
        $currency_test = trim($amount);
        return preg_match($regExpPattern, $currency_test);
    }

    public static function getNapasServiceCode(bool $is_card): string
    {
        if ($is_card) {
            return Constants::NAPAS_247_BY_CARD;
        } else {
            return Constants::NAPAS_247_BY_ACCOUNT;
        }
    }

    public static function loadDataBanks() {
        if (!isset(self::$json_banks)) {
            $banks = @file_get_contents(ROOT . '\database_initialization\banks.json');
            // $banks = @file_get_contents(__DIR__ . '/config/banks.json');
            self::$json_banks = json_decode($banks,true);
        }

        return self::$json_banks;
    }
}
