# Facility Bookings

Residents can reserve community facilities — swimming pools, gyms, meeting rooms, BBQ areas — through the booking system.

## Booking Workflow

```
Request Submitted → Pending Approval → Booked → Scheduled → Completed
                                     → Rejected
                 → (auto-approved if configured) → Booked
Booked → Canceled (by resident or admin)
```

## Making a Booking

1. Navigate to **Dashboard → Bookings** or find the facility in the **Directory**
2. Click **Book** on the facility you want to reserve
3. Fill in:

| Field | Description |
|-------|-------------|
| **Date** | The date you want to book |
| **Start Time** | Booking start time |
| **End Time** | Booking end time |
| **Number of Guests** | Total attendees |
| **Purpose** | Brief description of the event/use |

4. Click **Submit Booking**

If the facility requires approval, your booking shows as **Pending**. If auto-approved, you'll receive an immediate confirmation.

## Booking Status

| Status | Meaning |
|--------|---------|
| **Pending Approval** | Awaiting manager review |
| **Booked** | Confirmed reservation |
| **Scheduled** | Upcoming confirmed booking |
| **Completed** | Booking period has passed |
| **Canceled** | Booking was canceled |
| **Rejected** | Booking was declined by a manager |

## Canceling a Booking

Open your booking and click **Cancel**. Provide a reason and confirm. Cancellations may be subject to time restrictions configured by your administrator.

## Approving / Rejecting Bookings (Managers)

1. Navigate to **Dashboard → Bookings** or **Requests → Facility Bookings**
2. Open a **Pending** booking request
3. Review the details
4. Click **Approve** or **Reject**
5. Add a note if rejecting

The resident receives a notification of the decision.

## Booking Contracts

Some facilities require a signed booking contract before confirmation. The system will prompt you to sign the contract as part of the approval process.

## Viewing All Bookings

Navigate to **Dashboard → Bookings** to see the full calendar view of all facility bookings. You can filter by:
- Facility
- Date range
- Status
