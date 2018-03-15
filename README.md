# System for managing travel bookings and clients.

## 2.0

This system uses a React/Redux front end and a PHP api to manage bookings, budgets and customer information for a small tour agency.

The first implementation of this will be designed for one simulatinious user and not use Websockets. This is because the server architecture is unknown and can change frequently. However I have strifed to build the app in such a way that a seperate websocket server can be added without too much hassle, which could be running seperatly from the api. This would assist by having the users sending what data they changed to the socket server which then pushes an instruction to update from main api to all other clients.
