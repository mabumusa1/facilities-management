<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacilityBookingController extends Controller
{
    public function index(Request $request): Response
    {
        $bookings = FacilityBooking::query()
            ->with(['facility', 'booker', 'status'])
            ->latest()
            ->paginate(15);

        return Inertia::render('facilities/bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('facilities/bookings/Create', [
            'facilities' => Facility::where('is_active', true)->select('id', 'name', 'name_en')->orderBy('name')->get(),
            'residents' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'statuses' => Status::where('type', 'facility_booking')->select('id', 'name', 'name_en')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'integer', 'exists:rf_facilities,id'],
            'booker_id' => ['required', 'integer'],
            'booker_type' => ['required', 'string'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'number_of_guests' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $booking = FacilityBooking::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Booking created.')]);

        return to_route('facility-bookings.show', $booking);
    }

    public function show(FacilityBooking $facilityBooking): Response
    {
        $facilityBooking->load(['facility', 'booker', 'status']);

        return Inertia::render('facilities/bookings/Show', [
            'facilityBooking' => $facilityBooking,
        ]);
    }

    public function edit(FacilityBooking $facilityBooking): Response
    {
        $facilityBooking->load(['facility', 'booker', 'status']);

        return Inertia::render('facilities/bookings/Edit', [
            'facilityBooking' => $facilityBooking,
            'facilities' => Facility::where('is_active', true)->select('id', 'name', 'name_en')->orderBy('name')->get(),
            'residents' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'statuses' => Status::where('type', 'facility_booking')->select('id', 'name', 'name_en')->get(),
        ]);
    }

    public function update(Request $request, FacilityBooking $facilityBooking): RedirectResponse
    {
        $validated = $request->validate([
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'booking_date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'string'],
            'end_time' => ['sometimes', 'string'],
            'number_of_guests' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $facilityBooking->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Booking updated.')]);

        return to_route('facility-bookings.show', $facilityBooking);
    }

    public function destroy(FacilityBooking $facilityBooking): RedirectResponse
    {
        $facilityBooking->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Booking cancelled.')]);

        return to_route('facility-bookings.index');
    }
}
