<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Support\ServiceChargeManager;
use Illuminate\Http\Request;

class ServiceChargeController extends Controller
{
    public function index()
    {
        return view('dashboard.settings.service-charge', [
            'todayPercent' => ServiceChargeManager::percentage('today'),
            'tomorrowPercent' => ServiceChargeManager::percentage('tomorrow'),
            'tiers' => ServiceChargeManager::tiers(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'today_percent'    => ['required', 'numeric', 'min:0', 'max:100'],
            'tomorrow_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        ServiceChargeManager::updatePercentage((float) $data['today_percent'], 'today');
        ServiceChargeManager::updatePercentage((float) $data['tomorrow_percent'], 'tomorrow');

        return back()->with('success', 'Default service charge updated.');
    }

    public function storeTier(Request $request)
    {
        $data = $request->validate([
            'min_amount' => ['required', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'gt:min_amount'],
            'percent'    => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        ServiceChargeManager::addTier(
            (float) $data['min_amount'],
            isset($data['max_amount']) ? (float) $data['max_amount'] : null,
            (float) $data['percent']
        );

        return back()->with('success', 'Amount-based service charge tier added.');
    }

    public function destroyTier(int $tier)
    {
        ServiceChargeManager::deleteTier($tier);

        return back()->with('success', 'Tier removed.');
    }
}
