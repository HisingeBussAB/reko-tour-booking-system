# System for managing travel bookings and clients.

## 2.0

This system uses a React/Redux front end and a PHP api to manage bookings, budgets and customer information for a small tour agency.

The first implementation of this will be designed for one simulatinious user and not use Websockets. This is because the server architecture is unknown and can change frequently. However I have strifed to build the app in such a way that a seperate websocket server can be added wiuthout to much hasle which could be running seperatly from the api and used to have the users tell eachother when to update from the api.
