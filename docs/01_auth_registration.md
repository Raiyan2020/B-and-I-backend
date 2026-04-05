# Feature 01 — Authentication & Registration (Two User Types)

## Business Context

The platform has two registerable roles:
- **Investor** — browses projects, purchases booklets, registers interest
- **Advertiser** — posts business listings, receives investor interest via admin

Admin accounts are created manually, never via public registration.
Authentication uses **email + phone** (no password login) per the frontend spec.

---

## Database

### Table: `users`

Single unified table with role discriminator.

| column               | type                                      | nullable | notes                               |
|----------------------|-------------------------------------------|----------|-------------------------------------|
| id                   | bigIncrements PK                          | NO       |                                     |
| role                 | enum(investor, advertiser, admin)         | NO       |                                     |
| first_name           | string                                    | NO       |                                     |
| last_name            | string                                    | NO       |                                     |
| email                | string unique                             | NO       |                                     |
| phone                | string(8)                                 | NO       | Kuwait format, digits only          |
| password             | string (hashed)                           | NO       |                                     |
| email_verified_at    | timestamp                                 | YES      | null = not verified                 |
| subscription_plan    | enum(basic, premium, vip)                 | YES      |                                     |
| bio                  | text                                      | YES      |                                     |
| tagline              | string                                    | YES      |                                     |
| investor_type        | enum(angel, company, crowdfunding)        | YES      | required if role = investor         |
| investor_sector      | string                                    | YES      | required if role = investor         |
| investor_capital     | decimal(12,3)                             | YES      | required if role = investor         |
| investment_count     | unsignedInteger                           | YES      | required if role = investor         |
| investor_experience  | enum(beginner, intermediate, expert)      | YES      | required if role = investor         |
| company_name         | string                                    | YES      | required if role = advertiser       |
| company_license_url  | string                                    | YES      | required if role = advertiser       |
| license_number       | string                                    | YES      | required if role = advertiser       |
| created_at           | timestamp                                 | NO       |                                     |
| updated_at           | timestamp                                 | NO       |                                     |

---

## API Endpoints

All routes prefixed: `POST /api/v1/auth`

| Method | Route                    | Auth Required | Description                   |
|--------|--------------------------|---------------|-------------------------------|
| POST   | /register/investor       | No            | Register new investor         |
| POST   | /register/advertiser     | No            | Register new advertiser       |
| POST   | /login                   | No            | Unified login for both roles  |
| POST   | /logout                  | Yes           | Revoke current token          |
| GET    | /me                      | Yes           | Get authenticated user        |
| POST   | /email/verify/{id}/{hash}| No (signed)   | Verify email address          |
| POST   | /email/resend            | Yes           | Resend verification email     |

---

## Business Logic per Endpoint

### POST /register/investor

**Request (JSON):**
```
first_name           string   required
last_name            string   required
email                string   required | email | unique:users
phone                string   required | regex:/^\d{8}$/
password             string   required | min:8
password_confirmation string  required | same:password
investor_type        enum     required | in:angel,company,crowdfunding
investor_sector      string   required
investor_capital     numeric  required | min:1000
investment_count     integer  required | min:0
investor_experience  enum     required | in:beginner,intermediate,expert
agreed_to_terms      boolean  required | accepted
```

**Logic:**
1. Validate all fields via FormRequest
2. Hash password with bcrypt
3. Insert user row with `role = investor`
4. Dispatch email verification via Laravel MustVerifyEmail
5. Generate Sanctum token with name `investor`
6. Return 201 with user + token

---

### POST /register/advertiser

**Request (multipart/form-data — file upload required):**
```
first_name           string   required
last_name            string   required
email                string   required | email | unique:users
phone                string   required | regex:/^\d{8}$/
password             string   required | min:8
password_confirmation string  required | same:password
company_name         string   required
license_number       string   required
company_license      file     required | mimes:jpg,jpeg,png,pdf | max:10240
agreed_to_terms      boolean  required | accepted
```

**Logic:**
1. Validate all fields including file type (jpg/png/pdf) and size (max 10MB)
2. Store license file at `storage/app/public/licenses/{uuid}.{ext}`
   - Use UUID for filename — never store original filename (security)
   - Save public URL in `company_license_url`
