# CrowdSense Overview

## What is CrowdSense?
CrowdSense is a real-time crowd visibility platform designed to let users check how crowded a specific location (such as a gym, cafe, or mall) is *before* they visit. The goal is to help people save time, avoid long lines, and make informed decisions on where to go.

## Core Capabilities

### 1. Real-Time Crowd Indicators
Places are displayed with visual crowd statuses so users can understand the situation at a glance:
- 🟢 **Low Crowd (< 20 people):** Perfect time to visit.
- 🟡 **Medium Crowd (20 - 49 people):** Getting busy, minor wait times possible.
- 🔴 **High Crowd (50+ people):** Very busy, recommend visiting later.

### 2. Secure OTP Authentication
- The registration flow is protected with a 6-digit OTP verification system.
- Users must verify their email with the code before being granted an access token, preventing spam accounts.

### 3. User Engagement (Check-ins / Check-outs)
- Users can actively check into a place when they arrive, which instantly increments the crowd counter.
- Users can check out when they leave, decreasing the crowd counter.

### 3. Automated Crowd Decay
- To prevent artificial inflation of crowd numbers (e.g., users forgetting to check out), a scheduled backend task runs hourly to slowly decrement the crowd count, simulating natural crowd dissipation based on average stay times.

### 4. Admin Management
- Admins have elevated privileges to curate the platform.
- Admins can **Create**, **Update**, and **Delete** places dynamically through the dashboard.
- Regular users only consume the data and participate in check-ins.

### 5. Seamless Cross-Platform Foundation
- The backend is structured to serve as a robust RESTful API, making it fully ready to support a mobile app (like an iOS/Android Capacitor app) in the future without any backend restructuring.
