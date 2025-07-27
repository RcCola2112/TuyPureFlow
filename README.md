TUY PUREFLOW: DIGITAL PLATFORM FOR PURIFIED WATER DELIVERY AND DEMAND ANALYTICS CONNECTING DISTRIBUTORS AND CONSUMERS

Members:

Alcaraz, Vince Albert D. Ferrer, Christel Jhoy E. Roxas, John Rc Denver M.

Introduction/Summary

Purpose:

The purpose of this study is to create a system called Tuy PureFlow, which helps make the ordering and delivery of purified water easier and faster. The system includes a mobile application for customers and a web application for distributors and administrators. Through the mobile app, customers can place orders, track their deliveries, and receive updates. On the web app, distributors can manage orders, assign deliveries, check inventory, and view sales reports. This project aims to replace manual processes like phone calls or handwritten records with a more organized and digital way of handling water delivery services, making the process more convenient for both consumers and water distributors in Tuy, Batangas.

Scope:

This study focuses on the development and implementation of Tuy PureFlow, a digital platform composed of a web application and a mobile application designed to improve the order management, delivery tracking, and demand analytics of purified water services in Tuy, Batangas. The web application is intended for use by distributors, delivery personnel, and system administrators to manage customer orders, assign deliveries, monitor inventory, analyze demand trends, and generate reports. On the other hand, the mobile application is developed specifically for consumers, allowing them to place, cancel, or reschedule orders, track deliveries in real time, and receive automated updates through SMS and push notifications. The system also includes a demand analytics dashboard, secure user authentication with role-based access, and a feedback module for reporting issues and suggestions. The mobile application ensures accessibility and convenience for consumers using Android devices. While the system is designed for scalability, this study will focus on its deployment, testing, and evaluation within Tuy, Batangas, using survey questionnaires and usability testing from selected consumers and distributors in the area.

Definitions, acronyms and abbreviations

Agile Software Development – Refers to the iterative development methodology used in this study to build the Tuy PureFlow platform. It involves continuous testing, user feedback integration, and deployment of system modules throughout the project’s timeline.

Cloud Computing – The study utilizes cloud computing to host the Tuy PureFlow system on a scalable infrastructure, allowing access to the platform from any device via the internet and enabling real-time data synchronization.

Data Management System – In this research, this term pertains to the centralized MySQL-based database that stores customer records, order transactions, delivery logs, and feedback reports used for system operations.

Data-driven Intelligence – Refers to the use of analytical tools within Tuy PureFlow to process historical and real-time data for generating insights on demand trends, delivery routes, and inventory management.

Delivery Scheduling – Operationally defined as the platform’s feature that allows distributors to automate and optimize delivery timing and sequences using route algorithms based on customer locations.

Descriptive Statistics – The method applied in analyzing survey results in this study. It includes calculating frequencies, means, and percentage distributions to evaluate user responses on system usability, accuracy, and satisfaction.

Inventory Management – Describes the system’s capability to monitor stock levels in real time, send automated alerts, and assist distributors in restocking decisions to prevent shortages or excess.

Order Management System (OMS) – Refers to the module of Tuy PureFlow that enables consumers to place, modify, or cancel orders and allows distributors to track and process orders efficiently.

Purposive Sampling – The sampling technique used in selecting study participants, where only distributors, consumers, and IT professionals directly engaged in water delivery or system testing were included for targeted feedback.

Real-time Tracking – In the context of this study, this is the GPS-enabled feature of Tuy PureFlow that provides consumers and distributors with live updates on the location and progress of water deliveries.

System Usability Evaluation – Refers to the evaluation process used in this research involving surveys and Likert scales to assess user satisfaction, system effectiveness, and accessibility during testing.

User Authentication and Security – Operationally defined as the system’s use of role-based access control (RBAC), OAuth 2.0, and data encryption to ensure secure logins and restricted access based on user roles (e.g., admin, distributor, customer).

Web-based Platform – Refers to Tuy PureFlow being deployed as an internet-accessible system that does not require installation, enabling users to access the platform through any browser or device.

Overall Description

SYSTEM ARCHITECTURE: 
<img width="904" height="551" alt="image" src="https://github.com/user-attachments/assets/d4bdd813-7ee8-4de1-8b39-b8a45849b769" />

Discuss system architecture:

The Tuy PureFlow system architecture is a hybrid platform combining both web and mobile applications to support purified water delivery services in Tuy, Batangas. It follows a client-server architecture, where the client-side (consumers via mobile app; distributors/admin via web app) communicates with a centralized backend API and database server hosted on the cloud.
There are four user roles interacting with the system:

Consumers use the mobile app to register, place and track orders, receive notifications, and give feedback.

Distributors use the web app to manage orders, monitor inventory, assign deliveries, and analyze demand.

