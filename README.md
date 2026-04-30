<<<<<<< HEAD
# crowdsense
=======
# CrowdSense 🚀
**The Intelligent Real-Time Crowd Visibility Platform**

CrowdSense is a production-ready SaaS platform that provides real-time visibility into venue occupancy. Built with a "Privacy-First, GPS-Verified" philosophy, it helps users make informed decisions about when to visit their favorite spots.

---

## ✨ Key Features

### 📍 GPS-Verified Check-Ins
- **Proximity Validation**: Uses the Haversine formula to ensure users are physically present before checking in.
- **Session Intelligence**: Reactive timers track visit duration and automatically handle expiration.
- **Impact Weighting**: User "weights" influence crowd counts for high-accuracy visibility.

### 📩 Structured Inquiry System
- **Legacy Chat Replacement**: Replaced unstructured chat with a professional ticket-based inquiry system.
- **Categorized Requests**: Support for 'General', 'Issue', and 'Support' inquiries.
- **Admin Management**: Centralized dashboard for admins to manage and resolve user tickets.

### 📊 Admin Intelligence OS
- **Live Monitoring**: Real-time dashboard showing top active venues and live crowd counts.
- **Analytics at a Glance**: Track total places, unread inquiries, and active check-ins.
- **Venue Management**: Full CRUD capabilities for managing venue GPS radii and capacities.

### 🎨 Premium SaaS UI/UX
- **Glassmorphic Design**: Modern 3D interface built with Tailwind CSS.
- **Responsive Layout**: Seamless experience across mobile, tablet, and desktop.
- **Dark Mode Support**: Fully integrated dark theme for all components.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (Service-Oriented Architecture)
- **Frontend**: Vue 3 (Vite, Pinia, Composition API)
- **Real-time**: Laravel WebSockets (Pusher Protocol)
- **Styling**: Tailwind CSS (Vanilla CSS Design Tokens)
- **Database**: MySQL / PostgreSQL / SQLite

---

## 🚀 Getting Started

### 1. Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 2. Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

### 3. WebSocket Server (Optional for Live Updates)
```bash
php artisan websockets:serve
```

---

## 🔑 Demo Credentials

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `password` |
| **User** | `user@example.com` | `password` |

---

## 📂 Documentation Links
- [System Architecture & API](file:///Users/rahulpanwar/.gemini/antigravity/brain/4ad6c25b-a4a1-4866-831f-587ba7b6b5af/System_Architecture.md)
- [Design Decisions](file:///Users/rahulpanwar/Desktop/crowdsense/docs/design.md)
- [Implementation Report](file:///Users/rahulpanwar/.gemini/antigravity/brain/4ad6c25b-a4a1-4866-831f-587ba7b6b5af/SaaS_Implementation_Report.md)
>>>>>>> a2a62f3 (first commit for crowdsense)
