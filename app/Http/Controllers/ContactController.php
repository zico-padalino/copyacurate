<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $contacts = Contact::query()->withCount(['salesInvoices', 'purchaseBills'])->latest('id')->get();
        $stats = [
            'total' => $contacts->count(),
            'customers' => $contacts->whereIn('type', ['customer', 'both'])->count(),
            'vendors' => $contacts->whereIn('type', ['vendor', 'both'])->count(),
            'invoices' => (int) $contacts->sum('sales_invoices_count'),
        ];

        return view('contacts.index', compact('user', 'contacts', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('contacts'), 403);

        $data = $request->validate([
            'type' => ['required', 'in:customer,vendor,both'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'tax_number' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        Contact::query()->create($data);

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('contacts'), 403);

        $data = $request->validate([
            'type' => ['required', 'in:customer,vendor,both'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'tax_number' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $contact->update($data);

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diperbarui.');
    }
}
