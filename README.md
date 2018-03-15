# System for managing travel bookings and clients.

## 2.0

This system uses a React/Redux front end and a PHP API to manage bookings, budgets and customer information for a small tour agency.

The server architecture for this project is restictive. It needs to be deployable to any basic IIS server so instead of using Websockets
I have used a work-around with Firebase to provide a form of push notifications for multiple users of the system. 
When something data is changed in the back-end a record is created in Firebase that all clients subscribe to, which will trigger an update of the redux state from the main API.
