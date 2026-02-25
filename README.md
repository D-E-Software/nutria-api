# nutria-api (Backend)

Laravel backend for Nutria, a nutrition / clinic SaaS.
Provides APIs for clinic setup, programs, orders, and email-related flows.

# Tech Stack
•	PHP (8.4)
•	Laravel
•	MySQL
•	Composer


# API Areas

### Admin API (/api/admin/...)

Used by clinic/admin dashboard.

### Controllers visible:
•	AuthController
    Login / auth-related endpoints.
•	ClinicSettingsController
    Manage clinic-level settings.
•	ProgramController
Create/manage programs.
•	OrderController
Admin-side order management.
•	EmailController
Email templates/logging/sending (depending on implementation).

Public API (/api/public/...)

Used by public-facing flows (landing pages, checkout, limited program listing, etc.)

Controllers visible:
•	ProgramController
•	OrderController
•	store(Request $request, string $clinicSlug)
•	paymentCallback(Request $request, Order $order) (bank callback / webhook style)
