<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\KitchenDelivery;
use App\Models\ProductInventory;
use App\Models\ProductRequisition;
use App\Models\Requisition;
use App\PrintPDF;
use App\Prints\EmployeePrint;
use App\Prints\ItemInventoryItemPrint;
use App\Prints\ItemStockPrint;
use App\Prints\KitchenDeliveryPrint;
use App\Prints\ProductInventoryDetailPrint;
use App\Prints\ProductInventoryProductPrint;
use App\Prints\ProductRequisitionPrint;
use App\Prints\ProductStockPrint;
use App\Prints\RequisitionPrint;
use App\Prints\SaleStatusPrint;
use App\Prints\SaleVatPrint;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // public function invoice(Order $order)
    // {
    //     $fileName = 'Order ' . $order->invoice_number . ' Invoice.pdf';
    // 		return PrintPDF::make(new InvoicePrint($order), $fileName)
    // 			->inline();
    // }

    /**
     * Display a report pdf of the resource.
     *
     * @return Response
     */
    public function sale(Request $request)
    {
        $fileName = 'Sale Status - '.$request->start_date.' - '.$request->end_date.'.pdf';

        return PrintPDF::make(new SaleStatusPrint($request), $fileName)->inline();
    }

    /**
     * Display a report pdf of the resource.
     *
     * @return Response
     */
    public function employee(Request $request)
    {
        $fileName = 'Employee Report - '.$request->start_date.' - '.$request->end_date.'.pdf';

        return PrintPDF::make(new EmployeePrint($request->start_date, $request->end_date, $request->employee_id), $fileName)
            ->inline();
    }

    /**
     * Display a report pdf of the resource.
     *
     * @return Response
     */
    public function vat(Request $request)
    {
        $fileName = 'VAT Report - '.$request->start_date.' - '.$request->end_date.'.pdf';

        return PrintPDF::make(new SaleVatPrint($request->start_date, $request->end_date), $fileName)
            ->inline();
    }

    public function requisition(Requisition $requisition)
    {
        $fileName = 'Requisition '.$requisition->date.'.pdf';

        return PrintPDF::make(new RequisitionPrint($requisition), $fileName)
            ->inline();
    }

    public function product_requisition(ProductRequisition $product_requisition)
    {
        $fileName = 'Product Requisition '.$product_requisition->date.'.pdf';

        return PrintPDF::make(new ProductRequisitionPrint($product_requisition), $fileName)
            ->inline();
    }

    public function kitchen_delivery(KitchenDelivery $kitchen_delivery)
    {
        $fileName = 'Product Requisition '.$kitchen_delivery->date.'.pdf';

        return PrintPDF::make(new KitchenDeliveryPrint($kitchen_delivery), $fileName)
            ->inline();
    }

    /**
     * Display a listing of items.
     *
     * @return Item
     */
    public function item_stock()
    {
        $item_name = Helpers::getSettingValueOf('item');
        $fileName = $item_name.' Stock - '.' - '.now()->format('d/m/Y').'.pdf';

        return PrintPDF::make(new ItemStockPrint(), $fileName)
            ->inline();
    }

    public function item_inventory(Request $request)
    {
        $item_inventory_name = Helpers::getSettingValueOf('item_inventory');
        $fileName = $item_inventory_name . ' ' . now()->format('d/m/Y').'.pdf';

        return PrintPDF::make(new ItemInventoryItemPrint($request), $fileName)
            ->inline();
    }

    /**
     * Display a listing of products.
     *
     * @return Product
     */
    public function product_stock()
    {
        $product_name = Helpers::getSettingValueOf('product');
        $fileName = $product_name.' Stock - '.' - '.now()->format('d/m/Y').'.pdf';

        return PrintPDF::make(new ProductStockPrint(), $fileName)
            ->inline();
    }

    public function product_inventory(Request $request)
    {
        $product_inventory_name = Helpers::getSettingValueOf('product_inventory');
        $fileName = $product_inventory_name . ' ' . now()->format('d/m/Y').'.pdf';

        return PrintPDF::make(new ProductInventoryProductPrint($request), $fileName)
            ->inline();
    }

    public function product_inventory_detail(ProductInventory $product_inventory)
    {
        $product_inventory_name = Helpers::getSettingValueOf('product_inventory');
        $fileName = $product_inventory_name.' Detail '.now()->parse($product_inventory->date)->format('d/m/Y').'.pdf';

        return PrintPDF::make(new ProductInventoryDetailPrint($product_inventory->id), $fileName)
            ->inline();
    }
}
