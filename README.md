# symfony-cinema
![build status](https://github.com/mkniazuk123/symfony-library/actions/workflows/workflow.yml/badge.svg)

:construction: In development

## Assumptions
### Administrator Roles:
* **Movie Catalog Management:** Curate and manage the list of available movies.
* **Cinema Hall & Seating Management:** Configure cinema halls and their respective seating layouts.
* **Screening Scheduling:** Plan and organize movie showtimes.
* **Ticket Sales:** Sell tickets based on existing reservations or through direct walk-in purchases.

### User Roles:
* **Browse Repertory:** View the current cinema schedule and movie list.
* **Check Availability:** View real-time seating charts for specific screenings.
* **Seat Reservation:** Book seats for selected showtimes.
* **Reservation Cancellation:** Cancel existing bookings.

### Key Business Rules:
* **Reservation Expiry:** Reservations are valid until **15 minutes before** the screening starts. Users must exchange their reservation for a ticket within this timeframe.
* **No Single-Seat Gaps:** To optimize room capacity, users are not allowed to leave a single empty seat between occupied ones when making a reservation.
* **Advance Scheduling:** Screenings cannot be scheduled less than **24 hours** in advance.
* **Turnaround Time:** There must be a minimum break of **15 minutes** between consecutive screenings in the same hall.

## Approach
Modular monolith architecture with DDD principles. REST API interface.

Modules:
- `Catalog` - movies that can be displayed
- `Facilities` - halls and their seating plans
- `Screenings` - screenings schedule
- `Booking` - reservations management

## Usage
Start development application: 
```bash
docker compose -f docker/compose.dev.yaml up -d
``` 
The application will be available at `http://localhost:60149`.

Run PHPUnit tests:
```bash
docker compose -f docker/compose.test.yaml run tests bin/phpunit
```

Run Behat tests:
```bash
docker compose -f docker/compose.test.yaml run tests vendor/bin/behat
```
