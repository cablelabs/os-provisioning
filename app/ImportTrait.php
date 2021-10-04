<?php

namespace App;

use Log;
use Modules\ProvBase\Entities\Contract;

/**
 * Helper to hold functionality used for import commands
 *
 * @author Nino Ryschawy
 */
trait ImportTrait
{
    public $importantTodos = [];

    /**
     * Check if already a (n internet) contract exists for this customer
     *
     * @return object contract if exists, otherwise null or []
     */
    public function contractExists($number, $firstname, $lastname, $street, $city)
    {
        $contract = Contract::where('number', '=', $number)->first();

        if ($contract) {
            // Check if name and address differs - could be a different customer
            // Attention: strtolower doesn't work for ÄÖÜ, but i dont know if a street begins with such a char
            if ($contract->firstname != $firstname || $contract->lastname != $lastname || strtolower($contract->street) != strtolower($street)) {
                $msg = "Vertragsnummer $number existiert bereits, aber Name, Straße oder Stadt weichen ab - Bitte korrigieren Sie die Daten!";
                Log::warning($msg);
                $this->importantTodos[] = $msg;

                return $contract;
            }

            Log::notice("Vertrag $number existiert bereits übereinstimmend ($firstname $lastname) - füge nur TV Tarif hinzu");
        } else {
            // TODO: Check if customer/name & address already exists with another contract number
            $contract = Contract::where('firstname', '=', $firstname)->where('lastname', '=', $lastname)
                // make Straße or Str. respective ..straße or ..str. indifferent on searching in DB
                ->whereIn('street', [$street, str_replace(['trasse', 'traße'], 'tr.', $street)])
                ->where('city', '=', $city)->first();

            if ($contract) {
                // $msg = "Customer $number is probably already added with different contract number [$contract->number] (found same name [$firstname $lastname], city & street [$street]). Check this manually!";
                $msg = "Kunde $number existiert bereits unter der Vertragsnummer $contract->number (selber Name, Stadt, Straße: , $city, $street gefunden). Füge nur TV Tarif hinzu.";
                Log::notice($msg);
            }
        }

        return $contract;
    }

    public function printImportantTodos()
    {
        if (! $this->importantTodos) {
            return;
        }

        echo "\n".implode("\n", $this->importantTodos)."\n";
    }
}
