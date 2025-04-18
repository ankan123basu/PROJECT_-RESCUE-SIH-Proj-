# RESCUE - Emergency Response Tracking System
![Screenshot 2025-04-13 173018](https://github.com/user-attachments/assets/21215697-2e2b-4aa2-bba4-02362d97ba71)
![Screenshot 2025-04-13 175508](https://github.com/user-attachments/assets/df9df6e8-c27f-4ef1-91cf-cf206ef36569)
![Screenshot 2025-04-13 173036](https://github.com/user-attachments/assets/514096a9-1f90-4789-8e0b-fbda2a57ddff)
![Screenshot 2025-04-13 173448](https://github.com/user-attachments/assets/a55c8745-2293-483d-ba4b-94dfd743d329)
![Screenshot 2025-04-13 174734](https://github.com/user-attachments/assets/3e68acc2-f19f-4d97-bd2d-a3ce1c5caee5)
![Screenshot 2025-04-13 174841](https://github.com/user-attachments/assets/c973dff3-5cc7-4ce7-918c-6f5e3e3a0cb0)
![Screenshot 2025-04-13 174442](https://github.com/user-attachments/assets/2befd33d-f269-486b-9401-52297f395703)
![Screenshot 2025-04-13 174750](https://github.com/user-attachments/assets/8a1790b3-21f0-4c4c-b210-52b97de83963)
![Screenshot 2025-04-13 174503](https://github.com/user-attachments/assets/ed2806d8-0959-4c95-9054-d59660f5ccb7)
![Screenshot 2025-04-13 174812](https://github.com/user-attachments/assets/ca2cf313-4828-41fd-bb10-12e5abe14209)
![Screenshot 2025-04-13 174418](https://github.com/user-attachments/assets/f69044be-dd19-428a-8dfc-e392d9f18115)
![Screenshot 2025-04-13 174801](https://github.com/user-attachments/assets/45a4c767-2bdb-4b1a-b26d-4f9ae6af0fe7)
![Screenshot 2025-04-13 173059](https://github.com/user-attachments/assets/4467db43-e173-4bc8-a25a-7db3a0b9ca65)
![Screenshot 2025-04-13 173120](https://github.com/user-attachments/assets/785efe8a-19df-44a4-8c6f-9c41699a44c9)

A comprehensive web-based platform for tracking and managing emergency resources across multiple Indian cities. The system provides real-time tracking of emergency vehicles, interactive map interfaces, incident reporting, and comprehensive resource management.

## 🚨 Key Features

### Real-time Tracking & Visualization
- **Multi-vehicle Tracking**: Monitor ambulances, fire trucks, and helicopters in real-time
- **Interactive Maps**: Dynamic markers with filtering capabilities by type, status, and location
- **3D & Satellite Views**: Multiple map visualization options
- **City Coverage**: Full tracking across 14 major Indian cities

### Incident Management
- **Incident Reporting**: Comprehensive incident creation and reporting system
- **Email Notifications**: Automated notifications for incident reports and responses
- **Response Coordination**: Assign and track emergency resources to incidents
- **Real-time Status Updates**: Live tracking of incident status and resource deployment

### Resource Management
- **Resource Inventory**: Complete inventory of all emergency vehicles and assets
- **Status Monitoring**: Real-time status tracking (available, responding, busy)
- **Filtering System**: Advanced filtering by resource type, status, and location
- **Resource Dispatch**: Streamlined process for dispatching resources to incidents

### User Interface
- **Glass Morphism Design**: Modern, visually appealing interface with glass effects
- **Responsive Framework**: Fully responsive design that works on all device sizes
- **Dark Theme**: Eye-friendly dark theme with professional color scheme
- **Accessibility Features**: ARIA-compliant interface elements

### AI Integration
- **Gemini Flash 2.0 Chatbot**: AI-powered assistant on the landing page
- **Intelligent Resource Suggestions**: AI-based recommendations for resource allocation
- **Natural Language Interface**: Interact with the system using natural language

## 🌐 City Coverage

The system currently tracks emergency resources across these 14 Indian cities:

- Delhi
- Mumbai
- Kolkata
- Chennai
- Bangalore
- Hyderabad
- Ahmedabad
- Pune
- Jaipur
- Lucknow
- Chandigarh
- Amritsar
- Varanasi
- Patna

## 💻 Technologies Used

### Frontend
- HTML5
- Tailwind CSS (for responsive design)
- JavaScript
- Leaflet.js (for mapping)
- Font Awesome (for icons)

### Backend
- PHP for server-side processing
- JSON-based data handling
- Email notification scripts

### APIs & External Services
- Gemini Flash 2.0 API (for AI chatbot)
- OpenStreetMap and ESRI (for mapping)

## 📄 Main Pages

- **Landing Page**: Introduction to the system with AI chatbot
- **Dashboard**: Overview of all emergency resources
- **Incidents**: Incident reporting and management
- **Resources**: Resource tracking and management
- **Ambulance Tracking**: Dedicated ambulance monitoring
- **Fire Truck Map**: Fire truck resource visualization
- **Helicopter Tracking**: Aerial resource management

## 🔧 Setup Instructions

1. Clone the repository
2. Set up a local web server (e.g., XAMPP, WAMP)
3. Place the files in your server's htdocs folder
4. To use the AI chatbot feature, update the Gemini API key in `landing.html`
5. Access the application through `http://localhost/PROJECTRESCUE/`

## 💡 Current Implementations

- **Dashboard**: Real-time vehicle tracking with interactive filtering
- **Incident System**: Complete incident reporting and response workflow
- **Email Notifications**: Automated emails for incident reporting and response
- **Resource Management**: Comprehensive tracking and filtering for all resource types
- **AI Chatbot**: Gemini-powered assistant for website information

## 🛠️ Technical Details

### PHP Scripts
- `report_incident.php`: Handles incident reporting with email notifications
- `respond_to_incident.php`: Manages incident response workflow

### JavaScript Components
- Real-time tracking simulation
- Dynamic marker placement
- Interactive filtering system
- Map view toggling

### Data Structure
The system uses JSON-based data for:
- Emergency vehicle information
- Incident details
- City coordinates
- Resource statuses

## 🔒 Security Features
- Input validation
- Cross-site scripting prevention
- Secure email handling
- Data sanitization

## 📦 Included Assets
- Vehicle images for ambulances, fire trucks, and helicopters
- Interface icons and graphics
- City coordinate data
- Sample incident scenarios

## 🔜 Planned Enhancements
- Actual real-time data synchronization
- Advanced geolocation features
- Cross-browser compatibility testing
- Unit and integration tests
- Performance optimization for large datasets
- Enhanced error handling mechanisms

## 📧 Contact Information
- Primary Email: ankanbasu1234@gmail.com
- Sender Email: ankanbasu10@gmail.com

## 🆕 Recent Enhancements & Libraries

### Frontend
**Languages & Frameworks:**
- **HTML5** — Structure of all pages
- **CSS3 / Tailwind CSS** — Styling (Tailwind via CDN)
- **JavaScript (ES6+)** — Client-side scripting
- **jQuery** — For DOM manipulation and animations (via CDN)

**UI Libraries:**
- **Font Awesome** — Icon library (via CDN)
- **Google Fonts (Poppins)** — Custom font

**Components/Features:**
- **Image Gallery** — Custom with jQuery for modal, animation, and navigation
- **Responsive Design** — Tailwind utility classes
- **Glassmorphism Effects** — Custom CSS
- **Chatbot UI** — Custom chat interface

### 🌍 Maps & Geospatial
**Libraries:**
- **Leaflet.js** — Interactive maps (via CDN)
- **OpenStreetMap** — Default map tile provider
- **ESRI World Imagery** — Satellite map tiles
- **OpenTopoMap** — Topographic map tiles (used for “3D” view alternative)

**Map Features:**
- Custom markers for incidents/resources
- Dynamic layer switching (Standard, Satellite, Topo/3D)
- Marker popups and clustering

### 🗄️ Backend
**Languages:**
- **PHP** — Server-side scripting for all backend logic

**Backend Features:**
- Incident Reporting — php/report_incident.php
- Incident Response — php/respond_to_incident.php
- Agency/Network Management — php/get_agencies.php, php/get_network.php, etc.
- Contact Form Email — php/send_email.php
- Session Management — php/check_session.php

**Database:**
- Likely MySQL/MariaDB (typical for XAMPP stack, though not directly shown in code snippets)

### 📡 APIs & Integrations
**Map APIs:**
- OpenStreetMap (tile layer URLs)
- ESRI ArcGIS World Imagery (tile layer URLs)
- OpenTopoMap (tile layer URLs)

**Other APIs:**
- Gemini Flash 2.0 API (AI chatbot integration, as seen in landing.html)
- Mailto: fallback for email sending in offline mode

### ⚙️ Development Tools
- XAMPP (local server stack: Apache, MySQL, PHP, Perl)
- Modern Browser (for ES6, CSS3, and map features)

### 📦 Asset Management
- Images — Stored in assets/images/ and used throughout gallery, hero, testimonials, etc.
- Custom JS/CSS — Some in separate files (e.g., js/config.js), some inline

### 🔗 External Services (CDNs)
- Tailwind CSS — [https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)
- Font Awesome — [https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css](https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css)
- Google Fonts — [https://fonts.googleapis.com/css2?family=Poppins...](https://fonts.googleapis.com/css2?family=Poppins...)
- jQuery — [https://code.jquery.com/jquery-3.6.0.min.js](https://code.jquery.com/jquery-3.6.0.min.js)
- Leaflet.js — [https://unpkg.com/leaflet@1.9.4/dist/leaflet.js](https://unpkg.com/leaflet@1.9.4/dist/leaflet.js) and CSS

---

© 2025 RESCUE Agency Coordination System | All Rights Reserved
