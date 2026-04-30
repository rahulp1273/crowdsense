# API Documentation

Base URL: `http://localhost:8000/api`

All protected endpoints require an Authorization header:  
`Authorization: Bearer <your_token>`

---

## 🔐 Authentication

### 1. Register User
- **Endpoint:** `POST /register`
- **Access:** Public
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secretpassword"
  }
  ```
- **Response (200):** Returns a message that an OTP was sent. Does **not** return a token yet.

### 2. Verify OTP
- **Endpoint:** `POST /verify-otp`
- **Access:** Public
- **Body:**
  ```json
  {
    "email": "john@example.com",
    "otp": "123456"
  }
  ```
- **Response (200):** Validates the 6-digit OTP, marks the user as verified, and returns the user object and authentication token.

### 3. Login
- **Endpoint:** `POST /login`
- **Access:** Public
- **Body:**
  ```json
  {
    "email": "john@example.com",
    "password": "secretpassword"
  }
  ```
- **Response (200):** Returns user object and token.

### 4. Logout
- **Endpoint:** `POST /logout`
- **Access:** Protected
- **Response (200):** Invalidates the current user token.

### 5. Get Current User
- **Endpoint:** `GET /user`
- **Access:** Protected
- **Response (200):** Returns the currently authenticated user's profile.

---

## 📍 Places

### 1. Get All Places
- **Endpoint:** `GET /places`
- **Access:** Public
- **Response (200):** Array of place objects, including current crowd counts.

### 2. Create a Place
- **Endpoint:** `POST /places`
- **Access:** Protected (Admin Only)
- **Body:**
  ```json
  {
    "name": "Downtown Cafe",
    "type": "cafe",
    "location": "Main Street"
  }
  ```
- **Response (201):** Returns the newly created place object.

### 3. Delete a Place
- **Endpoint:** `DELETE /places/{id}`
- **Access:** Protected (Admin Only)
- **Response (200):** Success message.

---

## 📊 Crowd Logic

### 1. Check In
- **Endpoint:** `POST /checkin`
- **Access:** Protected
- **Body:**
  ```json
  {
    "place_id": 1
  }
  ```
- **Response (200):** Increments crowd count and returns updated count.

### 2. Check Out
- **Endpoint:** `POST /checkout`
- **Access:** Protected
- **Body:**
  ```json
  {
    "place_id": 1
  }
  ```
- **Response (200):** Decrements crowd count and returns updated count.

### 3. Get Crowd Status for a Specific Place
- **Endpoint:** `GET /places/{id}/crowd`
- **Access:** Public
- **Response (200):** Returns only the `current_crowd_count` integer.
