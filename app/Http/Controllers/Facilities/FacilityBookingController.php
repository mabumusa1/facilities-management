<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Models\FacilityBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacilityBookingController extends Controller
{
    public function index(Request $request): Response
    {
        $bookings = FacilityBooking::query()
            ->with(['facility', 'resident', 'status'])
            ->latest()
            ->paginate(15);

        return Inertia::render('facilities/bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function show(FacilityBooking $booking): Response
    {
        $booking->load(['facility', 'resident', 'status']);

        return Inertia::render('facilities/bookings/Show', [
            'booking' => $booking,
        ]);
    }

    public function update(Request $request, FacilityBooking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
        ]);

        $booking->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Booking updated.')]);

        return to_route('facility-bookings.show', $booking);
    }

    public function destroy(FacilityBooking $booking): RedirectResponse
    {
        $booking->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Booking cancelled.')]);

        return to_route('facility-bookings.index');
    }
}
