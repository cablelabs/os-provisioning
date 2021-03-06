<?php

namespace Modules\ProvBase\Services;

use App\V1\Service;

class ModemService extends Service
{
    public function getPosModems($options = [])
    {
        $query = $this->repository->createBaseBuilder($options);
        $query->select('modem.*')
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw('COUNT(CASE WHEN `us_pwr` = 0 THEN 1 END) as offline')
            ->selectRaw(' AVG(us_pwr) AS us_pwr ')
            ->groupBy('modem.x', 'modem.y')
            ->join('contract', 'contract.id', 'modem.contract_id')
            ->whereNull('modem.deleted_at')
            ->whereNull('contract.deleted_at')
            ->where('contract_start', '<=', date('Y-m-d'))
            ->where(whereLaterOrEqual('contract_end', date('Y-m-d')))
            ->orderBy('us_pwr', 'ASC');
        if (isset($options['paginate']) && $options['paginate']) {
            return $query->paginate($options['limit'], ['modem.*']);
        }

        return $query->get();
    }

    public function getModemsOfSameLocation(int $modem_id, $options = [])
    {
        $modem = $this->repository->getById($modem_id, $options);
        $query = $this->repository->createBaseBuilder($options);
        $query->select('modem.*')
            ->join('contract', 'contract.id', 'modem.contract_id')
            ->whereNull('modem.deleted_at')
            ->whereNull('contract.deleted_at')
            ->where('modem.x', $modem->x)
            ->where('modem.y', $modem->y)
            ->where('contract_start', '<=', date('Y-m-d'))
            ->where(whereLaterOrEqual('contract_end', date('Y-m-d')))
            ->orderBy('us_pwr', 'DESC');

        if (isset($options['paginate']) && $options['paginate']) {
            return $query->paginate($options['limit'], ['modem.*']);
        }

        return $query->get();
    }
}
