<?php

namespace App\Http\Controllers\B2b;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DashboardController extends Controller
{
    public function index(): View
    {
        $b2b = Auth::guard('b2b')->user();

        $orders = $b2b->orders()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $stats = [
            'orders_total' => $b2b->orders()->count(),
            'orders_pending' => $b2b->orders()->whereIn('status', ['new', 'confirmed'])->count(),
            'spend_total' => $b2b->orders()->where('status', '!=', 'cancelled')->sum('total'),
        ];

        return view('pages.b2b.dashboard', compact('b2b', 'orders', 'stats'));
    }

    public function orders(): View
    {
        $b2b = Auth::guard('b2b')->user();
        $orders = $b2b->orders()->orderByDesc('created_at')->paginate(20);
        return view('pages.b2b.orders', compact('b2b', 'orders'));
    }

    public function orderDetail(string $orderNumber): View
    {
        $b2b = Auth::guard('b2b')->user();
        $order = Order::with('items')->where('order_number', $orderNumber)->where('b2b_user_id', $b2b->id)->first();
        if (!$order) {
            throw new NotFoundHttpException();
        }
        return view('pages.b2b.order-detail', compact('order', 'b2b'));
    }

    public function profile(): View
    {
        $b2b = Auth::guard('b2b')->user();
        return view('pages.b2b.profile', compact('b2b'));
    }

    public function profileUpdate(Request $request): RedirectResponse
    {
        $b2b = Auth::guard('b2b')->user();
        $data = $request->validate([
            'contact_name' => 'required|string|max:150',
            'salon_name'   => 'required|string|max:200',
            'phone'        => 'nullable|string|max:40',
            'ico'          => 'nullable|string|max:16',
            'vat_id'       => ['nullable', 'string', 'max:24', 'regex:/^[A-Za-z]{2}[0-9A-Za-z]{8,12}$/'],
            'address'      => 'nullable|string|max:200',
            'city'         => 'nullable|string|max:100',
            'zip'          => 'nullable|string|max:16',
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($data['password'])) {
            $b2b->password = Hash::make($data['password']);
        }
        unset($data['password']);

        $b2b->fill($data)->save();

        return redirect()->route('b2b.profile')->with('success', 'Profil uložený.');
    }
}
