<?php

namespace Tests\Feature;

use App\Models\B2bUser;
use App\Models\Order;
use App\Services\SuperFaktura\SuperFakturaService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class B2bInvoiceDownloadTest extends TestCase
{
    use DatabaseTransactions;

    private function salon(): B2bUser
    {
        return B2bUser::forceCreate(['email' => 'dl-' . uniqid() . '@example.com', 'password' => bcrypt('x'), 'contact_name' => 'T', 'salon_name' => 'Salón DL', 'status' => 'active']);
    }

    private function order(B2bUser $b2b, array $extra = []): Order
    {
        $id = DB::table('orders')->insertGetId(array_merge([
            'order_number' => 'TS-26-' . random_int(10000, 99999), 'b2b_user_id' => $b2b->id, 'customer_name' => 'T',
            'customer_email' => 'x@example.com', 'customer_phone' => '0', 'shipping_address' => 'x', 'shipping_city' => 'x',
            'shipping_zip' => '0', 'subtotal' => 10, 'total' => 10, 'payment_method' => 'transfer', 'payment_status' => 'unpaid',
            'status' => 'confirmed', 'created_at' => now(), 'updated_at' => now(),
        ], $extra));
        return Order::find($id);
    }

    public function test_salon_downloads_its_invoice_from_history_and_detail(): void
    {
        Storage::fake(SuperFakturaService::PDF_DISK);
        Storage::disk(SuperFakturaService::PDF_DISK)->put('invoices/test.pdf', '%PDF-1.4 test');
        $b2b = $this->salon();
        $order = $this->order($b2b, ['invoice_sf_id' => 1, 'invoice_number' => 'FA2026/0042', 'invoice_pdf_path' => 'invoices/test.pdf', 'invoice_status' => 'issued']);
        $noInvoice = $this->order($b2b);
        $this->actingAs($b2b, 'b2b');

        $this->get(route('b2b.orders'))->assertOk()
            ->assertSee(route('b2b.order.invoice', $order->order_number))
            ->assertDontSee(route('b2b.order.invoice', $noInvoice->order_number));
        $this->get(route('b2b.order.detail', $order->order_number))->assertOk()->assertSee('Stiahnuť faktúru FA2026/0042');
        $this->get(route('b2b.order.detail', $noInvoice->order_number))->assertOk()->assertSee('Faktúra zatiaľ nie je vystavená');

        $res = $this->get(route('b2b.order.invoice', $order->order_number))->assertOk();
        $this->assertStringContainsString('Faktura-FA2026-0042.pdf', $res->headers->get('content-disposition'));
        $this->get(route('b2b.order.invoice', $noInvoice->order_number))->assertNotFound();
    }

    public function test_other_salon_and_cancelled_invoice_get_404(): void
    {
        Storage::fake(SuperFakturaService::PDF_DISK);
        Storage::disk(SuperFakturaService::PDF_DISK)->put('invoices/a.pdf', '%PDF');
        $owner = $this->salon();
        $order = $this->order($owner, ['invoice_sf_id' => 1, 'invoice_pdf_path' => 'invoices/a.pdf', 'invoice_status' => 'issued']);
        $cancelled = $this->order($owner, ['invoice_sf_id' => 2, 'invoice_pdf_path' => 'invoices/a.pdf', 'invoice_status' => 'cancelled']);

        $this->actingAs($this->salon(), 'b2b')->get(route('b2b.order.invoice', $order->order_number))->assertNotFound();
        $this->actingAs($owner, 'b2b')->get(route('b2b.order.invoice', $cancelled->order_number))->assertNotFound();
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/b2b/objednavky/TS-26-1/faktura')->assertRedirect();
    }
}