Delivery Personnel access assigned deliveries and update order statuses via the web interface.

Administrators configure system settings, manage users, and generate reports through the web platform.

Software perspective and functions:

Software Perspective and System Functions
From a software perspective, Tuy PureFlow is a multi-tiered application with distinct modules:

Frontend (Mobile App – React Native): Enables consumers to register, place orders, view order history, track deliveries in real time, and provide feedback.

Frontend (Web App – React.js): Accessible by distributors and administrators for order processing, inventory management, route assignment, user management, and analytics visualization.

Backend (Node.js + Express): Manages business logic, handles API requests, connects to third-party services, and ensures secure data transactions between the frontend and the database.

Database (MySQL): Stores user information, order records, inventory status, delivery history, and feedback.

Key System Functions: Real-time order placement and tracking

    Delivery scheduling and route optimization
	
    Inventory and demand monitoring
	
    User account and security management
	
    Notifications and alerts via SMS and push
	
    Feedback and support system

Add use case characteristics and other diagrams

USE CASE:

<img width="904" height="814" alt="image" src="https://github.com/user-attachments/assets/7779fd59-7a00-421a-af93-776fe308167a" />

CONTEXT DIAGRAM:

<img width="904" height="553" alt="image" src="https://github.com/user-attachments/assets/26c4dc7d-6770-4887-887f-f4ad90520eee" />

DATA FLOW DIAGRAM:

<img width="908" height="590" alt="image" src="https://github.com/user-attachments/assets/1be37ebb-d543-420a-9359-65812639449f" />

Constraints, limitation and dependencies

Constraints:

The system relies on internet connectivity for real-time operations and data synchronization.
  
Only supports Android in its initial release; iOS version will be developed later.

Dependent on the availability of third-party APIs (e.g., Google Maps, Zoho, OneSignal) which may impose usage limits or require fees.

Limitations:

Deployment and evaluation are limited to Tuy, Batangas; findings may not generalize to other locations.
		
The mobile app requires modern smartphones; outdated devices may experience performance issues.
		
Notifications may fail if users disable app permissions or have unstable mobile networks.

Dependencies:

Zoho Inventory API for real-time inventory alerts and stock monitoring.
		
Google Cloud Run for hosting backend services.
		
MySQL for relational database management.
		
Auth0 for secure OAuth 2.0-based authentication.
		
Agile methodology for iterative development and continuous feedback integration.

Specific Requirements

System Features
The Tuy PureFlow system includes the following main features:

User Registration and Login- Users (customers, distributors, and admins) can create an account and log in securely.
	
Order Management- Customers can place, modify, cancel, or reschedule water delivery orders.
	
Delivery Tracking- Customers can track their deliveries in real-time using GPS through the mobile app.
	
Inventory Management- Distributors can monitor stock levels and get alerts when items are low.
	
Delivery Assignment- Distributors can assign delivery staff to orders and update delivery statuses.
	
Notifications and Alerts- Users receive updates through SMS and push notifications.
	
Feedback and Reporting- Customers can give feedback or report issues directly through the app.
	
Sales and Demand Analytics- Distributors and admins can view reports about sales, popular products, and delivery performance.
	
Admin Dashboard- Admins can manage users and monitor activity.

Interface Requirements

Mobile Application Interface (For Customers Simple and clean layout

Buttons for placing and tracking orders

Notification section

Feedback form

Responsive design for various phone screen sizes

Web Application Interface (For Distributors/Admins and also consumers)

Dashboard with quick stats (orders, inventory, deliveries)

Tables for viewing and managing orders

Interactive map for delivery tracking

Login and role-based access


Non-Functional Requirements

Performance

System should load pages within 3 seconds on stable internet
   
Mobile app should work smoothly on devices with at least 2GB RAM

Reliability

System should operate 24/7 with minimal downtime
	
Auto-recovery in case of failure (e.g., retry sending notifications)

Security

All user data must be encrypted (SSL/TLS)
	
Passwords stored securely using hashing
	
Role-based access (users only see features they are allowed to use)

Scalability

System should be able to handle more users and deliveries as needed in the future

Usability

Interfaces must be easy to use for both tech-savvy and non-tech users
	
Use icons, tooltips, and simple words

Other Requirements

Internet Connection

Required for both mobile and web systems to work properly

Supported Devices

Mobile app: Android (minimum version 7.0 or above)
   
Web app: Compatible with major browsers (Chrome, Firefox, Edge)

Hosting and Server

Uses Google Cloud Run or Hostinger for web hosting
   
Database hosted on a secure MySQL server

Third-party Services

  Google Maps API for delivery tracking and route guidance

  OneSignal for push notifications

  Zoho Inventory for managing stock

  Auth0 for secure login and user authentication




