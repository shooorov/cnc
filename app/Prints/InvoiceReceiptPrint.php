<?php

namespace App\Prints;

use App\Helpers;
use Exception;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class InvoiceReceiptPrint
{
    public function __construct(private $order)
    {
    }

    public function print()
    {
        $order = $this->order;
        $printer_name = config('printer.name');

        $image_path = realpath('img/logo-bnw.png');
        $opening_hours = $order?->branch?->opening_hours;
        $company_name = Helpers::getSettingValueOf('company_name');
        $company_address = $order?->branch?->address;
        $pos_end_line = $order?->branch?->pos_end_line;
        // $company_address_01 = Helpers::getSettingValueOf('company_address_01');
        // $company_address_02 = Helpers::getSettingValueOf('company_address_02');
        // $end_line_01 = Helpers::getSettingValueOf('end_line_01');
        // $end_line_02 = Helpers::getSettingValueOf('end_line_02');

        $items = [];
        $sl = 0;
        foreach ($order->products as $item) {
            $sl++;
            array_push($items, (new ItemList($sl, ($item->product->english_name ?? $item->product->name), $item->quantity, $item->rate, $item->total)));
        }
        // dd($items);

        try {
            // Enter the share name for your USB printer here
            // $connector = new FilePrintConnector("php://stdout");
            $connector = new WindowsPrintConnector($printer_name);

            /* Start the printer */
            $printer = new Printer($connector);

            /* Information for the receipt */

            /* Print top logo */
            // $logo = EscposImage::load($image_path, false);
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->graphics($logo);
            // $printer->feed();

            /* Name of shop */
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("$company_name\n");

            /* Shop Address */
            $printer->selectPrintMode();
            $printer->text("$company_address\n");
            $printer->feed();

            /* Title of receipt */
            $printer->setEmphasis(true);
            $printer->text(new Item('Order', $order->invoice_number));
            $printer->text(new Item('Time', now()->parse($order->date)->format('Y-m-d H:i')));
            $printer->text(new Item('Manager', $order->creator?->name ?? 'Manager'));
            $printer->text(new Item('Customer', $order->customer?->name ?? 'Customer'));
            $printer->feed();

            /* Items */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(new item('Items', 'BDT'));
            $printer->setEmphasis(false);

            foreach ($items as $item) {
                $printer->text($item);
            }
            $printer->feed();

            /* Sub Total to Total */
            $printer->text(new Item('Sub Total', $order->sub_total));
            $printer->text(new Item('Discount', $order->discount_amount));
            $printer->text(new Item('VAT', $order->vat_amount));
            $printer->text(new Item('Cash', $order->cash));
            $printer->text(new Item('Change', $order->change));

            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(new Item('Total', $order->total, true));
            $printer->selectPrintMode();
            $printer->feed();

            /* Footer */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("$pos_end_line\n");
            $printer->feed();
            $printer->text(now()->parse($order->date)->format('l jS \of F Y h:i A')."\n");

            // dd($printer);
            /* Cut the receipt and open the cash drawer */
            $printer->cut();
            $printer->pulse();

            $printer->close();
        } catch (Exception $e) {
            echo "Couldn't print to this printer: ".$e->getMessage()."\n";
        }
    }

    public function dummyPrint()
    {
        $printer_name = 'RONGTA 80mm Series Printer';
        // $printer_name = config("printer.name");
        // dd($printer_name);
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->text("Hello World!\n");
        $printer->cut();
        $printer->close();
    }
}

class Item
{
    /* A wrapper to do organize item names & prices into columns */

    public function __construct(private $name = '', private $price = '', private $currencySign = false)
    {
    }

    public function __toString()
    {
        $rightCols = 19;
        $leftCols = 29;
        if ($this->currencySign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->currencySign ? 'tk ' : '');
        $right = str_pad($sign.$this->price, $rightCols, ' ', STR_PAD_LEFT);

        return "$left$right\n";
    }
}

class ItemList
{
    public function __construct(private $sl = '*', private $name = 'Product Name', private $quantity = 0, private $rate = 0, private $total = 0)
    {
    }

    public function __toString()
    {
        $sl = $this->sl;
        $name = $this->name;
        $rate = (int) $this->rate < $this->rate ? $this->rate : (int) ($this->rate);
        $cost = "$this->quantity".' x '."$rate";
        $total = (int) $this->total < $this->total ? $this->total : (int) ($this->total);
        $total = str_pad("$total", 5, ' ', STR_PAD_LEFT);

        $rightCols = 19;
        $leftCols = 29;
        $left = str_pad("$sl. $name ", $leftCols);

        $right = str_pad(" $cost =$total", $rightCols, ' ', STR_PAD_LEFT);

        return "$left$right\n";
    }
}
