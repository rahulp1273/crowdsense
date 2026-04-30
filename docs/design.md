# 📐 Detailed Design & Architecture

## 🚀 Core Philosophy
CrowdSense is designed as a **Service-Oriented SaaS** platform. The primary goal is to provide high-accuracy, real-time crowd data while maintaining a premium user experience and strict location-based integrity.

---

## 🏗️ Technical Stack

### **Frontend (The Experience Layer)**
- **Framework:** Vue 3 (Composition API)
- **State Management:** Pinia (Modular stores: `auth`, `user`, `admin`)
- **UI System:** Tailwind CSS with a custom Glassmorphic design language.
- **GPS Integration:** Native Browser Geolocation API integrated with Pinia.
- **Real-Time:** Pusher-compatible WebSocket client for live crowd badges.

### **Backend (The Intelligence Layer)**
- **Framework:** Laravel 11
- **Architecture:** Service-Oriented Architecture (SOA)
- **Business Logic:** Encapsulated in Services (`PlaceService`, `CheckInService`, `InquiryService`, `LocationService`).
- **Broadcasting:** Laravel Echo + BeyondCode WebSockets for local real-time updates.
- **Security:** Laravel Sanctum for API token management.

---

## 📍 GPS & Proximity Logic
One of the core features of CrowdSense is **GPS-Verified Check-ins**.

1. **Detection:** Frontend detects `lat/lng` on dashboard mount.
2. **Proximity:** Backend uses the **Haversine Formula** in `LocationService` to calculate the distance between the user and the venue.
3. **Validation:** Check-in is only permitted if `distance <= venue_radius`.
4. **Decay:** A scheduled task runs hourly to decrement crowd counts of stale sessions, ensuring data accuracy.

---

## 📩 Inquiry & Support Flow
The platform uses a structured **Inquiry System** instead of legacy chat to maintain professional communication.

1. **Submission:** Users submit categorized inquiries (General/Bug/Support) linked to a specific place.
2. **Persistence:** Inquiries are stored in the `inquiries` table with a `new` status.
3. **Admin Alert:** The Admin Dashboard polls every 10 seconds for `unread_inquiries`.
4. **Resolution:** Admins can mark inquiries as `seen` to clear their queue.

---

## 🗄️ Database Schema Design

### **1. `places`**
Stores the source of truth for locations.
- `name`, `type`, `location`
- `lat`, `lng`: Proximity centers.
- `radius`: Proximity boundary (meters).
- `current_crowd_count`: Atomic counter for live visits.

### **2. `check_ins`**
Tracks the lifecycle of a venue visit.
- `user_id`, `place_id`
- `is_active`: Boolean flag for current sessions.
- `expires_at`: Auto-expiry timestamp.
- `weight_applied`: Impact factor of the specific user.

### **3. `inquiries`**
Tracks user feedback and support tickets.
- `user_id`, `place_id`, `message`
- `type`: Category of inquiry.
- `status`: Lifecycle state (`new`, `seen`).

---

## 🎨 Design System
- **Glassmorphism:** Using `backdrop-blur` and semi-transparent backgrounds for a premium feel.
- **3D Depth:** Using heavy shadows (`shadow-2xl`) and scale transforms (`hover:scale-105`) to create hierarchy.
- **Color Coded Status:**
    - 🟢 **Low:** 0-10 people
    - 🟡 **Medium:** 11-30 people
    - 🔴 **High:** 31+ people
