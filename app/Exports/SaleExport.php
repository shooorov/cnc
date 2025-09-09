<?php

namespace App\Exports;

use App\Data;
use App\Helpers;
use App\UseBranch;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Http\Requests;
use App\RolePermission;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class SaleExport implements FromView
{
    public function __construct(private $isSuperAdmin,private $start_date, private $end_date)
    {
    }
	

    public function view(): View
    {
		$isSuperAdmin =$this->isSuperAdmin;
		// dd($isSuperAdmin);
		// die();
        $end_date = Helpers::dayEndedAt($this->end_date);
        $start_date = Helpers::dayStartedAt($this->start_date);
		$isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');

		$cards_summary = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->summaryCards($isSuperAdmin);
		$cards_payment_method = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->paymentMethodCards($isSuperAdmin);
		$cards_dine_type = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->dineTypeCards($isSuperAdmin);
		$cards_recipe = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->recipeCards($isSuperAdmin);

		$items = array_merge(
			$cards_summary,
			[[]],
			$cards_recipe,
			[[]],
			$cards_dine_type,
			[[]],
			$cards_payment_method,
		);

        return view('exports.sales', [
            'items' => $items,
        ]);
    }
}