3. Hash password
4. Insert user row with `role = advertiser`
5. Dispatch email verification
6. Generate Sanctum token with name `advertiser`
7. Return 201 with user + token

---

### POST /login

**Request (JSON):**
```
email   string   required | email
phone   string   required | regex:/^\d{8}$/
```

> No password. The platform authenticates via email + phone combination.

**Logic:**
1. Find user by email — if not found → 401
2. Verify phone matches stored phone — if mismatch → 401
3. Use **same generic error message** for both cases (never reveal which field failed)
4. Delete all existing tokens for this user (enforce single session)
5. Generate new Sanctum token named after role
6. Return 200 with user + token

**Error response 401:**
```json
{ "success": false, "message": "Invalid credentials.", "errors": null }
```

---

### POST /logout

**Logic:**
- Delete current access token only: `$request->user()->currentAccessToken()->delete()`
- Return 200

---

### GET /me

**Logic:**
- Return authenticated user via `UserResource`
- Resource must conditionally include fields by role:
  - Investor: include all `investor_*` fields, exclude `company_*` fields
  - Advertiser: include all `company_*` fields, exclude `investor_*` fields

---

### POST /email/resend

**Logic:**
1. If `email_verified_at` is not null → return 400 "Already verified"
2. Throttle: max 2 requests per 5 minutes per user (use Laravel rate limiter)
3. Resend via `$user->sendEmailVerificationNotification()`
4. Return 200

---

## Architecture Pattern

```
HTTP Request
    → FormRequest (validation only)
    → Controller (build DTO, call service, return response)
    → DTO (typed data container, fromRequest() factory)
    → Service (all business logic: hash, store file, insert, token)
    → UserResource (role-aware response shaping)
    → ResponseTrait (unified envelope)
```

### DTOs

```
app/DTO/Auth/RegisterInvestorDTO.php
app/DTO/Auth/RegisterAdvertiserDTO.php
app/DTO/Auth/LoginDTO.php
```

Each DTO is `readonly` with a static `fromRequest(array $validated): self` factory.
`RegisterAdvertiserDTO` accepts `UploadedFile $licenseFile` as a constructor parameter.

### Service Interface

```php
interface AuthServiceInterface
{
    public function registerInvestor(RegisterInvestorDTO $dto): array;
    // returns ['user' => User, 'token' => string]

    public function registerAdvertiser(RegisterAdvertiserDTO $dto): array;
    // returns ['user' => User, 'token' => string]

    public function login(LoginDTO $dto): array;
    // throws AuthenticationException on invalid credentials

    public function logout(User $user): void;

    public function resendVerification(User $user): void;
    // throws if already verified or rate-limited
}
```

---

## Response Envelope (via `App\Traits\ResponseTrait`)

**Success 201:**
```json
{
  "success": true,
  "message": "Account created. Please verify your email.",
  "data": {
    "token": "1|abc...",
    "user": {
      "id": 1,
      "role": "investor",
      "first_name": "Ahmed",
      "last_name": "Al-Shammari",
      "email": "ahmed@example.com",
      "phone": "80808080",
      "email_verified": false,
      "investor_type": "angel",
      "investor_sector": "Technology",
      "investor_capital": 100000,
      "investment_count": 3,
      "investor_experience": "intermediate",
      "subscription_plan": null
    }
  }
}
```

**Validation Error 422:**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "phone": ["The phone must be 8 digits."]
  }
}
```

---

## File Tree

```
app/
├── DTO/Auth/
│   ├── RegisterInvestorDTO.php
│   ├── RegisterAdvertiserDTO.php
│   └── LoginDTO.php
├── Services/Auth/
│   ├── AuthServiceInterface.php
│   └── AuthService.php
├── Http/
│   ├── Controllers/Api/V1/Auth/
│   │   ├── RegisterInvestorController.php
│   │   ├── RegisterAdvertiserController.php
│   │   ├── LoginController.php
│   │   ├── LogoutController.php
│   │   ├── MeController.php
│   │   └── EmailVerificationController.php
│   ├── Requests/
│   │   ├── RegisterInvestorRequest.php
│   │   ├── RegisterAdvertiserRequest.php
│   │   └── LoginRequest.php
│   ├── Resources/
│   │   └── UserResource.php
│   └── Middleware/
│       ├── EnsureUserIsInvestor.php
│       └── EnsureUserIsAdvertiser.php
```
