<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Display a listing of the contacts with tabs for each type.
     */
    public function index(Request $request): Response
    {
        $contactType = $request->get('type', 'all');
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Contact::query()
            ->with(['tenant'])
            ->forTenant(auth()->user()->tenant_id);

        // Filter by contact type
        if ($contactType !== 'all') {
            $query->where('contact_type', $contactType);
        }

        // Apply search
        if ($search) {
            $query->search($search);
        }

        // Apply status filter
        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'inactive') {
            $query->inactive();
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('contacts/index', [
            'contacts' => $contacts,
            'filters' => [
                'type' => $contactType,
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create(Request $request): Response
    {
        $contactType = $request->get('type', 'owner');

        return Inertia::render('contacts/create', [
            'contactType' => $contactType,
        ]);
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(StoreContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['active'] = $validated['active'] ?? true;
        if (($validated['contact_type'] ?? null) !== 'admin') {
            $validated['role'] = null;
        }

        $contact = Contact::create($validated);

        return redirect()->route('contacts.show', ['contact' => $contact->id])
            ->with('success', 'Contact created successfully.');
    }

    /**
     * Display the specified contact.
     */
    public function show(Contact $contact): Response
    {
        $contact->load([
            'tenant',
            'units',
            'leases',
            'activeRequests',
            'transactions',
        ]);

        return Inertia::render('contacts/show', [
            'contact' => $contact,
        ]);
    }

    /**
     * Show the form for editing the specified contact.
     */
    public function edit(Contact $contact): Response
    {
        return Inertia::render('contacts/edit', [
            'contact' => $contact,
        ]);
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validated();

        $resolvedContactType = $validated['contact_type'] ?? $contact->contact_type;
        if ($resolvedContactType !== 'admin') {
            $validated['role'] = null;
        }

        $contact->update($validated);

        return redirect()->route('contacts.show', ['contact' => $contact->id])
            ->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }
}
