# Smart AI Hospital Management System 

A modern and intelligent Hospital Management System built using PHP, MySQL, HTML, CSS, JavaScript, Bootstrap, Node.js, and Express.js.

The system helps streamline hospital operations by managing appointments, patient records, doctor schedules, prescriptions, real-time queue tracking, WhatsApp notifications, and an AI-powered symptom checker.

## Features

- Online patient registration and login
- Appointment booking and management
- Live patient queue tracking
- Doctor dashboard
- Admin dashboard
- Patient records management
- Digital prescription management
- Doctor availability scheduling
- Smart delay notifications
- Emergency leave management with appointment rescheduling
- QR code-based check-in integration
- WhatsApp appointment booking notifications
- WhatsApp queue delay alerts
- AI-powered symptom checker and department recommendation
- Responsive design for mobile, tablet, and desktop

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap
- Node.js
- Express.js

## Requirements

- XAMPP / WAMP
- PHP 7.4+ or PHP 8.x
- MySQL / MariaDB
- Node.js
- npm
- Modern Web Browser

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/kabila15/Smart-AI-Hospital-Management-System.git
```

2. Move the Project to XAMPP

Move the project folder into:

C:\xampp\htdocs\
3. Start XAMPP

Open the XAMPP Control Panel and start:

Apache
MySQL
4. Create the Database

Open phpMyAdmin in your browser:

http://localhost/phpmyadmin

Create a new database named:

myhmsdb
5. Import the Database
Select the myhmsdb database.
Click the Import tab.
Select the myhmsdb.sql file included in the project.
Click Import.
6. Install Node.js Dependencies

Open the terminal inside the project folder and run:

npm install
7. Start the WhatsApp Notification Server

Run:

node whatsapp-chatbot.js

Follow the instructions in the terminal and scan the WhatsApp QR code if required.

8. Run the Application

Open your browser and visit:

http://localhost/MABS/Hospital-Management-System-master/
Project Modules
Patient Portal
Patient registration and login
Online appointment booking
Appointment history
Appointment cancellation
Live queue tracking
Prescription viewing
QR code-based appointment check-in
Doctor Portal
Doctor appointment management
Patient prescription management
Weekly availability scheduling
Smart delay notifications
Emergency leave management
Automatic appointment rescheduling
Admin Portal
Doctor management
Patient management
Appointment management
Doctor schedule configuration
Block date management
Patient records management
QR code scanning integration
AI Symptom Checker
Patients can enter their symptoms
The AI analyzes the provided symptoms
Follow-up questions can be generated based on symptoms
The system recommends an appropriate medical department
WhatsApp Notification System

The system provides automated WhatsApp notifications for:

Appointment booking confirmation
Queue status updates
Doctor delay notifications
Appointment-related alerts
Database

The project includes:

myhmsdb.sql

This file contains the database structure and required tables.

To set up the database:

Create a database named myhmsdb.
Import the myhmsdb.sql file using phpMyAdmin.
Key Highlights
Real-time patient queue tracking
AI-powered symptom analysis
WhatsApp notification integration
Smart doctor delay management
Emergency leave handling
Automatic appointment rescheduling
QR code-based patient check-in
Responsive user interface
Future Improvements
Cloud deployment
Video consultation
Online payment integration
Advanced analytics dashboard
Mobile application
Multi-hospital support
Author

Kabila

Final Year Computer Science and Engineering Student

License

This project is created for educational and academic purposes.