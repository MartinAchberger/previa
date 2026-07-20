<?php

namespace App\Orchid\Screens;

use App\Mail\B2bApprovedMail;
use App\Models\B2bUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class B2bUserEditScreen extends Screen
{
    public ?B2bUser $user = null;

    public function permission(): ?iterable { return ['platform.eshop.b2b']; }
    public bool $exists = false;

    public function query(B2bUser $b2b_user): array
    {
        $this->user = $b2b_user;
        $this->exists = $b2b_user->exists;
        return ['user' => $b2b_user];
    }

    public function name(): string
    {
        return $this->exists ? 'Salón: ' . $this->user->salon_name : 'Nový B2B salón';
    }

    public function commandBar(): array
    {
        $cmds = [Button::make('Uložiť')->method('save')->icon('bs.check')];
        if ($this->exists) {
            if ($this->user->status === 'pending') {
                $cmds[] = Button::make('Schváliť účet')->method('approve')->icon('bs.check-circle');
            }
            $cmds[] = Button::make('Zmazať')->method('remove')->icon('bs.trash')->confirm('Naozaj zmazať salón?');
        }
        return $cmds;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('user.salon_name')->title('Názov salónu')->required()->maxlength(200),
                Input::make('user.contact_name')->title('Kontaktná osoba')->required(),
                Input::make('user.email')->type('email')->title('E-mail')->required(),
                Input::make('user.phone')->title('Telefón'),
                Input::make('user.ico')->title('IČO')->maxlength(16),
                Input::make('user.vat_id')->title('IČ DPH')->maxlength(24),
                Input::make('user.address')->title('Ulica a číslo'),
                Input::make('user.city')->title('Mesto'),
                Input::make('user.zip')->title('PSČ')->maxlength(16),
                Input::make('user.country')->title('Krajina')->placeholder('SK')->maxlength(2)->value($this->user->country ?? 'SK'),
                Select::make('user.status')->title('Stav účtu')->options(B2bUser::STATUSES)->required(),
                Input::make('user.discount_pct')->type('number')->min(0)->max(50)->title('Zľava (%)')->help('0–50 %')->required(),
                TextArea::make('user.notes')->title('Interná poznámka')->rows(3),
                Input::make('new_password')->type('password')->title('Nastaviť nové heslo (voliteľné)')->help('Vyplň iba ak chceš resetovať heslo'),
            ]),
        ];
    }

    public function save(B2bUser $b2b_user, Request $request)
    {
        $request->validate([
            'user.salon_name'   => 'required|string|max:200',
            'user.contact_name' => 'required|string|max:150',
            'user.email'        => 'required|email|max:150|unique:b2b_users,email,' . ($b2b_user->id ?? 'NULL'),
            'user.status'       => 'required|in:' . implode(',', array_keys(B2bUser::STATUSES)),
            'user.discount_pct' => 'required|integer|min:0|max:50',
            'new_password'      => 'nullable|string|min:8',
        ]);

        $data = $request->input('user');
        $data['discount_pct'] = max(0, min(50, (int) ($data['discount_pct'] ?? 0)));

        // Never let raw password / approval-time be mass-assigned from the form —
        // password is handled explicitly below, approved_at only via approve().
        unset($data['password'], $data['approved_at']);

        $wasPending = $b2b_user->exists && $b2b_user->status === 'pending';

        $b2b_user->fill($data);

        if ($request->filled('new_password')) {
            $b2b_user->password = Hash::make($request->input('new_password'));
        }

        if (!$b2b_user->exists && empty($b2b_user->password)) {
            $b2b_user->password = Hash::make(\Illuminate\Support\Str::random(16));
        }

        // Approval via the status select counts as approval too.
        if ($wasPending && $b2b_user->status === 'active') {
            $b2b_user->approved_at = now();
        }

        $b2b_user->save();

        if ($wasPending && $b2b_user->status === 'active') {
            $this->sendApprovalEmail($b2b_user);
        }

        Toast::info('Uložené');
        return redirect()->route('platform.b2b-users');
    }

    public function approve(B2bUser $b2b_user)
    {
        $b2b_user->status = 'active';
        $b2b_user->approved_at = now();
        $b2b_user->save();

        $this->sendApprovalEmail($b2b_user);

        Toast::info('Salón schválený');
        return redirect()->route('platform.b2b-users');
    }

    private function sendApprovalEmail(B2bUser $user): void
    {
        try {
            Mail::to($user->email)->send(new B2bApprovedMail($user));
            Toast::info('Salónu bol odoslaný e-mail o aktivácii účtu.');
        } catch (\Throwable $e) {
            Log::error('B2B approval email failed', ['user' => $user->email, 'error' => $e->getMessage()]);
            Toast::error('E-mail o schválení sa nepodarilo odoslať.');
        }
    }

    public function remove(B2bUser $b2b_user)
    {
        $b2b_user->delete();
        Toast::info('Zmazané');
        return redirect()->route('platform.b2b-users');
    }
}
